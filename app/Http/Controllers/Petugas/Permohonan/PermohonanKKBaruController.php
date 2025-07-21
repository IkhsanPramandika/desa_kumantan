<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanKKBaru;
use App\Notifications\StatusPermohonanDiperbarui;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanKKBaruController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanKKBaru::with('masyarakat')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('masyarakat', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 10); 

        // [PERBAIKAN] Mengubah nama variabel dari $data menjadi $permohonan
        $permohonan = $query->paginate($perPage)->withQueryString();
        
        // Sekarang variabel 'permohonan' sudah ada dan bisa dikirim
        return view('petugas.pengajuan.kk_baru.index', compact('permohonan'));
    }

    public function show($id)
    {
        $permohonan = PermohonanKKBaru::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.kk_baru.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        $permohonan = PermohonanKKBaru::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();
        
        // [STANDARISASI] Menggunakan kelas notifikasi yang benar (hanya butuh objek permohonan).
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

        return redirect()->route('petugas.permohonan-kk-baru.show', $id)->with('success', 'Permohonan berhasil diverifikasi.');
    }

   public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        
        $permohonan = PermohonanKKBaru::with('masyarakat')->findOrFail($id);

        $permohonan->status = 'membutuhkan_revisi';
        $permohonan->catatan_penolakan = $request->catatan_penolakan;
        $permohonan->save();
        
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        
        return redirect()->route('petugas.permohonan-kk-baru.show', $id)
                         ->with('success', 'Permohonan telah dikembalikan kepada pengguna untuk direvisi.');
    }

    public function selesaikan(Request $request, $id)
    {
        $request->validate(['file_hasil_akhir' => 'required|file|mimes:pdf|max:2048']);
        
        $permohonan = PermohonanKKBaru::with('masyarakat')->findOrFail($id);

        if ($request->hasFile('file_hasil_akhir')) {
            if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
                Storage::disk('public')->delete($permohonan->file_hasil_akhir);
            }

            $file = $request->file('file_hasil_akhir');
            $namaPemohonSlug = Str::slug($permohonan->masyarakat->nama_lengkap);
            $idPermohonan = $permohonan->id;
            $ekstensi = $file->getClientOriginalExtension();

            $namaFileKustom = "Kartu Keluarga_{$namaPemohonSlug}_{$idPermohonan}.{$ekstensi}";

            $path = $file->storeAs('permohonan_kk_baru/hasil_akhir', $namaFileKustom, 'public');

            $permohonan->file_hasil_akhir = $path;
        }

        $permohonan->status = 'selesai';
        $permohonan->tanggal_selesai_proses = Carbon::now();
        $permohonan->save();

        // [STANDARISASI] Menggunakan kelas notifikasi yang benar.
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

        return redirect()->route('petugas.permohonan-kk-baru.show', $id)->with('success', 'Proses permohonan berhasil diselesaikan.');
    }

    public function downloadFinal($id)
    {
        $permohonan = PermohonanKKBaru::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}
