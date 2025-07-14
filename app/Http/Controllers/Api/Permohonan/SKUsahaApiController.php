<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_usaha\StoreSKUsahaRequest;
use App\Http\Resources\Permohonan\sk_usaha\PermohonanSKUsahaResource;
use App\Models\PermohonanSKUsaha;
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

class SKUsahaApiController extends Controller
{
    public function store(StoreSKUsahaRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            $fileFields = ['file_kk', 'file_ktp'];
            $basePath = 'permohonan_sk_usaha/lampiran';
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
                $permohonan = PermohonanSKUsaha::findOrFail($request->draft_id);
                $permohonan->update($dbData);
            } else {
                $permohonan = PermohonanSKUsaha::create($dbData);
            }

            // Notifikasi ke Petugas (sudah benar)
            try {
                $semuaPetugas = User::where('role', 'petugas')->get();
                if ($semuaPetugas->isNotEmpty()) {
                    $title = $permohonan->getJudulNotifikasi();
                    $message = 'Ada ' . $title . ' baru dari ' . $user->nama_lengkap;
                    $url = $permohonan->getRouteTujuan();
                    $permohonanId = $permohonan->getId();
                    Notification::send($semuaPetugas, (new PermohonanBaru($title, $message, $url, $permohonanId))->afterCommit());
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi ke petugas: ' . $e->getMessage());
            }

            // Notifikasi konfirmasi ke Masyarakat
            try {
                // [PERBAIKAN KUNCI] Tambahkan ->afterCommit()
                // Ini memastikan notifikasi hanya dikirim setelah data permohonan
                // berhasil disimpan permanen di database.
                Notification::send($user, (new StatusPermohonanDiperbarui($permohonan))->afterCommit());

            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi konfirmasi ke masyarakat: ' . $e->getMessage());
            }

            return (new PermohonanSKUsahaResource($permohonan))
                ->additional(['message' => 'Permohonan SK Usaha berhasil diajukan.'])
                ->response()->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SK Usaha - Store] Gagal menyimpan: ' . $e->getMessage());
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
            'nama_pemohon' => 'nullable|string|max:255',
            'nik_pemohon' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'warganegara_agama' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat_pemohon' => 'nullable|string',
            'nama_usaha' => 'nullable|string|max:255',
            'alamat_usaha' => 'nullable|string',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $user = $request->user();
        $dbData = $validatedData;
        $dbData['masyarakat_id'] = $user->id;
        $dbData['status'] = 'draft';

        $permohonan = PermohonanSKUsaha::create($dbData);

        return response()->json([
            'message' => 'Permohonan berhasil disimpan sebagai draft.',
            'data' => new PermohonanSKUsahaResource($permohonan),
        ], 201);
    }

    public function updateDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanSKUsaha::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();

        $validatedData = $request->validate([
            'nama_pemohon' => 'nullable|string|max:255',
            'nik_pemohon' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'warganegara_agama' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat_pemohon' => 'nullable|string',
            'nama_usaha' => 'nullable|string|max:255',
            'alamat_usaha' => 'nullable|string',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $permohonan->update($validatedData);

        return response()->json([
            'message' => 'Draft berhasil diperbarui.',
            'data' => new PermohonanSKUsahaResource($permohonan),
        ]);
    }

    public function destroyDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanSKUsaha::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();

        $permohonan->delete();

        return response()->json(['message' => 'Draft berhasil dihapus.'], 200);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKUsaha::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);

        return PermohonanSKUsahaResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan SK Usaha berhasil diambil.'])
            ->response();
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKUsaha::with('masyarakat')->where('masyarakat_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan.'], 404);
        }

        return (new PermohonanSKUsahaResource($permohonan))
            ->additional(['message' => 'Detail permohonan berhasil diambil.'])
            ->response();
    }
}
