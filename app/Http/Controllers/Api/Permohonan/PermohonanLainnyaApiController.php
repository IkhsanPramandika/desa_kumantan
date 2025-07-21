<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Permohonan\permohonan_lainnya\StorePermohonanLainnyaRequest;
use App\Http\Resources\Permohonan\permohonan_lainnya\PermohonanLainnyaResource;
use App\Models\PermohonanLainnya;
use App\Models\User;
use App\Notifications\PermohonanBaru;
use App\Notifications\StatusPermohonanDiperbarui;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanLainnyaApiController extends Controller
{
    /**
     * Menampilkan daftar riwayat permohonan lainnya milik pengguna.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanLainnya::where('masyarakat_id', $user->id)
            ->latest()
            ->paginate(10);

        return PermohonanLainnyaResource::collection($permohonan)
            ->additional(['message' => 'Daftar permohonan berhasil diambil.'])
            ->response();
    }

    /**
     * Menampilkan detail satu permohonan lainnya milik pengguna.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanLainnya::where('masyarakat_id', $user->id)
            ->findOrFail($id);

        return (new PermohonanLainnyaResource($permohonan))
            ->additional(['message' => 'Detail permohonan berhasil diambil.'])
            ->response();
    }

    /**
     * Menyimpan permohonan baru yang sudah final.
     */
   public function store(StorePermohonanLainnyaRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $dbData = $validatedData;
        $dbData['masyarakat_id'] = $user->id;
        $uploadedPaths = [];

        try {
            // Cek apakah ini proses revisi atau pembuatan baru
            if ($request->has('revisi_id') && !empty($request->revisi_id)) {
                // ALUR REVISI
                $permohonan = PermohonanLainnya::where('id', $request->revisi_id)
                                              ->where('masyarakat_id', $user->id)
                                              ->where('status', 'membutuhkan_revisi')
                                              ->firstOrFail();
                
                $dbData['status'] = 'pending';
                $dbData['catatan_penolakan'] = null;

            } else {
                // ALUR PEMBUATAN BARU
                $dbData['status'] = 'pending';
            }

            // Logika upload multiple files
            if ($request->hasFile('lampiran')) {
                // Jika ini revisi dan ada file baru, hapus semua file lampiran lama
                if (isset($permohonan) && !empty($permohonan->lampiran)) {
                    $oldFiles = json_decode($permohonan->lampiran, true);
                    if (is_array($oldFiles)) {
                        foreach ($oldFiles as $oldFile) {
                            Storage::disk('public')->delete($oldFile);
                        }
                    }
                }

                // Upload file-file yang baru
                foreach ($request->file('lampiran') as $file) {
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $filePath = 'permohonan_lainnya/lampiran/' . $fileName;
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    $uploadedPaths[] = $filePath;
                }
                // Simpan path sebagai JSON string di database
                $dbData['lampiran'] = json_encode($uploadedPaths);
            }

            // Lakukan update jika revisi, atau create jika baru
            if (isset($permohonan)) {
                $permohonan->update($dbData);
                $message = 'Revisi permohonan berhasil dikirim.';
            } else {
                $permohonan = PermohonanLainnya::create($dbData);
                $message = 'Permohonan Anda berhasil diajukan.';
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

            return (new PermohonanLainnyaResource($permohonan))
                ->additional(['message' => $message])
                ->response()->setStatusCode(201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Permohonan untuk direvisi tidak ditemukan atau status tidak valid.'], 404);
        } catch (\Exception $e) {
            Log::error('[API Permohonan Lainnya - Store/Revisi] Gagal menyimpan: ' . $e->getMessage());
            foreach ($uploadedPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            return response()->json(['message' => 'Gagal menyimpan permohonan.', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Menyimpan permohonan sebagai draft.
     */
    public function storeAsDraft(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'judul_permohonan' => 'nullable|string|max:255',
            'keperluan' => 'nullable|string|max:1000',
            'rincian_pemohon' => 'nullable|string|max:5000',
        ]);

        $user = $request->user();
        $dbData = $validatedData;
        $dbData['masyarakat_id'] = $user->id;
        $dbData['status'] = 'draft';

        $permohonan = PermohonanLainnya::create($dbData);

        return response()->json([
            'message' => 'Permohonan berhasil disimpan sebagai draft.',
            'data' => new PermohonanLainnyaResource($permohonan),
        ], 201);
    }

    /**
     * Memperbarui draft yang sudah ada.
     */
    public function updateDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanLainnya::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();
            
        $validatedData = $request->validate([
            'judul_permohonan' => 'nullable|string|max:255',
            'keperluan' => 'nullable|string|max:1000',
            'rincian_pemohon' => 'nullable|string|max:5000',
        ]);

        $permohonan->update($validatedData);

        return response()->json([
            'message' => 'Draft berhasil diperbarui.',
            'data' => new PermohonanLainnyaResource($permohonan),
        ]);
    }

    /**
     * Menghapus draft yang sudah ada.
     */
    public function destroyDraft(Request $request, $id): JsonResponse
    {
        $permohonan = PermohonanLainnya::where('masyarakat_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'draft')
            ->firstOrFail();

        $permohonan->delete();

        return response()->json(['message' => 'Draft berhasil dihapus.'], 200);
    }

    /**
     * Mengunduh dokumen hasil akhir (PDF).
     */
    public function downloadHasil(Request $request, $id): \Symfony\Component\HttpFoundation\StreamedResponse|JsonResponse
    {
        $user = $request->user();
        $permohonan = PermohonanLainnya::where('id', $id)
            ->where('masyarakat_id', $user->id)
            ->firstOrFail();
            
        if ($permohonan->status !== 'selesai') {
            return response()->json(['message' => 'Surat belum selesai diproses.'], 403);
        }

        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }

        return response()->json(['message' => 'File tidak ditemukan atau belum tersedia.'], 404);
    }
}
