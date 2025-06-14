<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PermohonanBaru;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\PermohonanKKPerubahanData; // Pastikan nama model benar
use App\Http\Requests\Api\Permohonan\kk_perubahan\StoreKKPerubahanDataRequest; 
use App\Http\Resources\Permohonan\kk_perubahan\PermohonanKKPerubahanDataResource;

class KKPerubahanApiController extends Controller 
{
    protected $attachmentBaseDir = 'permohonan_kk_perubahan_attachments';

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

                // ... proses upload file ...

                $permohonan = PermohonanKKPerubahanData::create($dbData);

                // ====================================================================
                // [MODIFIKASI] KIRIM NOTIFIKASI UNIVERSAL
                // ====================================================================
                try {
                    $semuaPetugas = User::where('role', 'petugas')->get();

                    if ($semuaPetugas->isNotEmpty()) {
                        // Membuat instance notifikasi universal dengan parameter yang relevan
                        $jenisSurat = "KK Perubahan";
                        $routeName = "petugas.permohonan-kk-baru.show"; // Sesuaikan dengan nama route Anda di web.php

                        Notification::send($semuaPetugas, new PermohonanBaru($permohonan, $jenisSurat, $routeName));
                    }
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
