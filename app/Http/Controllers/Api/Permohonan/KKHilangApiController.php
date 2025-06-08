<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonananKKHilang; // Pastikan nama model benar
use App\Http\Requests\Api\Permohonan\kk_hilang\StoreKkHilangRequest; // Path ke FormRequest
use App\Http\Resources\Permohonan\kk_hilang\PermohonananKKHilangResource; // Path ke Resource
// use App\Http\Resources\Permohonan\kk_hilang\PermohonananKKHilangCollection; // Jika ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str; // Ditambahkan untuk Str::startsWith dan Str::contains

class KkHilangApiController extends Controller // Pastikan nama class controller benar
{
    protected $attachmentBaseDir = 'permohonan_kk_hilang_attachments'; // Path di dalam disk 'public'

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKkHilangRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        Log::info('[KK Hilang API - Store] Validasi berhasil.', $validatedData);

        $dbData = $validatedData;
        
        $user = $request->user('sanctum');
        if (!$user) {
            Log::error('[KK Hilang API - Store] Tidak ada pengguna terautentikasi via Sanctum.');
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }
        $dbData['masyarakat_id'] = $user->id; // Pastikan kolom ini ada di tabel & model $fillable
        
        $dbData['status'] = 'pending';

        $fileFields = ['surat_pengantar_rt_rw', 'surat_keterangan_hilang_kepolisian'];
        
        // Ambil rules sekali untuk efisiensi di dalam loop
        $rulesForStore = (new StoreKkHilangRequest())->rules();

        foreach ($fileFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $path = $request->file($fileField)->store($this->attachmentBaseDir . '/' . $fileField, 'public');
                $dbData[$fileField] = $path;
                Log::info('[KK Hilang API - Store] File ' . $fileField . ' disimpan ke: ' . $path);
            } else {
                // Jika file tidak diunggah, dan field tersebut nullable berdasarkan rules, pastikan nilainya null.
                if (isset($rulesForStore[$fileField]) && str_contains($rulesForStore[$fileField], 'nullable')) {
                    if (!array_key_exists($fileField, $dbData)) { // Hanya set null jika belum ada dari $validatedData
                        $dbData[$fileField] = null;
                    }
                }
            }
        }
        
        try {
            $permohonan = PermohonananKKHilang::create($dbData);
            Log::info('[KK Hilang API - Store] Permohonan KK Hilang berhasil dibuat dengan ID: ' . $permohonan->id);

            return (new PermohonananKKHilangResource($permohonan))
                    ->additional(['message' => 'Permohonan KK Hilang berhasil diajukan.'])
                    ->response()
                    ->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error('[KK Hilang API - Store] Gagal menyimpan permohonan: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            foreach ($fileFields as $fileField) {
                if (isset($dbData[$fileField]) && $dbData[$fileField] && Storage::disk('public')->exists($dbData[$fileField])) {
                    Storage::disk('public')->delete($dbData[$fileField]);
                    Log::info('[KK Hilang API - Store] Rollback: File ' . $fileField . ' dihapus: ' . $dbData[$fileField]);
                }
            }
            return response()->json(['message' => 'Gagal menyimpan permohonan KK Hilang.', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Helper untuk mendapatkan rules dari FormRequest.
     * Tidak lagi dipanggil di store utama karena validasi sudah ditangani.
     */
    private function getRulesForStoreKkHilang(): array
    {
        return (new StoreKkHilangRequest())->rules();
    }


    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonananKKHilang::where('masyarakat_id', $user->id) // Pastikan ada kolom masyarakat_id
                                ->latest()
                                ->paginate(10);
        
        return PermohonananKKHilangResource::collection($permohonan)
                                 ->additional(['message' => 'Daftar permohonan KK Hilang berhasil diambil.'])
                                 ->response(); // <-- DITAMBAHKAN .response()
    }

    /**
     * Display the specified resource for the authenticated user.
     */
    public function show(Request $request, $id): JsonResponse 
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonananKKHilang::where('masyarakat_id', $user->id) // Pastikan ada kolom masyarakat_id
                                      ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan KK Hilang tidak ditemukan atau Anda tidak berhak mengaksesnya.'], 404);
        }
            
        return (new PermohonananKKHilangResource($permohonan))
                       ->additional(['message' => 'Detail permohonan KK Hilang berhasil diambil.'])
                       ->response(); // <-- DITAMBAHKAN .response()
    }

    /**
     * Download the final processed document if available.
     */
    public function downloadHasil(Request $request, $id): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonananKKHilang::where('masyarakat_id', $user->id)
                                      ->where('status', 'selesai') // Hanya jika sudah selesai
                                      ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan, belum selesai, atau Anda tidak berhak mengaksesnya.'], 404);
        }

        if ($permohonan->file_hasil_akhir) {
            $pathRelativeToPublicDisk = '';
            // Path yang disimpan di DB adalah URL publik, misal: /storage/dokumen_hasil_sk_hilang/SK_Hilang_xxx.pdf
            // Kita perlu mengubahnya menjadi path relatif terhadap root disk 'public', 
            // yaitu 'dokumen_hasil_sk_hilang/SK_Hilang_xxx.pdf'
            if (Str::startsWith($permohonan->file_hasil_akhir, Storage::url(''))) {
                 $pathRelativeToPublicDisk = substr($permohonan->file_hasil_akhir, strlen(Storage::url('')));
            } else if (Str::startsWith($permohonan->file_hasil_akhir, '/storage/')) { // Fallback jika Storage::url('') tidak cocok
                 $pathRelativeToPublicDisk = substr($permohonan->file_hasil_akhir, strlen('/storage/'));
            } else {
                 // Jika format tidak sesuai, ini adalah error pada saat penyimpanan path
                 Log::error('[KK Hilang API - Download] Format file_hasil_akhir tidak dikenal: ' . $permohonan->file_hasil_akhir . ' untuk ID: ' . $id);
                 return response()->json(['message' => 'Format path file hasil akhir tidak dikenal.'], 400);
            }


            if (Storage::disk('public')->exists($pathRelativeToPublicDisk)) {
                Log::info('[KK Hilang API - Download] File ditemukan: ' . $pathRelativeToPublicDisk . ' pada disk public.');
                return Storage::disk('public')->download($pathRelativeToPublicDisk);
            } else {
                Log::error('[KK Hilang API - Download] File tidak ditemukan di disk public: ' . $pathRelativeToPublicDisk . ' untuk ID: ' . $id .'. Path absolut dicek: ' . Storage::disk('public')->path($pathRelativeToPublicDisk));
            }
        }
        
        Log::warning('[KK Hilang API - Download] File hasil akhir tidak tersedia untuk permohonan ID: ' . $id);
        return response()->json(['message' => 'File hasil akhir tidak tersedia untuk permohonan ini.'], 404);
    }
}
