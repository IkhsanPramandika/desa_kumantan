<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKTidakMampu; // Pastikan nama model sesuai
use Illuminate\Http\Request;

class PermohonanSKTidakMampuController extends Controller
{
    /**
     * Menampilkan daftar permohonan SK Tidak Mampu.
     */
    public function index()
    {
        $data = PermohonananSKTidakMampu::all();
        return view('petugas.pengajuan.sk_tidak_mampu.index', compact('data'));
    }

    /**
     * Memverifikasi permohonan SK Tidak Mampu.
     */
    public function verifikasi($id)
    {
        $data = PermohonananSKTidakMampu::findOrFail($id);
        $data->status = 'diterima';
        $data->save();

        return redirect()->route('permohonan-sk-tidak-mampu.index')->with('success', 'Permohonan berhasil diverifikasi.');
    }

    /**
     * Menolak permohonan SK Tidak Mampu.
     */
    public function tolak($id)
    {
        $data = PermohonananSKTidakMampu::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        return redirect()->route('permohonan-sk-tidak-mampu.index')->with('error', 'Permohonan telah ditolak.');
    }
}
