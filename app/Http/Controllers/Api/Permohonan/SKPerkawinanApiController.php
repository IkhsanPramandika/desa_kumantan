<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\sk_perkawinan\StoreSKPerkawinanRequest;
use App\Http\Resources\Permohonan\sk_perkawinan\PermohonanSKPerkawinanResource;
use App\Models\PermohonanSKPerkawinan;
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

class SKPerkawinanApiController extends Controller
{
    public function store(StoreSKPerkawinanRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

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
            
            if ($request->has('draft_id')) {
                $permohonan = PermohonanSKPerkawinan::findOrFail($request->draft_id);
                $permohonan->update($dbData);
            } else {
                $permohonan = PermohonanSKPerkawinan::create($dbData);
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
                Log::error('Gagal mengirim notifikasi untuk SK Perkawinan: ' . $e->getMessage());
            }

            try {
                Notification::send($user, new StatusPermohonanDiperbarui($permohonan));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi konfirmasi SK Perkawinan ke masyarakat: ' . $e->getMessage());
            }

            return (new PermohonanSKPerkawinanResource($permohonan))
                ->additional(['message' => 'Permohonan SK Perkawinan berhasil diajukan.'])
                ->response()->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API SK Perkawinan - Store] Gagal menyimpan: ' . $e->getMessage());
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
            'nama_pria' => 'nullable|string|max:255',
            'nik_pria' => 'nullable|string|max:16',
            'tempat_lahir_pria' => 'nullable|string|max:255',
            'tanggal_lahir_pria' => 'nullable|date',
            'alamat_pria' => 'nullable|string',
            'nama_wanita' => 'nullable|string|max:255',
            'nik_wanita' => 'nullable|string|max:16',
            'tempat_lahir_wanita' => 'nullable|string|max:255',
            'tanggal_lahir_wanita' => 'nullable|date',
            'alamat_wanita' => 'nullable|string',
            'tanggal_akad' => 'nullable|date',
            'tempat_akad' => 'nullable|string|max:255',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $user = $request->user();
        $dbData = $validatedData;
        $dbData['masyarakat_id'] = $user->id;
        $dbData['status'] = 'draft';

        $permohonan = PermohonanSKPerkawinan::create($dbData);

        return response()->json([
            'message' => 'Permohonan berhasil disimpan sebagai draft.',
            'data' => new PermohonanSKPerkawinanResource($permohonan),
        ], 201);
    }

    public function updateDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanSKPerkawinan::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();

        $validatedData = $request->validate([
            'nama_pria' => 'nullable|string|max:255',
            'nik_pria' => 'nullable|string|max:16',
            'tempat_lahir_pria' => 'nullable|string|max:255',
            'tanggal_lahir_pria' => 'nullable|date',
            'alamat_pria' => 'nullable|string',
            'nama_wanita' => 'nullable|string|max:255',
            'nik_wanita' => 'nullable|string|max:16',
            'tempat_lahir_wanita' => 'nullable|string|max:255',
            'tanggal_lahir_wanita' => 'nullable|date',
            'alamat_wanita' => 'nullable|string',
            'tanggal_akad' => 'nullable|date',
            'tempat_akad' => 'nullable|string|max:255',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $permohonan->update($validatedData);

        return response()->json([
            'message' => 'Draft berhasil diperbarui.',
            'data' => new PermohonanSKPerkawinanResource($permohonan),
        ]);
    }

    public function destroyDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanSKPerkawinan::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();
        
        $permohonan->delete();

        return response()->json(['message' => 'Draft berhasil dihapus.'], 200);
    }

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

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanSKPerkawinan::with('masyarakat')->where('masyarakat_id', $user->id)
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