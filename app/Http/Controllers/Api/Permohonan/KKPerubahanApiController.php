<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\kk_perubahan\StoreKKPerubahanDataRequest;
use App\Http\Resources\Permohonan\kk_perubahan\PermohonanKKPerubahanDataResource;
use App\Models\PermohonanKKPerubahanData;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KKPerubahanApiController extends Controller 
{
    /**
     * Menyimpan permohonan baru dari aplikasi mobile.
     */
    public function store(StoreKKPerubahanDataRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // Menambahkan logika untuk menangani upload file lampiran
            // Asumsi field file berdasarkan standar permohonan perubahan data. Sesuaikan jika perlu.
            $fileFields = ['file_kk', 'file_ktp', 'surat_pengantar_rt_rw','surat_keterangan_pendukung'];
            $basePath = 'permohonan_kk_perubahan_data/lampiran';

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

            $permohonan = PermohonanKKPerubahanData::create($dbData);

            // ====================================================================
            // [TAMBAHAN] Mengirim Notifikasi Universal ke Petugas
            // ====================================================================
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();

                if ($semuaPetugas->isNotEmpty()) {
                    // PERBAIKAN: Sesuaikan parameter untuk Permohonan Perubahan Data KK
                    $jenisSurat = "Perubahan Data KK";
                    $routeName = "petugas.permohonan-kk-perubahan.show"; // Nama route disesuaikan

                    Notification::send($semuaPetugas, new PermohonanBaru($permohonan, $jenisSurat, $routeName));
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi untuk Perubahan KK: ' . $e->getMessage());
            }
            // ====================================================================
            
            return (new PermohonanKKPerubahanDataResource($permohonan))
                ->additional(['message' => 'Permohonan Perubahan Data KK berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API Perubahan KK - Store] Gagal menyimpan: ' . $e->getMessage());
            foreach ($uploadedFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            return response()->json(['message' => 'Gagal menyimpan permohonan.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan daftar permohonan milik pengguna yang terotentikasi.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonanKKPerubahanData::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanKKPerubahanDataResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan KK Perubahan Data berhasil diambil.'])
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

        $permohonan = PermohonanKKPerubahanData::where('masyarakat_id', $user->id)
            ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan KK Perubahan Data tidak ditemukan atau Anda tidak berhak mengaksesnya.'], 404);
        }
            
        return (new PermohonanKKPerubahanDataResource($permohonan))
            ->additional(['message' => 'Detail permohonan KK Perubahan Data berhasil diambil.'])
            ->response(); 
    }

    /**
     * Mengunduh file hasil akhir untuk pengguna yang terotentikasi.
     */
    public function downloadHasil(Request $request, $id)
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonanKKPerubahanData::where('masyarakat_id', $user->id)
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
