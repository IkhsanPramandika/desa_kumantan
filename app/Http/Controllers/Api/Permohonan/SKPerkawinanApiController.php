<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_perkawinan\StoreSKPerkawinanRequest;
use App\Http\Resources\Permohonan\sk_perkawinan\PermohonanSKPerkawinanResource;
use App\Models\PermohonanSKPerkawinan;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SKPerkawinanApiController extends Controller
{
    /**
     * Menyimpan permohonan SK Perkawinan baru dari aplikasi mobile.
     */
    public function store(StoreSKPerkawinanRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user(); // Ini adalah objek Masyarakat yang login
        $uploadedFilePaths = [];

        try {
            // Mengambil semua data yang divalidasi dan menambahkan data sistem
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // Menangani upload file lampiran
            $fileFields = [
                'file_kk', 'file_ktp_mempelai', 'surat_nikah_orang_tua', 
                'kartu_imunisasi_catin', 'sertifikat_elsimil', 'akta_penceraian'
            ];
            $basePath = 'permohonan_sk_perkawinan/lampiran';

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
            
            $permohonan = PermohonanSKPerkawinan::create($dbData);

            // Mengirim notifikasi ke petugas menggunakan pola yang paling aman
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();
                if ($semuaPetugas->isNotEmpty()) {
                    // Siapkan semua data matang di sini
                    $title = $permohonan->getJudulNotifikasi();
                    $message = 'Ada ' . $title . ' baru dari ' . $user->nama_lengkap;
                    $url = $permohonan->getRouteTujuan();
                    $permohonanId = $permohonan->getId();

                    $notification = (new PermohonanBaru($title, $message, $url, $permohonanId))->afterCommit();
                    Notification::send($semuaPetugas, $notification);
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi untuk SK Perkawinan: ' . $e->getMessage());
            }

            return (new PermohonanSKPerkawinanResource($permohonan))
                ->additional(['message' => 'Permohonan SK Perkawinan berhasil diajukan.'])
                ->response()->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SK Perkawinan - Store] Gagal menyimpan: ' . $e->getMessage());
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
        $permohonan = PermohonanSKPerkawinan::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanSKPerkawinanResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan SK Perkawinan berhasil diambil.'])
            ->response();
    }

    /**
     * Menampilkan detail satu permohonan.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKPerkawinan::where('masyarakat_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan.'], 404);
        }
            
        return (new PermohonanSKPerkawinanResource($permohonan))
            ->additional(['message' => 'Detail permohonan berhasil diambil.'])
            ->response();
    }
}
