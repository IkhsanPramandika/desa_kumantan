<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Masyarakat; // Pastikan nama model dan namespace sudah benar
use Illuminate\Validation\Rules\Password as PasswordRules; // Alias untuk menghindari konflik nama

class MasyarakatAuthController extends Controller
{
    /**
     * Registrasi pengguna masyarakat baru.
     * Email akan menjadi penting jika reset password menggunakan email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        Log::info('[MasyarakatAuthController - Register] Menerima request registrasi baru.');
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|digits:16|unique:masyarakat,nik',
            'nama_lengkap' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20|unique:masyarakat,nomor_hp',
            'email' => 'required|string|email|max:255|unique:masyarakat,email', // Email dibuat wajib untuk reset password
            'password' => ['required', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()],
            // Data pribadi lainnya bisa opsional saat registrasi awal
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|in:LAKI-LAKI,PEREMPUAN',
            'alamat_lengkap' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'dusun_atau_lingkungan' => 'nullable|string|max:100',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Jika ada upload KTP saat registrasi
        ]);

        if ($validator->fails()) {
            Log::warning('[MasyarakatAuthController - Register] Validasi gagal.', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $dataToCreate = $validator->validated();
            // Password akan di-hash otomatis oleh mutator di Model Masyarakat jika $casts['password'] = 'hashed'
            // Jika tidak, hash manual: $dataToCreate['password'] = Hash::make($request->password);
            
            $dataToCreate['status_akun'] = 'pending_verification'; // Atau 'active' jika tidak ada verifikasi

            if ($request->hasFile('foto_ktp')) {
                // Simpan ke disk 'public', path relatif akan disimpan di DB
                $pathFotoKtp = $request->file('foto_ktp')->store('masyarakat/foto_ktp', 'public');
                $dataToCreate['foto_ktp'] = $pathFotoKtp;
            }

            $masyarakat = Masyarakat::create($dataToCreate);
            Log::info('[MasyarakatAuthController - Register] Masyarakat baru berhasil dibuat dengan NIK: ' . $masyarakat->nik);

            // Opsional: Kirim notifikasi ke admin/petugas bahwa ada pendaftaran baru

            return response()->json([
                'message' => 'Registrasi berhasil. Akun Anda akan segera diverifikasi oleh petugas desa.',
                'data' => $masyarakat // Opsional: mengembalikan data user yang baru dibuat
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error registrasi masyarakat: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Terjadi kesalahan pada server saat registrasi.'], 500);
        }
    }

    /**
     * Login pengguna masyarakat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        Log::info('[MasyarakatAuthController - Login] Menerima request login.');
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|digits:16',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255', // Opsional: nama perangkat untuk token
        ]);

        if ($validator->fails()) {
            Log::warning('[MasyarakatAuthController - Login] Validasi gagal.', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $masyarakat = Masyarakat::where('nik', $request->nik)->first();

        if (!$masyarakat || !Hash::check($request->password, $masyarakat->password)) {
            Log::warning('[MasyarakatAuthController - Login] NIK atau Password salah untuk NIK: ' . $request->nik);
            return response()->json(['message' => 'NIK atau Password salah.'], 401);
        }

        if ($masyarakat->status_akun === 'pending_verification') {
            Log::warning('[MasyarakatAuthController - Login] Akun masih pending verifikasi untuk NIK: ' . $request->nik);
            return response()->json(['message' => 'Akun Anda masih dalam proses verifikasi oleh petugas desa.'], 403);
        }

        if ($masyarakat->status_akun !== 'active') {
            Log::warning('[MasyarakatAuthController - Login] Akun tidak aktif untuk NIK: ' . $request->nik . '. Status: ' . $masyarakat->status_akun);
            return response()->json(['message' => 'Akun Anda tidak aktif atau telah diblokir. Silakan hubungi petugas desa.'], 403);
        }

        $deviceName = $request->input('device_name', 'api_token_masyarakat_' . Str::random(5));
        $token = $masyarakat->createToken($deviceName)->plainTextToken;
        Log::info('[MasyarakatAuthController - Login] Login berhasil untuk NIK: ' . $request->nik);

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $masyarakat->id,
                'nik' => $masyarakat->nik,
                'nama_lengkap' => $masyarakat->nama_lengkap,
                'nomor_hp' => $masyarakat->nomor_hp,
                'email' => $masyarakat->email,
                'status_akun' => $masyarakat->status_akun,
                // Tambahkan data profil lain yang ingin dikirim ke aplikasi mobile
            ]
        ]);
    }

    /**
     * Logout pengguna masyarakat (menghapus token saat ini).
     * Membutuhkan middleware 'auth:sanctum' pada rute.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            Log::info('[MasyarakatAuthController - Logout] Logout berhasil untuk user ID: ' . $request->user()->id);
            return response()->json(['message' => 'Logout berhasil']);
        } catch (\Exception $e) {
            Log::error('Error logout masyarakat: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal logout, terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * Mendapatkan data profil pengguna masyarakat yang sedang login.
     * Membutuhkan middleware 'auth:sanctum' pada rute.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profil(Request $request): JsonResponse
    {
        // $request->user() akan mengembalikan instance model Masyarakat yang terautentikasi
        Log::info('[MasyarakatAuthController - Profil] Mengambil profil untuk user ID: ' . $request->user()->id);
        return response()->json($request->user());
    }

    /**
     * Memperbarui data profil pengguna masyarakat yang sedang login.
     * Membutuhkan middleware 'auth:sanctum' pada rute.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfil(Request $request): JsonResponse
    {
        $masyarakat = $request->user();
    Log::info('[MasyarakatAuthController - UpdateProfil] Memulai update profil untuk user ID: ' . $masyarakat->id);
    Log::info('[MasyarakatAuthController - UpdateProfil] Raw Request Input:', $request->all()); // <-- TAMBAHKAN BARIS INI
    Log::info('[MasyarakatAuthController - UpdateProfil] Data user dari DB sebelum update:', $masyarakat->toArray()); 
    
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'sometimes|required|string|max:255',
            'nomor_hp' => 'sometimes|required|string|max:20|unique:masyarakat,nomor_hp,' . $masyarakat->id,
            'email' => 'sometimes|required|string|email|max:255|unique:masyarakat,email,' . $masyarakat->id,
            'tempat_lahir' => 'sometimes|nullable|string|max:100',
            'tanggal_lahir' => 'sometimes|nullable|date',
            'jenis_kelamin' => 'sometimes|nullable|string|in:LAKI-LAKI,PEREMPUAN',
            'alamat_lengkap' => 'sometimes|nullable|string',
            'rt' => 'sometimes|nullable|string|max:5',
            'rw' => 'sometimes|nullable|string|max:5',
            'dusun_atau_lingkungan' => 'sometimes|nullable|string|max:100',
            'agama' => 'sometimes|nullable|string|max:50',
            'status_perkawinan' => 'sometimes|nullable|string|max:50',
            'pekerjaan' => 'sometimes|nullable|string|max:100',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning('[MasyarakatAuthController - UpdateProfil] Validasi gagal untuk user ID: ' . $masyarakat->id, $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updateData = $validator->validated();
            Log::info('[MasyarakatAuthController - UpdateProfil] Data yang akan di-update (setelah validasi):', $updateData); // LOG INI PENTING

            // Hapus foto KTP lama jika ada dan file baru diunggah
            if ($request->hasFile('foto_ktp')) {
                if ($masyarakat->foto_ktp && Storage::disk('public')->exists($masyarakat->foto_ktp)) {
                    Storage::disk('public')->delete($masyarakat->foto_ktp);
                    Log::info('[MasyarakatAuthController - UpdateProfil] Foto KTP lama dihapus: ' . $masyarakat->foto_ktp);
                }
                $pathFotoKtp = $request->file('foto_ktp')->store('masyarakat/foto_ktp', 'public');
                $updateData['foto_ktp'] = $pathFotoKtp;
                Log::info('[MasyarakatAuthController - UpdateProfil] Foto KTP baru disimpan ke: ' . $pathFotoKtp);
            }

            // Lakukan update dan tangkap hasilnya
            $isUpdated = $masyarakat->update($updateData);
            Log::info('[MasyarakatAuthController - UpdateProfil] Hasil metode update(): ' . ($isUpdated ? 'TRUE' : 'FALSE')); // LOG INI SANGAT PENTING

            // Ambil data terbaru dari database setelah update
            // Gunakan fresh() untuk memastikan data terbaru dari DB
            $updatedMasyarakat = $masyarakat->fresh();
            Log::info('[MasyarakatAuthController - UpdateProfil] Data user dari DB setelah update (via fresh()):', $updatedMasyarakat->toArray());

            return response()->json([
                'message' => 'Profil berhasil diperbarui.',
                'user' => $updatedMasyarakat // Pastikan ini adalah objek yang fresh
            ]);

        } catch (\Exception $e) {
            Log::error('Error update profil masyarakat ID ' . $masyarakat->id . ': ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Terjadi kesalahan pada server saat memperbarui profil.'], 500);
        }
    }
}