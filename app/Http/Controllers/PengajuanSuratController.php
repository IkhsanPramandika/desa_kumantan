<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanSuratController extends Controller
{
    public function index()
    {
        $pengajuan = PengajuanSurat::where('user_id', Auth::id())->get();
        return view('petugas.pengajuan.index', compact('pengajuan'));
    }

    public function create()
    {
        $layanan = PengajuanSurat::LAYANAN;
        return view('petugas.pengajuan.create', compact('layanan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_layanan' => 'required|in:' . implode(',', array_keys(PengajuanSurat::LAYANAN)),
            'data_tambahan' => 'required|json',
        ]);

        PengajuanSurat::create([
            'user_id' => Auth::id(),
            'jenis_layanan' => $data['jenis_layanan'],
            'data_tambahan' => json_decode($data['data_tambahan'], true),
            'status' => 'pending',
        ]);

        return redirect()->route('petugas.pengajuan.index')->with('success', 'Pengajuan berhasil dikirim!');
    }

    public function show(PengajuanSurat $pengajuan)
    {
        $this->authorize('view', $pengajuan);
        return view('petugas.pengajuan.show', compact('pengajuan'));
    }
}
