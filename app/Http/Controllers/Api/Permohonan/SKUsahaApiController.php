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
            
            // [PERUBAHAN UTAMA] Cek apakah ini proses revisi atau pembuatan baru
            if ($request->has('revisi_id')) {
                // INI ADALAH ALUR REVISI
                $permohonan = PermohonanSKUsaha::where('id', $request->revisi_id)
                                            ->where('masyarakat_id', $user->id)
                                            ->where('status', 'membutuhkan_revisi')
                                            ->firstOrFail();
                
                // Set status kembali ke 'pending' dan hapus catatan lama
                $dbData['status'] = 'pending';
                $dbData['catatan_penolakan'] = null;

            } else {
                // INI ADALAH ALUR PEMBUATAN BARU (seperti sebelumnya)
                $dbData['status'] = 'pending';
            }

            // Logika upload file (tetap sama, berlaku untuk revisi & baru)
            $fileFields = ['file_kk', 'file_ktp'];
            $basePath = 'permohonan_sk_usaha/lampiran';
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    // Jika ini revisi & ada file baru, hapus file lama
                    if (isset($permohonan) && $permohonan->{$field}) {
                        Storage::disk('public')->delete($permohonan->{$field});
                    }
                    $file = $request->file($field);
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $filePath = $basePath . '/' . $fileName;
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    $dbData[$field] = $filePath;
                    $uploadedFilePaths[] = $filePath;
                }
            }

            // Lakukan update jika revisi, atau create jika baru
            if (isset($permohonan)) {
                $permohonan->update($dbData);
            } else {
                $permohonan = PermohonanSKUsaha::create($dbData);
            }

            // Notifikasi ke Petugas (tetap sama)
            $semuaPetugas = User::where('role', 'petugas')->get();
            if ($semuaPetugas->isNotEmpty()) {
                $title = $permohonan->getJudulNotifikasi();
                $message = 'Ada ' . $title . ' baru (hasil revisi) dari ' . $user->nama_lengkap;
                $url = $permohonan->getRouteTujuan();
                $permohonanId = $permohonan->getId();
                Notification::send($semuaPetugas, (new PermohonanBaru($title, $message, $url, $permohonanId))->afterCommit());
            }

            return (new PermohonanSKUsahaResource($permohonan))
                    ->additional(['message' => 'Permohonan SK Usaha berhasil dikirim.'])
                    ->response()->setStatusCode(201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Permohonan untuk direvisi tidak ditemukan atau status tidak valid.'], 404);
        } catch (\Exception $e) {
            Log::error('[API SK Usaha - Store/Revisi] Gagal menyimpan: ' . $e->getMessage());
            // ... logika hapus file jika gagal ...
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
            'keperluan_surat' => 'required|string|max:1000',
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
            'keperluan_surat' => 'required|string|max:1000',
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
