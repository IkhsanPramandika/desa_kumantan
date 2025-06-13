<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PermohonanKKBaru;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PermohonanKKBaruController extends Controller
{
    /**
     * PERBAIKAN: Menampilkan daftar permohonan dengan filter dan paginasi.
     */
    public function index(Request $request)
    {
        $query = PermohonanKKBaru::with('masyarakat')->latest();

        // 1. Filter berdasarkan kata kunci pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Mencari di relasi masyarakat (nama atau nik)
                $q->whereHas('masyarakat', function($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                });
            });
        }

        // 2. Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Gunakan paginate untuk data yang banyak, dan withQueryString agar filter tetap ada saat pindah halaman
        $data = $query->paginate(10)->withQueryString();
        
        return view('petugas.pengajuan.kk_baru.index', compact('data'));
    }

    /**
     * Menampilkan detail satu permohonan untuk diproses.
     */
    public function show($id)
    {
        $permohonan = PermohonanKKBaru::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.kk_baru.show', compact('permohonan'));
    }

    /**
     * Memverifikasi permohonan.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonanKKBaru::findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();

        return redirect()->route('petugas.permohonan-kk-baru.show', $id)->with('success', 'Permohonan berhasil diverifikasi.');
    }

    /**
     * Menolak permohonan.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonanKKBaru::findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->catatan_penolakan;
        $permohonan->save();
        
        return redirect()->route('petugas.permohonan-kk-baru.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    /**
     * Menyelesaikan permohonan dengan mengunggah file hasil akhir.
     */
    public function selesaikan(Request $request, $id)
    {
        $request->validate(['file_hasil_akhir' => 'required|file|mimes:pdf|max:2048']);
        $permohonan = PermohonanKKBaru::findOrFail($id);

        if ($request->hasFile('file_hasil_akhir')) {
            if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
                Storage::disk('public')->delete($permohonan->file_hasil_akhir);
            }
            
            $path = $request->file('file_hasil_akhir')->store('permohonan_kk_baru/hasil_akhir', 'public');
            $permohonan->file_hasil_akhir = $path;
        }

        $permohonan->status = 'selesai';
        $permohonan->tanggal_selesai_proses = Carbon::now();
        $permohonan->save();

        return redirect()->route('petugas.permohonan-kk-baru.show', $id)->with('success', 'Proses permohonan berhasil diselesaikan.');
    }

    /**
     * Mengunduh file hasil akhir.
     */
    public function downloadFinal($id)
    {
        $permohonan = PermohonanKKBaru::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan di server.');
    }
}
