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
        $dbData['status'] = 'pending';

        $uploadedPaths = []; // Definisikan di sini agar bisa diakses di blok catch

        try {
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $filePath = 'permohonan_lainnya/lampiran/' . $fileName;
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    $uploadedPaths[] = $filePath;
                }
                // Simpan path sebagai JSON string di database
                $dbData['lampiran'] = json_encode($uploadedPaths);
            }

            if ($request->has('draft_id')) {
                $permohonan = PermohonanLainnya::findOrFail($request->draft_id);
                $permohonan->update($dbData);
            } else {
                $permohonan = PermohonanLainnya::create($dbData);
            }

            // Notifikasi ke Petugas
            $semuaPetugas = User::where('role', 'petugas')->get();
            if ($semuaPetugas->isNotEmpty()) {
                Notification::send($semuaPetugas, new PermohonanBaru(
                    $permohonan->getJudulNotifikasi(),
                    'Ada permohonan baru dari ' . $user->nama_lengkap,
                    $permohonan->getRouteTujuan(),
                    $permohonan->getId()
                ));
            }
            
            // Notifikasi konfirmasi ke Masyarakat
            Notification::send($user, (new StatusPermohonanDiperbarui($permohonan))->afterCommit());

            return (new PermohonanLainnyaResource($permohonan))
                ->additional(['message' => 'Permohonan Anda berhasil diajukan.'])
                ->response()->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('[API Permohonan Lainnya - Store] Gagal menyimpan: ' . $e->getMessage());
            
            // --- PERBAIKAN DI SINI ---
            // Hapus semua file yang sudah terupload jika terjadi error
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
