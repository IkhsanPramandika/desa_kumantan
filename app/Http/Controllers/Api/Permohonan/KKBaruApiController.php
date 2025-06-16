<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\kk_baru\StoreKKBaruRequest;
use App\Http\Resources\Permohonan\kk_baru\PermohonanKKBaruResource;
use App\Models\PermohonanKKBaru;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KKBaruApiController extends Controller 
{
    /**
     * Menyimpan permohonan baru dari aplikasi mobile.
     */
    public function store(StoreKKBaruRequest $request)
    {
        Log::info('Data Masuk dari Request KK Baru:', $request->all());
        
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;

            // ====================================================================
            // PERBAIKAN: Mapping 'catatan' dari mobile ke 'catatan_pemohon' di DB
            // ====================================================================
            // Kita perlu mengambilnya dari $request, bukan $validatedData, karena namanya berbeda.
            if ($request->has('catatan')) {
                $dbData['catatan_pemohon'] = $request->input('catatan');
            }
            // ====================================================================

            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // Logika untuk menangani upload file lampiran
            $fileFields = ['surat_pengantar_rt_rw', 'kk_lama', 'file_ktp', 'buku_nikah_akta_cerai', 'surat_pindah_datang', 'ijazah_terakhir'];
            $basePath = 'permohonan_kk_baru/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $filePath = $basePath . '/' . $fileName;
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    $dbData[$field] = $filePath; 
                    $uploadedFilePaths[] = $filePath;
                }
            }
            
            Log::info('Data yang akan dibuat untuk PermohonanKKBaru:', $dbData);

            $permohonan = PermohonanKKBaru::create($dbData);

            // Mengirim notifikasi ke petugas
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();
                if ($semuaPetugas->isNotEmpty()) {
                    $jenisSurat = "KK Baru";
                    $routeName = "petugas.permohonan-kk-baru.show";
                    Notification::send($semuaPetugas, new PermohonanBaru($permohonan, $jenisSurat, $routeName));
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi untuk KK Baru: ' . $e->getMessage());
            }

            return (new PermohonanKKBaruResource($permohonan))
                ->additional(['message' => 'Permohonan KK Baru berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API KK Baru - Store] Gagal menyimpan: ' . $e->getMessage());
            // Hapus file yang sudah terlanjur di-upload jika terjadi error
            foreach ($uploadedFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            return response()->json(['message' => 'Gagal menyimpan permohonan.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan daftar permohonan milik pengguna.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanKKBaruResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan KK Baru berhasil diambil.'])
            ->response(); 
    }

    /**
     * Menampilkan detail satu permohonan.
     */
    public function show(Request $request, $id): JsonResponse 
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
            ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan atau Anda tidak berhak mengaksesnya.'], 404);
        }
            
        return (new PermohonanKKBaruResource($permohonan))
            ->additional(['message' => 'Detail permohonan berhasil diambil.'])
            ->response(); 
    }

    /**
     * Mengunduh file hasil akhir.
     */
    public function downloadHasil(Request $request, $id): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
            ->where('status', 'selesai')
            ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan, belum selesai, atau Anda tidak berhak mengaksesnya.'], 404);
        }

        if ($permohonan->file_hasil_akhir) {
            $path = $permohonan->file_hasil_akhir;
            
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->download($path);
            }
        }
        
        return response()->json(['message' => 'File hasil akhir tidak tersedia untuk permohonan ini.'], 404);
    }
}
