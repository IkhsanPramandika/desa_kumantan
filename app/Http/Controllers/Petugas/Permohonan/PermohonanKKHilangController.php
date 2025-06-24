<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanKKHilang;
use App\Notifications\PermohonanStatusUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PermohonanKKHilangController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanKKHilang::with('masyarakat')->latest();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('masyarakat', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $data = $query->paginate(10)->withQueryString();
        return view('petugas.pengajuan.kk_hilang.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanKKHilang::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.kk_hilang.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        $permohonan = PermohonanKKHilang::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();
        
        $title = "Permohonan Diverifikasi";
        $message = "Permohonan KK Hilang Anda (#{$permohonan->id}) telah kami verifikasi.";
        Notification::send($permohonan->masyarakat, new PermohonanStatusUpdated($permohonan, $title, $message, '#'));

        return redirect()->route('petugas.permohonan-kk-hilang.show', $id)->with('success', 'Permohonan berhasil diverifikasi.');
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonanKKHilang::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->catatan_penolakan;
        $permohonan->save();
        
        $title = "Permohonan Ditolak";
        $message = "Maaf, permohonan KK Hilang Anda (#{$permohonan->id}) kami tolak. Alasan: " . $request->catatan_penolakan;
        Notification::send($permohonan->masyarakat, new PermohonanStatusUpdated($permohonan));
        
        return redirect()->route('petugas.permohonan-kk-hilang.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    public function selesaikan(Request $request, $id)
    {
        $request->validate(['file_hasil_akhir' => 'required|file|mimes:pdf|max:2048']);
        $permohonan = PermohonanKKHilang::with('masyarakat')->findOrFail($id);

        if ($request->hasFile('file_hasil_akhir')) {
            if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
                Storage::disk('public')->delete($permohonan->file_hasil_akhir);
            }
            $path = $request->file('file_hasil_akhir')->store('permohonan_kk_hilang/hasil_akhir', 'public');
            $permohonan->file_hasil_akhir = $path;
        }

        $permohonan->status = 'selesai';
        $permohonan->tanggal_selesai_proses = Carbon::now();
        $permohonan->save();

        $title = "Permohonan Selesai";
        $message = "Selamat! Permohonan KK Hilang Anda (#{$permohonan->id}) telah selesai diproses.";
        Notification::send($permohonan->masyarakat, new PermohonanStatusUpdated($permohonan, $title, $message, '#'));

        return redirect()->route('petugas.permohonan-kk-hilang.show', $id)->with('success', 'Proses permohonan berhasil diselesaikan.');
    }

    public function downloadFinal($id)
    {
        
        $permohonan = PermohonanKKHilang::where('status', 'selesai')->findOrFail($id);

        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }

        // Redirect ini sudah benar jika file tidak ada
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan atau permohonan belum selesai.');

    }
}
