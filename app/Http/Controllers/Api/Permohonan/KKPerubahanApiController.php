<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonananKKPerubahanData; // Pastikan nama model benar
use App\Http\Requests\Api\Permohonan\kk_perubahan\StoreKkPerubahanDataRequest; 
use App\Http\Resources\Permohonan\kk_perubahan\PermohonananKKPerubahanDataResource;
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

    public function store(StoreKkPerubahanDataRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        Log::info('[KK Perubahan Data API - Store] Validasi berhasil.', $validatedData);

        $dbData = $validatedData;
        
        $user = $request->user('sanctum');
        if (!$user) {
            Log::error('[KK Perubahan Data API - Store] Tidak ada pengguna terautentikasi via Sanctum.');
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }
        $dbData['masyarakat_id'] = $user->id; 
        $dbData['status'] = 'pending';

        // Ganti 'catatan' dengan 'catatan_pemohon' jika Anda mengubahnya di FormRequest dan Model
        if (isset($validatedData['catatan'])) {
             $dbData['catatan_pemohon'] = $validatedData['catatan'];
             unset($dbData['catatan']); // Hapus 'catatan' agar tidak konflik jika nama kolom di DB adalah 'catatan_pemohon'
        }


        $fileFields = [
            'file_kk', 
            'file_ktp', 
            'surat_pengantar_rt_rw', 
            'surat_keterangan_pendukung'
        ];
        
        $rulesForStore = (new StoreKkPerubahanDataRequest())->rules();

        foreach ($fileFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $path = $request->file($fileField)->store($this->attachmentBaseDir . '/' . $fileField, 'public');
                $dbData[$fileField] = $path;
                Log::info('[KK Perubahan Data API - Store] File ' . $fileField . ' disimpan ke: ' . $path);
            } else {
                if (isset($rulesForStore[$fileField]) && 
                    ((is_string($rulesForStore[$fileField]) && str_contains($rulesForStore[$fileField], 'nullable')) ||
                     (is_array($rulesForStore[$fileField]) && in_array('nullable', $rulesForStore[$fileField])))
                ) {
                    if (!array_key_exists($fileField, $dbData)) { 
                        $dbData[$fileField] = null;
                    }
                }
            }
        }
        
        try {
            $permohonan = PermohonananKKPerubahanData::create($dbData);
            Log::info('[KK Perubahan Data API - Store] Permohonan berhasil dibuat dengan ID: ' . $permohonan->id);

            return (new PermohonananKKPerubahanDataResource($permohonan))
                    ->additional(['message' => 'Permohonan KK Perubahan Data berhasil diajukan.'])
                    ->response()
                    ->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error('[KK Perubahan Data API - Store] Gagal menyimpan permohonan: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            foreach ($fileFields as $fileField) {
                if (isset($dbData[$fileField]) && $dbData[$fileField] && Storage::disk('public')->exists($dbData[$fileField])) {
                    Storage::disk('public')->delete($dbData[$fileField]);
                    Log::info('[KK Perubahan Data API - Store] Rollback: File ' . $fileField . ' dihapus: ' . $dbData[$fileField]);
                }
            }
            return response()->json(['message' => 'Gagal menyimpan permohonan KK Perubahan Data.', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonananKKPerubahanData::where('masyarakat_id', $user->id)
                                ->latest()
                                ->paginate(10);
        
        return PermohonananKKPerubahanDataResource::collection($permohonan)
                                 ->additional(['message' => 'Daftar permohonan KK Perubahan Data berhasil diambil.'])
                                 ->response(); 
    }

    public function show(Request $request, $id): JsonResponse 
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonananKKPerubahanData::where('masyarakat_id', $user->id)
                                      ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan KK Perubahan Data tidak ditemukan atau Anda tidak berhak mengaksesnya.'], 404);
        }
            
        return (new PermohonananKKPerubahanDataResource($permohonan))
                       ->additional(['message' => 'Detail permohonan KK Perubahan Data berhasil diambil.'])
                       ->response(); 
    }

    public function downloadHasil(Request $request, $id): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonananKKPerubahanData::where('masyarakat_id', $user->id)
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
