<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_ahli_waris\StoreSKAhliWarisRequest;
use App\Http\Resources\Permohonan\sk_ahli_waris\PermohonanSKAhliWarisResource;
use App\Models\PermohonanSKAhliWaris;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use App\Notifications\StatusPermohonanDiperbarui;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SKAhliWarisApiController extends Controller
{
    public function store(StoreSKAhliWarisRequest $request): JsonResponse
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
            
            if ($request->has('draft_id')) {
                $permohonan = PermohonanSKAhliWaris::findOrFail($request->draft_id);
                $permohonan->update($dbData);
            } else {
                $permohonan = PermohonanSKAhliWaris::create($dbData);
            }

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

            try {
                Notification::send($user, new StatusPermohonanDiperbarui($permohonan));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi konfirmasi SK Ahli Waris ke masyarakat: ' . $e->getMessage());
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

    public function storeAsDraft(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nama_pewaris' => 'nullable|string|max:255',
            'nik_pewaris' => 'nullable|string|max:16',
            'tempat_lahir_pewaris' => 'nullable|string|max:255',
            'tanggal_lahir_pewaris' => 'nullable|date',
            'tanggal_meninggal_pewaris' => 'nullable|date',
            'alamat_pewaris' => 'nullable|string',
            'daftar_ahli_waris' => 'nullable|json',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $user = $request->user();
        $dbData = $validatedData;
        $dbData['masyarakat_id'] = $user->id;
        $dbData['status'] = 'draft';

        $permohonan = PermohonanSKAhliWaris::create($dbData);

        return response()->json([
            'message' => 'Permohonan berhasil disimpan sebagai draft.',
            'data' => new PermohonanSKAhliWarisResource($permohonan),
        ], 201);
    }

    public function updateDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanSKAhliWaris::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();

        $validatedData = $request->validate([
            'nama_pewaris' => 'nullable|string|max:255',
            'nik_pewaris' => 'nullable|string|max:16',
            'tempat_lahir_pewaris' => 'nullable|string|max:255',
            'tanggal_lahir_pewaris' => 'nullable|date',
            'tanggal_meninggal_pewaris' => 'nullable|date',
            'alamat_pewaris' => 'nullable|string',
            'daftar_ahli_waris' => 'nullable|json',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $permohonan->update($validatedData);

        return response()->json([
            'message' => 'Draft berhasil diperbarui.',
            'data' => new PermohonanSKAhliWarisResource($permohonan),
        ]);
    }

    public function destroyDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanSKAhliWaris::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();
        
        $permohonan->delete();

        return response()->json(['message' => 'Draft berhasil dihapus.'], 200);
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
        $permohonan = PermohonanSKAhliWaris::with('masyarakat')->where('masyarakat_id', $user->id)
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