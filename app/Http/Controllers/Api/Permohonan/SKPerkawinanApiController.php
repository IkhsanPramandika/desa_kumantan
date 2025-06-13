<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKPerkawinan;
use App\Http\Requests\Api\Permohonan\sk_perkawinan\StoreSKPerkawinanRequest;
use App\Http\Resources\Permohonan\sk_perkawinan\PermohonanSKPerkawinanResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SKPerkawinanApiController extends Controller
{
    /**
     * Menampilkan daftar permohonan milik pengguna yang terotentikasi.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $permohonan = PermohonanSKPerkawinan::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);

        return PermohonanSKPerkawinanResource::collection($permohonan);
    }

    /**
     * Menyimpan permohonan baru dari aplikasi mobile.
     */
    public function store(StoreSKPerkawinanRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // Proses upload file
            $fileFields = [
                'file_kk', 'file_ktp_mempelai', 'surat_nikah_orang_tua', 
                'kartu_imunisasi_catin', 'sertifikat_elsimil', 'akta_penceraian'
            ];
            $basePath = 'permohonan_sk_perkawinan/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($basePath, 'public');
                    $dbData[$field] = $path;
                }
            }

            $permohonan = PermohonanSKPerkawinan::create($dbData);
            
            return (new PermohonanSKPerkawinanResource($permohonan))
                ->additional(['message' => 'Permohonan SK Perkawinan berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SK Perkawinan - Store] Gagal menyimpan: ' . $e->getMessage());
            if (isset($dbData) && is_array($dbData)) {
                foreach ($fileFields as $field) {
                    if (!empty($dbData[$field]) && Storage::disk('public')->exists($dbData[$field])) {
                        Storage::disk('public')->delete($dbData[$field]);
                    }
                }
            }
            return response()->json(['message' => 'Gagal menyimpan permohonan.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan detail satu permohonan.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $permohonan = PermohonanSKPerkawinan::where('id', $id)
            ->where('masyarakat_id', $user->id)
            ->firstOrFail();
            
        return new PermohonanSKPerkawinanResource($permohonan);
    }

    /**
     * Mengunduh file hasil akhir untuk pengguna yang terotentikasi.
     */
    public function downloadHasil(Request $request, $id)
    {
        $user = $request->user();
        $permohonan = PermohonanSKPerkawinan::where('id', $id)
            ->where('masyarakat_id', $user->id)
            ->where('status', 'selesai')
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Dokumen tidak ditemukan atau belum selesai.'], 404);
        }

        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }

        return response()->json(['message' => 'File fisik tidak ditemukan di server.'], 404);
    }
}
