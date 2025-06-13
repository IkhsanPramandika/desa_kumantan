<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKDomisili;
use App\Http\Requests\Api\Permohonan\sk_domisili\StoreSkDomisiliRequest;
use App\Http\Resources\Permohonan\sk_domisili\PermohonanSKDomisiliResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Events\PermohonanMasuk; // <-- 'use' statement ini sudah benar

class SKDomisiliApiController extends Controller
{
    /**
     * Menampilkan daftar permohonan milik pengguna yang terotentikasi.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $permohonan = PermohonanSKDomisili::where('masyarakat_id', $user->id)
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
        $uploadedFilePaths = []; // Untuk menyimpan path file yang diupload

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
                    $uploadedFilePaths[] = $path; // Simpan path untuk rollback jika gagal
                }
            }

            $permohonan = PermohonanSKDomisili::create($dbData);

            // ====================================================================
            // [KODE TAMBAHAN] MEMANGGIL EVENT UNTUK NOTIFIKASI REAL-TIME
            // ====================================================================
            try {
                $dataNotifikasi = [
                    'jenis_surat' => 'SK Domisili',
                    'nama_pemohon' => $permohonan->nama_pemohon_atau_lembaga,
                    'waktu' => now()->diffForHumans(),
                    'icon' => 'fas fa-home text-white', // Icon yang relevan dengan domisili
                    'bg_color' => 'bg-primary',         // Warna latar ikon
                    'url' => route('petugas.permohonan-sk-domisili.show', $permohonan->id)
                ];
                // Mengirim event ke antrian (queue)
                event(new PermohonanMasuk($dataNotifikasi));
            } catch (\Exception $e) {
                // Jika pengiriman notifikasi gagal, jangan gagalkan seluruh proses.
                // Cukup catat errornya saja.
                Log::error('Gagal mengirim event notifikasi SK Domisili: ' . $e->getMessage());
            }
            // ====================================================================
            
            return (new PermohonanSKDomisiliResource($permohonan))
                ->additional(['message' => 'Permohonan SK Domisili berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SK Domisili - Store] Gagal menyimpan: ' . $e->getMessage());
            // Rollback file yang sudah terupload jika ada error DB
            foreach ($uploadedFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
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
        $permohonan = PermohonanSKDomisili::where('id', $id)
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
        $permohonan = PermohonanSKDomisili::where('id', $id)
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