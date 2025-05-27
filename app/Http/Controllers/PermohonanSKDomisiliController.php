<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKDomisili; // Pastikan nama model sesuai
use Illuminate\Http\Request;

class PermohonanSKDomisiliController extends Controller
{
    /**
     * Menampilkan daftar permohonan SK Domisili.
     */
    public function index()
    {
        $data = PermohonananSKDomisili::all();
        return view('petugas.pengajuan.sk_domisili.index', compact('data'));
    }

    /**
     * Memverifikasi permohonan SK Domisili.
     */
    public function verifikasi($id)
    {
        $data = PermohonananSKDomisili::findOrFail($id);
        $data->status = 'diterima';
        $data->save();

        return redirect()->route('permohonan-sk-domisili.index')->with('success', 'Permohonan berhasil diverifikasi.');
    }

    /**
     * Menolak permohonan SK Domisili.
     */
    public function tolak($id)
    {
        $data = PermohonananSKDomisili::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        return redirect()->route('permohonan-sk-domisili.index')->with('error', 'Permohonan telah ditolak.');
    }
}
