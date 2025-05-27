<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKAhliWaris; // Asumsi nama model ini
use Illuminate\Http\Request;

class PermohonanSkAhliWarisController extends Controller
{
    /**
     * Menampilkan daftar permohonan SK Ahli Waris.
     */
    public function index()
    {
        $data = PermohonananSKAhliWaris::all();
        return view('petugas.pengajuan.sk_ahli_waris.index', compact('data'));
    }

    /**
     * Memverifikasi permohonan SK Ahli Waris.
     */
    public function verifikasi($id)
    {
        $data = PermohonananSKAhliWaris::findOrFail($id);
        $data->status = 'diterima';
        $data->save();

        return redirect()->route('permohonan-sk-ahli-waris.index')->with('success', 'Permohonan berhasil diverifikasi.');
    }

    /**
     * Menolak permohonan SK Ahli Waris.
     */
    public function tolak($id)
    {
        $data = PermohonananSKAhliWaris::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        return redirect()->route('permohonan-sk-ahli-waris.index')->with('error', 'Permohonan telah ditolak.');
    }
}