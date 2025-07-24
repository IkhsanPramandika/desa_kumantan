<?php
// Lokasi: app/Http/Controllers/Petugas/Dashboard/ProfileController.php

namespace App\Http\Controllers\Petugas\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman form edit profil.
     */
    public function edit()
    {
        // Mengambil data pengguna yang sedang login
        $user = Auth::user();
        return view('petugas.profile.edit', compact('user'));
    }

    /**
     * Memperbarui informasi profil pengguna (nama & email).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Memastikan email unik, kecuali untuk user saat ini
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return redirect()->route('petugas.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('petugas.profile.edit')->with('status', 'password-updated');
    }
}
