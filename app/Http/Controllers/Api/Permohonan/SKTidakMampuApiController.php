<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_tidak_mampu\StoreSKTidakMampuRequest;
use App\Http\Resources\Permohonan\sk_tidak_mampu\PermohonanSKTidakMampuResource;
use App\Models\PermohonanSKTidakMampu;
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

class SKTidakMampuApiController extends Controller
{
    public function store(StoreSKTidakMampuRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            $fileFields = ['file_kk', 'file_ktp', 'file_pendukung_lain'];
            $basePath = 'permohonan_sk_tidak_mampu/lampiran';

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
                $permohonan = PermohonanSKTidakMampu::findOrFail($request->draft_id);
                $permohonan->update($dbData);
            } else {
                $permohonan = PermohonanSKTidakMampu::create($dbData);
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
                Log::error('Gagal mengirim notifikasi untuk SK Tidak Mampu: ' . $e->getMessage());
            }

            try {
                Notification::send($user, new StatusPermohonanDiperbarui($permohonan));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi konfirmasi SKTM ke masyarakat: ' . $e->getMessage());
            }

            return (new PermohonanSKTidakMampuResource($permohonan))
                ->additional(['message' => 'Permohonan SK Tidak Mampu berhasil diajukan.'])
                ->response()->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SKTM - Store] Gagal menyimpan: ' . $e->getMessage());
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
            'nama_terkait' => 'nullable|string|max:255',
            'nik_terkait' => 'nullable|string|max:20',
            'tempat_lahir_terkait' => 'nullable|string|max:100',
            'tanggal_lahir_terkait' => 'nullable|date',
            'jenis_kelamin_terkait' => 'nullable|string',
            'agama_terkait' => 'nullable|string',
            'pekerjaan_atau_sekolah_terkait' => 'nullable|string|max:100',
            'alamat_terkait' => 'nullable|string',
            'keperluan_surat' => 'nullable|string',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $user = $request->user();
        $dbData = $validatedData;
        $dbData['masyarakat_id'] = $user->id;
        $dbData['status'] = 'draft';

        $permohonan = PermohonanSKTidakMampu::create($dbData);

        return response()->json([
            'message' => 'Permohonan berhasil disimpan sebagai draft.',
            'data' => new PermohonanSKTidakMampuResource($permohonan),
        ], 201);
    }

    public function updateDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanSKTidakMampu::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();

        $validatedData = $request->validate([
            'nama_terkait' => 'nullable|string|max:255',
            'nik_terkait' => 'nullable|string|max:20',
            'tempat_lahir_terkait' => 'nullable|string|max:100',
            'tanggal_lahir_terkait' => 'nullable|date',
            'jenis_kelamin_terkait' => 'nullable|string',
            'agama_terkait' => 'nullable|string',
            'pekerjaan_atau_sekolah_terkait' => 'nullable|string|max:100',
            'alamat_terkait' => 'nullable|string',
            'keperluan_surat' => 'nullable|string',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $permohonan->update($validatedData);

        return response()->json([
            'message' => 'Draft berhasil diperbarui.',
            'data' => new PermohonanSKTidakMampuResource($permohonan),
        ]);
    }

    public function destroyDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanSKTidakMampu::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();
        
        $permohonan->delete();

        return response()->json(['message' => 'Draft berhasil dihapus.'], 200);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKTidakMampu::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return PermohonanSKTidakMampuResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan SK Tidak Mampu berhasil diambil.'])
            ->response();
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKTidakMampu::with('masyarakat')->where('masyarakat_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan.'], 404);
        }
            
        return (new PermohonanSKTidakMampuResource($permohonan))
            ->additional(['message' => 'Detail permohonan berhasil diambil.'])
            ->response();
    }
}