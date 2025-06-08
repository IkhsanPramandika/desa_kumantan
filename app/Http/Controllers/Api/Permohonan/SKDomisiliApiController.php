<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonananSKDomisili;
use App\Http\Requests\Api\Permohonan\sk_domisili\StoreSkDomisiliRequest;
use App\Http\Resources\Permohonan\sk_domisili\PermohonanSKDomisiliResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SKDomisiliApiController extends Controller
{
    /**
     * Menampilkan daftar permohonan milik pengguna yang terotentikasi.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $permohonan = PermohonananSKDomisili::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);

        return PermohonanSKDomisiliResource::collection($permohonan);
    }

    /**
     * Menyimpan permohonan baru dari aplikasi mobile.
     */
    public function store(StoreSkDomisiliRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // Proses upload file
            $fileFields = ['file_kk', 'file_ktp', 'file_surat_pengantar_rt_rw'];
            $basePath = 'permohonan_sk_domisili/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($basePath, 'public');
                    $dbData[$field] = $path;
                }
            }

            $permohonan = PermohonananSKDomisili::create($dbData);
            
            return (new PermohonanSKDomisiliResource($permohonan))
                ->additional(['message' => 'Permohonan SK Domisili berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SK Domisili - Store] Gagal menyimpan: ' . $e->getMessage());
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
        $permohonan = PermohonananSKDomisili::where('id', $id)
            ->where('masyarakat_id', $user->id)
            ->firstOrFail();
            
        return new PermohonanSKDomisiliResource($permohonan);
    }

    /**
     * Mengunduh file hasil akhir untuk pengguna yang terotentikasi.
     */
    public function downloadHasil(Request $request, $id)
    {
        $user = $request->user();
        $permohonan = PermohonananSKDomisili::where('id', $id)
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
