<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKPerkawinan; // Pastikan nama model sesuai
use Illuminate\Http\Request;

class PermohonanSKPerkawinanController extends Controller
{
    /**
     * Menampilkan daftar permohonan SK Perkawinan.
     */
    public function index()
    {
        $data = PermohonananSKPerkawinan::all();
        return view('petugas.pengajuan.sk_nikah.index', compact('data'));
    }

    /**
     * Memverifikasi permohonan SK Perkawinan.
     */
    public function verifikasi($id)
    {
        $data = PermohonananSKPerkawinan::findOrFail($id);
        $data->status = 'diterima';
        $data->save();

        return redirect()->route('permohonan-sk-perkawinan.index')->with('success', 'Permohonan berhasil diverifikasi.');
    }

    /**
     * Menolak permohonan SK Perkawinan.
     */
    public function tolak($id)
    {
        $data = PermohonananSKPerkawinan::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        return redirect()->route('permohonan-sk-perkawinan.index')->with('error', 'Permohonan telah ditolak.');
    }
}
