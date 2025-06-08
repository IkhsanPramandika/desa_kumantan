<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  // Menerima satu atau lebih role (misal: 'petugas', 'admin')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Cek apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect('login'); // Jika belum login, arahkan ke halaman login
        }

        // 2. Ambil data pengguna yang sedang login
        $user = Auth::user();

        // 3. Loop melalui role yang diizinkan (misal: 'petugas' dari 'role:petugas')
        foreach ($roles as $role) {
            // 4. Cek apakah role pengguna sama dengan role yang diizinkan
            if ($user->role == $role) {
                // Jika cocok, izinkan request untuk melanjutkan
                return $next($request);
            }
        }

        // 5. Jika tidak ada role yang cocok, tolak akses
        // abort(403, 'ANDA TIDAK MEMILIKI AKSES.');
        return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk halaman tersebut.');
    }
}