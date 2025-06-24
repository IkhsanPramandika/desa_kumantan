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
    $user = $request->user(); // Ini adalah objek Masyarakat yang login
    $uploadedFilePaths = [];

    try {
        // [PERBAIKAN] Kita hanya akan mengambil data yang ada kolomnya di tabel permohonan.
        // Data pribadi seperti 'nama_pemohon', 'nik_pemohon' dari form kita abaikan
        // karena sudah ada di data user yang login.
        $dbData = [
            'masyarakat_id' => $user->id,
            'status' => 'pending',
            'catatan_pemohon' => $validatedData['catatan_pemohon'] ?? null,
        ];

        $fileFields = ['file_kk', 'file_ktp', 'surat_pengantar_rt_rw', 'surat_keterangan_pendukung'];
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

        // Sekarang $dbData hanya berisi data yang aman untuk disimpan
        $permohonan = PermohonanKKPerubahanData::create($dbData);

        // Logika notifikasi Anda sudah benar dan bisa tetap seperti ini
        try {
            $semuaPetugas = User::where('role', 'petugas')->get();
            if ($semuaPetugas->isNotEmpty()) {
                $title = $permohonan->getJudulNotifikasi();
                // Kita ambil nama dari user yang login, bukan dari form request
                $message = 'Ada ' . $title . ' baru dari ' . $user->nama_lengkap;
                $url = $permohonan->getRouteTujuan();
                $permohonanId = $permohonan->getId();

                $notification = (new PermohonanBaru($title, $message, $url, $permohonanId))->afterCommit();
                Notification::send($semuaPetugas, $notification);
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi untuk ' . get_class($permohonan) . ': ' . $e->getMessage());
        }

        return (new PermohonanKKPerubahanDataResource($permohonan))
            ->additional(['message' => 'Permohonan berhasil diajukan.'])
            ->response()->setStatusCode(201);

    } catch (\Exception $e) {
        Log::error('[API Perubahan KK - Store] Gagal menyimpan: ' . $e->getMessage());
        // ... logika cleanup file ...
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
                                             ->where('id', $id) // <-- Gunakan where()
                                             ->first();         // <-- dan first()

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan atau Anda tidak berhak mengaksesnya.'], 404);
        }
            
        return (new PermohonanKKPerubahanDataResource($permohonan))
            ->additional(['message' => 'Detail permohonan berhasil diambil.'])
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
        ->where('id', $id) // <-- Gunakan where()
        ->first();         // <-- dan first()
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
