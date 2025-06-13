<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKTidakMampu;
use App\Http\Requests\Api\Permohonan\sk_tidak_mampu\StoreSKTidakMampuRequest;
use App\Http\Resources\Permohonan\sk_tidak_mampu\PermohonanSKTidakMampuResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SKTidakMampuApiController extends Controller
{
    /**
     * Menampilkan daftar permohonan milik pengguna yang terotentikasi.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $permohonan = PermohonanSKTidakMampu::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);

        return PermohonanSKTidakMampuResource::collection($permohonan);
    }

    /**
     * Menyimpan permohonan baru dari aplikasi mobile.
     */
    public function store(StoreSKTidakMampuRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // Proses upload file
            $fileFields = ['file_kk', 'file_ktp', 'file_pendukung_lain'];
            $basePath = 'permohonan_sk_tidak_mampu/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($basePath, 'public');
                    $dbData[$field] = $path;
                }
            }

            $permohonan = PermohonanSKTidakMampu::create($dbData);
            
            return (new PermohonanSKTidakMampuResource($permohonan))
                ->additional(['message' => 'Permohonan SK Tidak Mampu berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SKTM - Store] Gagal menyimpan: ' . $e->getMessage());
            // Rollback file yang sudah terupload jika ada error DB
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
        $permohonan = PermohonanSKTidakMampu::where('id', $id)
            ->where('masyarakat_id', $user->id)
            ->firstOrFail();
            
        return new PermohonanSKTidakMampuResource($permohonan);
    }

    /**
     * Mengunduh file hasil akhir untuk pengguna yang terotentikasi.
     */
    public function downloadHasil(Request $request, $id)
    {
        $user = $request->user();
        $permohonan = PermohonanSKTidakMampu::where('id', $id)
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
