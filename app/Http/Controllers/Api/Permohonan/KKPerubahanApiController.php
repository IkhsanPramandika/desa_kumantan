<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanKKPerubahanData; // Pastikan nama model benar
use App\Http\Requests\Api\Permohonan\kk_perubahan\StoreKKPerubahanDataRequest; 
use App\Http\Resources\Permohonan\kk_perubahan\PermohonanKKPerubahanDataResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class KKPerubahanApiController extends Controller 
{
    protected $attachmentBaseDir = 'permohonan_kk_perubahan_attachments';

    /**
 * Menyimpan permohonan baru dari aplikasi mobile.
 */
    public function store(StoreKKPerubahanDataRequest $request) // Pastikan nama Request-nya sesuai
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // Proses upload file (sesuaikan dengan field Anda)
            $fileFields = ['file_kk_lama', 'file_dokumen_pendukung'];
            $basePath = 'permohonan_kk_perubahan/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($basePath, 'public');
                    $dbData[$field] = $path;
                    $uploadedFilePaths[] = $path;
                }
            }

            $permohonan = PermohonanKKPerubahanDataResource::create($dbData); // Pastikan nama Model-nya sesuai

            // ====================================================================
            // [TAMBAHAN] MEMANGGIL EVENT UNTUK NOTIFIKASI REAL-TIME
            // ====================================================================
            try {
                $dataNotifikasi = [
                    'jenis_surat' => 'Perubahan Data KK',
                    'nama_pemohon' => $permohonan->nama_kepala_keluarga, // Sesuaikan dengan field di model Anda
                    'waktu' => now()->diffForHumans(),
                    'icon' => 'fas fa-edit text-white',
                    'bg_color' => 'bg-info',
                    'url' => route('petugas.permohonan-kk-perubahan-data.show', $permohonan->id) // Pastikan nama route benar
                ];
                event(new \App\Events\PermohonanMasuk($dataNotifikasi));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim event notifikasi Perubahan KK: ' . $e->getMessage());
            }
            // ====================================================================
            
            return (new PermohonanKKPerubahanDataResource($permohonan)) // Pastikan nama Resource sesuai
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

    public function downloadHasil(Request $request, $id): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
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
            $pathRelativeToPublicDisk = '';
            if (Str::startsWith($permohonan->file_hasil_akhir, Storage::url(''))) {
                 $pathRelativeToPublicDisk = substr($permohonan->file_hasil_akhir, strlen(Storage::url('')));
            } else if (Str::startsWith($permohonan->file_hasil_akhir, '/storage/')) { 
                 $pathRelativeToPublicDisk = substr($permohonan->file_hasil_akhir, strlen('/storage/'));
            } else {
                 Log::error('[KK Perubahan Data API - Download] Format file_hasil_akhir tidak dikenal: ' . $permohonan->file_hasil_akhir . ' untuk ID: ' . $id);
                 return response()->json(['message' => 'Format path file hasil akhir tidak dikenal.'], 400);
            }

            if (Storage::disk('public')->exists($pathRelativeToPublicDisk)) {
                Log::info('[KK Perubahan Data API - Download] File ditemukan: ' . $pathRelativeToPublicDisk . ' pada disk public.');
                return Storage::disk('public')->download($pathRelativeToPublicDisk);
            } else {
                Log::error('[KK Perubahan Data API - Download] File tidak ditemukan di disk public: ' . $pathRelativeToPublicDisk . ' untuk ID: ' . $id .'. Path absolut dicek: ' . Storage::disk('public')->path($pathRelativeToPublicDisk));
            }
        }
        
        Log::warning('[KK Perubahan Data API - Download] File hasil akhir tidak tersedia untuk permohonan ID: ' . $id);
        return response()->json(['message' => 'File hasil akhir tidak tersedia untuk permohonan ini.'], 404);
    }
}
