<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\kk_baru\StoreKKBaruRequest;
use App\Http\Resources\Permohonan\kk_baru\PermohonanKKBaruResource;
use App\Models\PermohonanKKBaru;
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

class KKBaruApiController extends Controller
{
     public function store(StoreKKBaruRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;

            // Cek apakah ini proses revisi atau pembuatan baru
            if ($request->has('revisi_id') && !empty($request->revisi_id)) {
                // ALUR REVISI
                $permohonan = PermohonanKKBaru::where('id', $request->revisi_id)
                                              ->where('masyarakat_id', $user->id)
                                              ->where('status', 'membutuhkan_revisi')
                                              ->firstOrFail();
                
                $dbData['status'] = 'pending';
                $dbData['catatan_penolakan'] = null;

            } else {
                // ALUR PEMBUATAN BARU
                $dbData['status'] = 'pending';
            }

            // Logika upload file
            $fileFields = [
                'surat_pengantar_rt_rw', 'file_kk', 'file_ktp',
                'buku_nikah_akta_cerai', 'surat_pindah_datang', 'ijazah_terakhir'
            ];
            $basePath = 'permohonan_kk_baru/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    // Hapus file lama jika ini revisi dan ada file baru yang diupload
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
                $message = 'Revisi permohonan KK Baru berhasil dikirim.';
            } else {
                $permohonan = PermohonanKKBaru::create($dbData);
                $message = 'Permohonan KK Baru berhasil diajukan.';
            }

            // Notifikasi ke Petugas
            $semuaPetugas = User::where('role', 'petugas')->get();
            if ($semuaPetugas->isNotEmpty()) {
                $title = $permohonan->getJudulNotifikasi();
                $notifMessage = 'Ada ' . $title . ' baru dari ' . $user->nama_lengkap;
                if(isset($permohonan) && $permohonan->wasChanged()) {
                    $notifMessage = 'Ada revisi untuk ' . $title . ' dari ' . $user->nama_lengkap;
                }
                $url = $permohonan->getRouteTujuan();
                $permohonanId = $permohonan->getId();
                Notification::send($semuaPetugas, (new PermohonanBaru($title, $notifMessage, $url, $permohonanId))->afterCommit());
            }

            // Notifikasi ke Masyarakat (hanya untuk pengajuan baru)
            if(!isset($permohonan) || !$permohonan->wasChanged()){
                 Notification::send($user, (new StatusPermohonanDiperbarui($permohonan))->afterCommit());
            }

            return (new PermohonanKKBaruResource($permohonan))
                ->additional(['message' => $message])
                ->response()->setStatusCode(201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Permohonan untuk direvisi tidak ditemukan atau status tidak valid.'], 404);
        } catch (\Exception $e) {
            Log::error('[API KK Baru - Store/Revisi] Gagal menyimpan: ' . $e->getMessage());
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
            'catatan_pemohon' => 'nullable|string',
        ]);

        $user = $request->user();
        $dbData = $validatedData;
        $dbData['masyarakat_id'] = $user->id;
        $dbData['status'] = 'draft';

        $permohonan = PermohonanKKBaru::create($dbData);

        return response()->json([
            'message' => 'Permohonan berhasil disimpan sebagai draft.',
            'data' => new PermohonanKKBaruResource($permohonan),
        ], 201);
    }

    public function updateDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanKKBaru::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();

        $validatedData = $request->validate([
            'catatan_pemohon' => 'nullable|string',
        ]);

        $permohonan->update($validatedData);

        return response()->json([
            'message' => 'Draft berhasil diperbarui.',
            'data' => new PermohonanKKBaruResource($permohonan),
        ]);
    }

    public function destroyDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanKKBaru::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();
        
        $permohonan->delete();

        return response()->json(['message' => 'Draft berhasil dihapus.'], 200);
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