<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Events\PermohonanMasuk;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\kk_hilang\StoreKKHilangRequest;
use App\Http\Resources\Permohonan\kk_hilang\PermohonanKKHilangResource; // [PERBAIKAN] Nama disesuaikan
use App\Models\PermohonanKKHilang; // [PERBAIKAN] Nama disesuaikan (tanpa typo 'nan')
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KKHilangApiController extends Controller // [PERBAIKAN] Nama class disesuaikan dengan standar PSR-4
{
    /**
     * Menampilkan daftar permohonan milik pengguna yang terotentikasi.
     */
    public function index(Request $request): JsonResponse
    {
        $permohonan = PermohonanKKHilang::where('masyarakat_id', $request->user()->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanKKHilangResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan KK Hilang berhasil diambil.'])
            ->response();
    }

    /**
     * Menyimpan permohonan baru dari aplikasi mobile.
     */
    public function store(StoreKKHilangRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // ====================================================================
            // [PERBAIKAN] Nama file di sini disesuaikan dengan nama kolom di DB
            // ====================================================================
            $fileFields = ['surat_pengantar_rt_rw', 'surat_keterangan_hilang_kepolisian'];
            $basePath = 'permohonan_kk_hilang/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($basePath, 'public');
                    $dbData[$field] = $path; // Menimpa objek file dengan string path yang benar
                    $uploadedFilePaths[] = $path;
                }
            }

            $permohonan = PermohonanKKHilang::create($dbData);
            
            // "Segarkan" model untuk memastikan data yang dikirim ke Resource adalah data final
            $permohonan->refresh();

            // Memicu event notifikasi
            try {
                $dataNotifikasi = [
                    'jenis_surat' => 'KK Hilang',
                    'nama_pemohon' => $permohonan->nama_pemohon,
                    'waktu' => now()->diffForHumans(),
                    'icon' => 'fas fa-search-minus text-white',
                    'bg_color' => 'bg-warning',
                    'url' => route('petugas.permohonan-kk-hilang.show', $permohonan->id)
                ];
                event(new PermohonanMasuk($dataNotifikasi));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim event notifikasi KK Hilang: ' . $e->getMessage());
            }
            
            return (new PermohonanKKHilangResource($permohonan))
                ->additional(['message' => 'Permohonan KK Hilang berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API KK Hilang - Store] Gagal menyimpan: ' . $e->getMessage());
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
    public function show(Request $request, $id): JsonResponse 
    {
        // [PERBAIKAN] Query dilakukan ke Model, bukan ke Resource
        $permohonan = PermohonanKKHilang::where('id', $id)
            ->where('masyarakat_id', $request->user()->id)
            ->first();
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan atau Anda tidak berhak mengaksesnya.'], 404);
        }
            
        return (new PermohonanKKHilangResource($permohonan))
            ->additional(['message' => 'Detail permohonan KK Hilang berhasil diambil.'])
            ->response();
    }

    /**
     * Mengunduh file hasil akhir untuk pengguna yang terotentikasi.
     */
    public function downloadHasil(Request $request, $id) // Tipe return bisa bervariasi
    {
        // [PERBAIKAN] Query dilakukan ke Model, bukan ke Resource
        $permohonan = PermohonanKKHilang::where('id', $id)
            ->where('masyarakat_id', $request->user()->id)
            ->first();

        if (!$permohonan || $permohonan->status !== 'selesai' || !$permohonan->file_hasil_akhir) {
            return response()->json(['message' => 'Dokumen tidak ditemukan, belum selesai, atau file tidak tersedia.'], 404);
        }
        
        $filePath = $permohonan->file_hasil_akhir;

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }

        Log::error('[KK Hilang API - Download] File hasil akhir tidak ditemukan di storage untuk ID: ' . $id .'. Path yang dicari: ' . $filePath);
        return response()->json(['message' => 'File fisik tidak ditemukan di server.'], 404);
    }
}
