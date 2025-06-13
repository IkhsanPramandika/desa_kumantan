<?php

namespace App\Http\Controllers\Api\Permohonan; // Namespace controller

use App\Models\User;
use App\Notifications\PermohonanKKBaruMasuk;
use Illuminate\Support\Facades\Notification;
    
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; 
use Illuminate\Support\Facades\Log; 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\Permohonan\kk_baru\StoreKKBaruRequest; 
use App\Models\PermohonanKKBaru; // Pastikan nama model ini benar
use App\Http\Resources\Permohonan\kk_baru\PermohonanKKBaruResource; 

class KKBaruApiController extends Controller 
{
    protected $attachmentBaseDir = 'permohonan_kk_baru_attachments';

    /**
     * Store a newly created resource in storage.
     */
    /**
 * Menyimpan permohonan baru dari aplikasi mobile.
 */
public function store(StoreKKBaruRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $uploadedFilePaths = [];

        try {
            $dbData = $validatedData;
            $dbData['masyarakat_id'] = $user->id;
            $dbData['status'] = 'pending';

            // Proses upload file (sesuaikan dengan field Anda)
            $fileFields = ['file_pengantar_rt_rw', 'file_kk_lama', 'file_ktp', 'file_buku_nikah'];
            $basePath = 'permohonan_kk_baru/lampiran';

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($basePath, 'public');
                    $dbData[$field] = $path;
                    $uploadedFilePaths[] = $path;
                }
            }

            // Buat entri permohonan di database
            $permohonan = PermohonanKKBaru::create($dbData);

            // ====================================================================
            // [MODIFIKASI] KIRIM NOTIFIKASI KE SEMUA PETUGAS
            // ====================================================================
            try {
                // Asumsikan semua petugas memiliki role 'petugas'
                $semuaPetugas = User::where('role', 'petugas')->get();

                if ($semuaPetugas->isNotEmpty()) {
                    // Mengirim notifikasi ke setiap petugas yang ditemukan
                    Notification::send($semuaPetugas, new PermohonanKKBaruMasuk($permohonan));
                } else {
                    Log::warning('Tidak ada user dengan role "petugas" yang ditemukan untuk dikirim notifikasi.');
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi KK Baru: ' . $e->getMessage());
            }
            // ====================================================================

            return (new PermohonanKKBaruResource($permohonan))
                ->additional(['message' => 'Permohonan KK Baru berhasil diajukan.'])
                ->response()
                ->setStatusCode(201);

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

    // Method helper tidak lagi digunakan secara langsung di loop store di atas,
    // karena validasi 'required' sudah ditangani oleh FormRequest.
    // Namun, jika Anda masih ingin menggunakannya di tempat lain, ini versi yang diperbaiki:
    private function getRulesForStoreKKBaru(): array
    {
        return (new StoreKKBaruRequest())->rules();
    }

    private function isFieldRequiredInStoreKKBaru(string $fieldName): bool
    {
        $rulesDefinition = $this->getRulesForStoreKKBaru();
        if (!isset($rulesDefinition[$fieldName])) {
            return false; // Tidak ada rule, anggap tidak required
        }

        $rulesForField = $rulesDefinition[$fieldName];

        if (is_string($rulesForField)) {
            // Jika rule adalah string, pecah menjadi array
            $rulesArray = explode('|', $rulesForField);
            return in_array('required', $rulesArray);
        } elseif (is_array($rulesForField)) {
            // Jika rule sudah array (misalnya berisi objek Rule)
            foreach ($rulesForField as $rule) {
                if ((is_string($rule) && $rule === 'required') || 
                    (is_object($rule) && strtolower(class_basename($rule)) === 'required')) {
                    return true;
                }
                // Anda bisa menambahkan pengecekan untuk objek Rule lain seperti RequiredIf, dll.
            }
        }
        return false;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
                                ->latest()
                                ->paginate(10);
        
        return PermohonanKKBaruResource::collection($permohonan)
                                 ->additional(['message' => 'Daftar permohonan KK Baru berhasil diambil.'])
                                 ->response(); 
    }

    public function show(Request $request, $id): JsonResponse 
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
                                      ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan atau Anda tidak berhak mengaksesnya.'], 404);
        }
            
        return (new PermohonanKKBaruResource($permohonan))
                       ->additional(['message' => 'Detail permohonan berhasil diambil.'])
                       ->response(); 
    }

    public function downloadHasil(Request $request, $id): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $permohonan = PermohonanKKBaru::where('masyarakat_id', $user->id)
                                      ->where('status', 'selesai') // Hanya jika sudah selesai
                                      ->find($id);
        
        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan, belum selesai, atau Anda tidak berhak mengaksesnya.'], 404);
        }

        if ($permohonan->file_hasil_akhir) {
            // file_hasil_akhir disimpan sebagai URL publik, misal: /storage/dokumen_hasil_kk/file.pdf
            // Ubah menjadi path relatif terhadap root disk 'public' ('dokumen_hasil_kk/file.pdf')
            
            $urlPath = parse_url($permohonan->file_hasil_akhir, PHP_URL_PATH);
            $pathRelativeToPublicDisk = Str::startsWith($urlPath, '/storage/') ? substr($urlPath, strlen('/storage/')) : $urlPath;

            Log::info('[KK Baru API - Download] Mencoba unduh file. Path di disk public: ' . $pathRelativeToPublicDisk . ' untuk ID: ' . $id);
            
            if (Storage::disk('public')->exists($pathRelativeToPublicDisk)) {
                Log::info('[KK Baru API - Download] File ditemukan: ' . $pathRelativeToPublicDisk . '. Mengunduh...');
                return Storage::disk('public')->download($pathRelativeToPublicDisk);
            } else {
                Log::error('[KK Baru API - Download] File TIDAK ditemukan di disk public: ' . $pathRelativeToPublicDisk . '. URL di DB: ' . $permohonan->file_hasil_akhir);
            }
        }
        
        Log::warning('[KK Baru API - Download] File hasil akhir tidak tersedia untuk permohonan KK Baru ID: ' . $id);
        return response()->json(['message' => 'File hasil akhir tidak tersedia untuk permohonan ini.'], 404);
    }
}
