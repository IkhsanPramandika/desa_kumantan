<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_ahli_waris\StoreSKAhliWarisRequest;
use App\Http\Resources\Permohonan\sk_ahli_waris\PermohonanSKAhliWarisResource;
use App\Models\PermohonanSKAhliWaris;
use App\Models\User;
use App\Notifications\PermohonanBaru; // Pastikan use statement ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class SKAhliWarisApiController extends Controller
{
    /**
     * Menampilkan daftar permohonan milik pengguna yang terotentikasi.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $permohonan = PermohonanSKAhliWaris::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);

        return PermohonanSKAhliWarisResource::collection($permohonan);
    }

    /**
     * Menyimpan permohonan baru dari aplikasi mobile.
     */
    public function store(StoreSKAhliWarisRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // PERBAIKAN: Menambahkan logika upload file yang hilang
            $fileFields = [
                'file_ktp_pemohon',
                'file_kk_pemohon',
                'file_ktp_ahli_waris',
                'file_kk_ahli_waris',
                'surat_keterangan_kematian',
                'surat_pengantar_rt_rw',
            ];
            $basePath = 'permohonan_sk_ahli_waris/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($basePath, 'public');
                    $dbData[$field] = $path;
                    $uploadedFilePaths[] = $path;
                }
            }
            
            $permohonan = PermohonanSKAhliWaris::create($dbData);

            // ====================================================================
            // [MODIFIKASI] KIRIM NOTIFIKASI UNIVERSAL
            // ====================================================================
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();

                if ($semuaPetugas->isNotEmpty()) {
                    // PERBAIKAN: Sesuaikan parameter untuk SK Ahli Waris
                    $jenisSurat = "SK Ahli Waris";
                    $routeName = "petugas.permohonan-sk-ahli-waris.show"; // Sesuaikan jika nama route Anda berbeda

                    Notification::send($semuaPetugas, new PermohonanBaru($permohonan, $jenisSurat, $routeName));
                }
            } catch (\Exception $e) {
                // Log jika pengiriman notifikasi gagal, tapi jangan hentikan proses utama
                Log::error('Gagal mengirim notifikasi untuk SK Ahli Waris: ' . $e->getMessage());
            }
            // ====================================================================

            return (new PermohonanSKAhliWarisResource($permohonan))
                ->additional(['message' => 'Permohonan SK Ahli Waris berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            // PERBAIKAN: Error handling untuk menghapus file jika penyimpanan gagal
            Log::error('[API SK Ahli Waris - Store] Gagal menyimpan: ' . $e->getMessage());
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
        $permohonan = PermohonanSKAhliWaris::where('id', $id)
            ->where('masyarakat_id', $user->id)
            ->firstOrFail();
            
        return new PermohonanSKAhliWarisResource($permohonan);
    }

    /**
     * Mengunduh file hasil akhir untuk pengguna yang terotentikasi.
     */
    public function downloadHasil(Request $request, $id)
    {
        $user = $request->user();
        $permohonan = PermohonanSKAhliWaris::where('id', $id)
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
