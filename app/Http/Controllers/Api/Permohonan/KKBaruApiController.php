<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\kk_baru\StoreKKBaruRequest;
use App\Http\Resources\Permohonan\kk_baru\PermohonanKKBaruResource;
use App\Models\PermohonanKKBaru;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KKBaruApiController extends Controller
{
    /**
     * Menyimpan permohonan KK Baru dari aplikasi mobile.
     */
    public function store(StoreKKBaruRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user(); // Ini adalah objek Masyarakat yang login
        $uploadedFilePaths = [];

        try {
            // Kita hanya akan menyimpan data yang ada kolomnya di tabel permohonan.
            $dbData = [
                'masyarakat_id' => $user->id,
                'status' => 'pending',
                'catatan_pemohon' => $validatedData['catatan_pemohon'] ?? null,
            ];

            // Sesuaikan file-file yang dibutuhkan untuk Permohonan KK Baru
            $fileFields = [
                'surat_pengantar_rt_rw',
                'kk_lama', // jika menumpang
                'file_ktp',
                'buku_nikah_akta_cerai', // jika ada
                'surat_pindah_datang', // jika pindahan
                'ijazah_terakhir' // jika ada
            ];
            $basePath = 'permohonan_kk_baru/lampiran';

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

            $permohonan = PermohonanKKBaru::create($dbData);

            // Mengirim notifikasi ke petugas menggunakan pola yang sudah benar
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();
                if ($semuaPetugas->isNotEmpty()) {
                    $title = $permohonan->getJudulNotifikasi();
                    $message = 'Ada ' . $title . ' baru dari ' . $user->nama_lengkap;
                    $url = $permohonan->getRouteTujuan();
                    $permohonanId = $permohonan->getId();

                    $notification = (new PermohonanBaru($title, $message, $url, $permohonanId))->afterCommit();
                    Notification::send($semuaPetugas, $notification);
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi untuk KK Baru: ' . $e->getMessage());
            }

            return (new PermohonanKKBaruResource($permohonan))
                ->additional(['message' => 'Permohonan KK Baru berhasil diajukan.'])
                ->response()->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API KK Baru - Store] Gagal menyimpan: ' . $e->getMessage());
            // Cleanup file jika terjadi error
            foreach ($uploadedFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            return response()->json(['message' => 'Gagal menyimpan permohonan.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan daftar permohonan milik pengguna.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanKKBaruResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan KK Baru berhasil diambil.'])
            ->response();
    }

    /**
     * Menampilkan detail satu permohonan.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan.'], 404);
        }
            
        return (new PermohonanKKBaruResource($permohonan))
            ->additional(['message' => 'Detail permohonan KK Baru berhasil diambil.'])
            ->response();
    }
}