<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\Masyarakat; // Pastikan nama model benar

class MasyarakatForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:masyarakat,email', // Validasi berdasarkan email
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Mengirim link reset password menggunakan broker 'masyarakat_reset'
        // Laravel akan mencari user berdasarkan email dari request
        $status = Password::broker('masyarakat_reset')->sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status), 'status_code' => 200], 200);
        }

        // Jika email tidak ditemukan atau ada error lain
        return response()->json(['message' => __($status), 'status_code' => 400], 400);
    }
}
