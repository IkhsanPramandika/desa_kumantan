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
            // [PERBAIKAN] Mengubah 'required' menjadi 'nullable' agar email menjadi opsional
            'email' => 'nullable|string|email|max:255|unique:masyarakat,email',
            'password' => ['required', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()],
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'alamat_lengkap' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'dusun_atau_lingkungan' => 'nullable|string|max:100',
            'agama' => 'nullable|string|in:Islam,Kristen Protestan,Katolik,Hindu,Buddha,Konghucu',
            'status_perkawinan' => 'nullable|string|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pekerjaan' => 'nullable|string|max:100',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning('[MasyarakatAuthController - Register] Validasi gagal.', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $dataToCreate = $validator->validated();
            
            $dataToCreate['status_akun'] = 'pending_verification';

            if ($request->hasFile('foto_ktp')) {
                $pathFotoKtp = $request->file('foto_ktp')->store('masyarakat/foto_ktp', 'public');
                $dataToCreate['foto_ktp'] = $pathFotoKtp;
            }

            $masyarakat = Masyarakat::create($dataToCreate);
            Log::info('[MasyarakatAuthController - Register] Masyarakat baru berhasil dibuat dengan NIK: ' . $masyarakat->nik);

            return response()->json([
                'message' => 'Registrasi berhasil. Akun Anda akan segera diverifikasi oleh petugas desa.',
                'data' => $masyarakat
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
            'device_name' => 'nullable|string|max:255',
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
            ]
        ]);
    }

    /**
     * Logout pengguna masyarakat (menghapus token saat ini).
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profil(Request $request): JsonResponse
    {
        Log::info('[MasyarakatAuthController - Profil] Mengambil profil untuk user ID: ' . $request->user()->id);
        return response()->json($request->user());
    }

    /**
     * Memperbarui data profil pengguna masyarakat yang sedang login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfil(Request $request): JsonResponse
    {
        $masyarakat = $request->user();
        Log::info('[MasyarakatAuthController - UpdateProfil] Memulai update profil untuk user ID: ' . $masyarakat->id);
        
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'sometimes|required|string|max:255',
            'nomor_hp' => 'sometimes|required|string|max:20|unique:masyarakat,nomor_hp,' . $masyarakat->id,
            // [PERBAIKAN] Mengubah 'required' menjadi 'nullable' agar konsisten
            'email' => 'sometimes|nullable|string|email|max:255|unique:masyarakat,email,' . $masyarakat->id,
            'tempat_lahir' => 'sometimes|nullable|string|max:100',
            'tanggal_lahir' => 'sometimes|nullable|date',
            'jenis_kelamin' => 'sometimes|nullable|string|in:Laki-laki,Perempuan',
            'alamat_lengkap' => 'sometimes|nullable|string',
            'rt' => 'sometimes|nullable|string|max:5',
            'rw' => 'sometimes|nullable|string|max:5',
            'dusun_atau_lingkungan' => 'sometimes|nullable|string|max:100',
            'agama' => 'sometimes|nullable|string|in:Islam,Kristen Protestan,Katolik,Hindu,Buddha,Konghucu',
            'status_perkawinan' => 'sometimes|nullable|string|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pekerjaan' => 'sometimes|nullable|string|max:100',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning('[MasyarakatAuthController - UpdateProfil] Validasi gagal untuk user ID: ' . $masyarakat->id, $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updateData = $validator->validated();
            
            if ($request->hasFile('foto_ktp')) {
                if ($masyarakat->foto_ktp && Storage::disk('public')->exists($masyarakat->foto_ktp)) {
                    Storage::disk('public')->delete($masyarakat->foto_ktp);
                }
                $pathFotoKtp = $request->file('foto_ktp')->store('masyarakat/foto_ktp', 'public');
                $updateData['foto_ktp'] = $pathFotoKtp;
            }

            $masyarakat->update($updateData);
            
            $updatedMasyarakat = $masyarakat->fresh();

            return response()->json([
                'message' => 'Profil berhasil diperbarui.',
                'user' => $updatedMasyarakat
            ]);

        } catch (\Exception $e) {
            Log::error('Error update profil masyarakat ID ' . $masyarakat->id . ': ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Terjadi kesalahan pada server saat memperbarui profil.'], 500);
        }
    }
    
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'new_password'     => ['required', 'string', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password saat ini tidak cocok.'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah.']);
    }
}
