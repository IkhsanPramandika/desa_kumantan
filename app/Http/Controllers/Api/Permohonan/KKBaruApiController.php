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
    public function store(StoreKKBaruRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            $fileFields = [
                'surat_pengantar_rt_rw',
                'file_kk',
                'file_ktp',
                'buku_nikah_akta_cerai',
                'surat_pindah_datang',
                'ijazah_terakhir'
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
        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanKKBaruResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan KK Baru berhasil diambil.'])
            ->response();
    }

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
