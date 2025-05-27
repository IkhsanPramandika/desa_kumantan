<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKUsaha; // Pastikan nama model sesuai
use Illuminate\Http\Request;

class PermohonanSKUsahaController extends Controller
{
    /**
     * Menampilkan daftar permohonan SK Usaha.
     */
    public function index()
    {
        $data = PermohonananSKUsaha::all();
        return view('petugas.pengajuan.sk_usaha.index', compact('data'));
    }

    /**
     * Memverifikasi permohonan SK Usaha.
     */
    public function verifikasi($id)
    {
        $data = PermohonananSKUsaha::findOrFail($id);
        $data->status = 'diterima';
        $data->save();

        return redirect()->route('permohonan-sk-usaha.index')->with('success', 'Permohonan berhasil diverifikasi.');
    }

    /**
     * Menolak permohonan SK Usaha.
     */
    public function tolak($id)
    {
        $data = PermohonananSKUsaha::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        return redirect()->route('permohonan-sk-usaha.index')->with('error', 'Permohonan telah ditolak.');
    }
}
