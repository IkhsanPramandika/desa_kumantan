<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Masyarakat; // Pastikan nama model benar
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;

class MasyarakatResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|exists:masyarakat,email', // Kredensial utama adalah email
            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Menggunakan broker 'masyarakat_reset'
        $status = Password::broker('masyarakat_reset')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // $user di sini adalah instance dari Masyarakat
                $user->forceFill([
                    'password' => $password // Model akan otomatis hash karena ada di $casts
                ])->save();

                // Hapus semua token Sanctum user tersebut agar login ulang jika sesi aktif di perangkat lain
                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => __('Password Anda telah berhasil direset.'), 'status_code' => 200], 200);
        }

        // Jika token tidak valid atau email tidak cocok
        return response()->json(['message' => __('Token reset password tidak valid atau email tidak cocok.'), 'status_code' => 400, 'status_key' => $status], 400);
    }
}
