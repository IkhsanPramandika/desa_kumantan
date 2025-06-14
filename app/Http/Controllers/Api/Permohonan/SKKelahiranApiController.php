<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKKelahiran;
use App\Http\Requests\Api\Permohonan\sk_kelahiran\StoreSKKelahiranRequest;
use App\Http\Resources\Permohonan\sk_kelahiran\PermohonanSKKelahiranResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User; 
use App\Notifications\PermohonanBaru;
use Illuminate\Support\Facades\Notification;

class SKKelahiranApiController extends Controller 
{
    protected $attachmentBaseDir = 'permohonan_sk_kelahiran_attachments';

    public function store(StoreSKKelahiranRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $dbData = $validatedData;
        
        $user = $request->user('sanctum');
        $dbData['masyarakat_id'] = $user->id; 
        $dbData['status'] = 'pending';

        $fileFields = ['file_kk', 'file_ktp', 'surat_pengantar_rt_rw', 'surat_nikah_orangtua', 'surat_keterangan_kelahiran'];
        
        // Inisialisasi array untuk melacak file yang diunggah
        $uploadedFilePaths = [];

        foreach ($fileFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $path = $request->file($fileField)->store($this->attachmentBaseDir . '/' . $fileField, 'public');
                $dbData[$fileField] = $path;
                $uploadedFilePaths[$fileField] = $path; // Simpan path untuk rollback
            }
        }
        
        try {
            $permohonan = PermohonanSKKelahiran::create($dbData);
            
            // ====================================================================
            // [TAMBAHAN] Mengirim Notifikasi Universal
            // ====================================================================
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();

                if ($semuaPetugas->isNotEmpty()) {
                    $jenisSurat = "SK Kelahiran";
                    $routeName = "petugas.permohonan-sk-kelahiran.show"; 

                    Notification::send($semuaPetugas, new PermohonanBaru($permohonan, $jenisSurat, $routeName));
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi untuk SK Kelahiran: ' . $e->getMessage());
            }
            // ====================================================================

            return (new PermohonanSKKelahiranResource($permohonan))
                    ->additional(['message' => 'Permohonan SK Kelahiran berhasil diajukan.'])
                    ->response()
                    ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[SK Kelahiran API - Store] Gagal menyimpan: ' . $e->getMessage());
            // Rollback file yang sudah diunggah jika penyimpanan DB gagal
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
        $permohonan = PermohonanSKKelahiran::where('masyarakat_id', $user->id)
                                ->latest()
                                ->paginate(10);
        
        return PermohonanSKKelahiranResource::collection($permohonan)
                                 ->additional(['message' => 'Daftar permohonan SK Kelahiran berhasil diambil.'])
                                 ->response(); 
    }

    public function show(Request $request, $id): JsonResponse 
    {
        $user = $request->user('sanctum');
        $permohonan = PermohonanSKKelahiran::where('masyarakat_id', $user->id)
                                      ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan SK Kelahiran tidak ditemukan atau Anda tidak berhak mengaksesnya.'], 404);
        }
            
        return (new PermohonanSKKelahiranResource($permohonan))
                       ->additional(['message' => 'Detail permohonan SK Kelahiran berhasil diambil.'])
                       ->response(); 
    }

    public function downloadHasil(Request $request, $id): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user('sanctum');
        $permohonan = PermohonanSKKelahiran::where('masyarakat_id', $user->id)
                                      ->where('status', 'selesai') 
                                      ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan, belum selesai, atau Anda tidak berhak mengaksesnya.'], 404);
        }

        if ($permohonan->file_hasil_akhir) {
            $path = Str::replaceFirst('/storage/', '', parse_url($permohonan->file_hasil_akhir, PHP_URL_PATH));
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->download($path);
            }
        }
        
        return response()->json(['message' => 'File hasil akhir tidak tersedia.'], 404);
    }
}
