<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_kelahiran\StoreSKKelahiranRequest;
use App\Http\Resources\Permohonan\sk_kelahiran\PermohonanSKKelahiranResource;
use App\Models\PermohonanSKKelahiran;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SKKelahiranApiController extends Controller
{
    public function store(StoreSKKelahiranRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            $fileFields = ['file_kk', 'file_ktp', 'surat_pengantar_rt_rw', 'surat_nikah_orangtua', 'surat_keterangan_kelahiran'];
            $basePath = 'permohonan_sk_kelahiran/lampiran';

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
            
            $permohonan = PermohonanSKKelahiran::create($dbData);

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
                Log::error('Gagal mengirim notifikasi untuk SK Kelahiran: ' . $e->getMessage());
            }

            return (new PermohonanSKKelahiranResource($permohonan))
                ->additional(['message' => 'Permohonan SK Kelahiran berhasil diajukan.'])
                ->response()->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SK Kelahiran - Store] Gagal menyimpan: ' . $e->getMessage());
            foreach ($uploadedFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            return response()->json(['message' => 'Gagal menyimpan permohonan.', 'error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKKelahiran::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanSKKelahiranResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan SK Kelahiran berhasil diambil.'])
            ->response();
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKKelahiran::where('masyarakat_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan.'], 404);
        }
            
        return (new PermohonanSKKelahiranResource($permohonan))
            ->additional(['message' => 'Detail permohonan berhasil diambil.'])
            ->response();
    }
}
