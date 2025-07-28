<?php

namespace App\Http\Controllers\KepalaDesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman form edit profil untuk Kepala Desa.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Mengambil data pengguna (Kepala Desa) yang sedang login
        $user = Auth::user();
        
        // Mengarahkan ke view yang sesuai untuk Kepala Desa
        return view('kepala_desa.profile.edit', compact('user'));
    }

    /**
     * Memperbarui informasi profil Kepala Desa (nama & email).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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

        // Redirect kembali ke halaman edit profil Kepala Desa dengan pesan sukses
        return redirect()->route('kepala_desa.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui password Kepala Desa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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

        // Redirect kembali ke halaman edit profil Kepala Desa dengan pesan sukses
        return redirect()->route('kepala_desa.profile.edit')->with('status', 'password-updated');
    }
}
