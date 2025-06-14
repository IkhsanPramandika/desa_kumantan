<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_domisili\StoreSkDomisiliRequest;
use App\Http\Resources\Permohonan\sk_domisili\PermohonanSKDomisiliResource;
use App\Models\PermohonanSKDomisili;
use App\Models\User; // Tambahkan use statement untuk User
use App\Notifications\PermohonanBaru; // Tambahkan use statement untuk Notifikasi Universal
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification; // Tambahkan use statement untuk Notification
use Illuminate\Support\Facades\Storage;

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
        $uploadedFilePaths = []; 

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
                    $uploadedFilePaths[] = $path;
                }
            }

            $permohonan = PermohonanSKDomisili::create($dbData);

            // ====================================================================
            // [PERBAIKAN] Mengganti sistem Event dengan Notifikasi Universal
            // ====================================================================
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();

                if ($semuaPetugas->isNotEmpty()) {
                    // Sesuaikan parameter untuk SK Domisili
                    $jenisSurat = "SK Domisili";
                    $routeName = "petugas.permohonan-sk-domisili.show"; // Sesuaikan jika nama route Anda berbeda

                    Notification::send($semuaPetugas, new PermohonanBarU($permohonan, $jenisSurat, $routeName));
                }
            } catch (\Exception $e) {
                // Catat error jika notifikasi gagal, tapi jangan hentikan proses utama
                Log::error('Gagal mengirim notifikasi untuk SK Domisili: ' . $e->getMessage());
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
