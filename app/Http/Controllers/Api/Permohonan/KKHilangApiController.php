<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\kk_hilang\StoreKKHilangRequest;
use App\Http\Resources\Permohonan\kk_hilang\PermohonanKKHilangResource;
use App\Models\PermohonanKKHilang;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KKHilangApiController extends Controller
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

            // Menambahkan logika untuk menangani upload file lampiran
            $fileFields = ['surat_pengantar_rt_rw', 'surat_keterangan_hilang_kepolisian'];
            $basePath = 'permohonan_kk_hilang/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $filePath = $basePath . '/' . $fileName;
                    
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    
                    $dbData[$field] = $filePath; 
                    $uploadedFilePaths[] = $filePath;
                }
            }

            $permohonan = PermohonanKKHilang::create($dbData);

            // ====================================================================
            // [TAMBAHAN] Mengirim Notifikasi Universal ke Petugas
            // ====================================================================
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();

                if ($semuaPetugas->isNotEmpty()) {
                    // Sesuaikan parameter untuk Permohonan KK Hilang
                    $jenisSurat = "KK Hilang";
                    $routeName = "petugas.permohonan-kk-hilang.show";

                    Notification::send($semuaPetugas, new PermohonanBaru($permohonan, $jenisSurat, $routeName));
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi untuk KK Hilang: ' . $e->getMessage());
            }
            // ====================================================================
            
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
    public function downloadHasil(Request $request, $id)
    {
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
