<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Models\User;
use Illuminate\Http\Request;

use App\Events\PermohonanMasuk;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\PermohonanBaru;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Api\Permohonan\kk_hilang\StoreKKHilangRequest;
use App\Models\PermohonanKKHilang; // [PERBAIKAN] Nama disesuaikan (tanpa typo 'nan')
use App\Http\Resources\Permohonan\kk_hilang\PermohonanKKHilangResource; // [PERBAIKAN] Nama disesuaikan

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
    public function store(StoreKKHilangRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // ... proses upload file ...

            $permohonan = PermohonanKKHilang::create($dbData);

            // ====================================================================
            // [MODIFIKASI] KIRIM NOTIFIKASI UNIVERSAL
            // ====================================================================
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();

                if ($semuaPetugas->isNotEmpty()) {
                    // Membuat instance notifikasi universal dengan parameter yang relevan
                    $jenisSurat = "KK HILANG";
                    $routeName = "petugas.permohonan-kk-hilang.show"; // Sesuaikan dengan nama route Anda di web.php

                    Notification::send($semuaPetugas, new PermohonanBaru($permohonan, $jenisSurat, $routeName));
                }
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
