<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_ahli_waris\StoreSKAhliWarisRequest;
use App\Http\Resources\Permohonan\sk_ahli_waris\PermohonanSKAhliWarisResource;
use App\Models\PermohonanSKAhliWaris;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SKAhliWarisApiController extends Controller
{
    public function store(StoreSKAhliWarisRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            $fileFields = [
                'file_ktp_pemohon', 'file_kk_pemohon', 'file_ktp_ahli_waris',
                'file_kk_ahli_waris', 'surat_keterangan_kematian', 'surat_pengantar_rt_rw',
            ];
            $basePath = 'permohonan_sk_ahli_waris/lampiran';

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
            
            $permohonan = PermohonanSKAhliWaris::create($dbData);

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
                Log::error('Gagal mengirim notifikasi untuk SK Ahli Waris: ' . $e->getMessage());
            }

            return (new PermohonanSKAhliWarisResource($permohonan))
                ->additional(['message' => 'Permohonan SK Ahli Waris berhasil diajukan.'])
                ->response()->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SK Ahli Waris - Store] Gagal menyimpan: ' . $e->getMessage());
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
        $permohonan = PermohonanSKAhliWaris::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanSKAhliWarisResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan SK Ahli Waris berhasil diambil.'])
            ->response();
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKAhliWaris::where('masyarakat_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan.'], 404);
        }
            
        return (new PermohonanSKAhliWarisResource($permohonan))
            ->additional(['message' => 'Detail permohonan berhasil diambil.'])
            ->response();
    }
}
