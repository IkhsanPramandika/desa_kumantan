<?php
// Lokasi: app/Http/Controllers/Petugas/Permohonan/PermohonanLainnyaController.php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanLainnya;
use App\Notifications\StatusPermohonanDiperbarui;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanLainnyaController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanLainnya::with('masyarakat')->latest();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('masyarakat', fn($q) => $q->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"))
                  ->orWhere('judul_permohonan', 'like', "%{$search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // [FITUR BARU] Menambahkan fitur jumlah data per halaman
        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage)->withQueryString();

        return view('petugas.pengajuan.permohonan_lainnya.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanLainnya::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.permohonan_lainnya.show', compact('permohonan'));
    }

    /**
     * [PERBAIKAN] Fungsi tolak diubah untuk alur revisi.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        
        $permohonan = PermohonanLainnya::with('masyarakat')->findOrFail($id);

        // Mengubah status menjadi 'membutuhkan_revisi'
        $permohonan->status = 'membutuhkan_revisi';
        
        // Menyimpan catatan penolakan dari petugas
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();

        // Mengirim notifikasi ke pengguna bahwa permohonan perlu direvisi
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        
        return redirect()->route('petugas.permohonan-lainnya.show', $id)
                         ->with('success', 'Permohonan telah dikembalikan kepada pengguna untuk direvisi.');
    }


    public function createSurat($id)
    {
        $permohonan = PermohonanLainnya::findOrFail($id);
        // [PERBAIKAN] Izinkan membuat surat jika statusnya pending atau sudah direvisi
        if (!in_array($permohonan->status, ['pending', 'membutuhkan_revisi'])) {
             return redirect()->route('petugas.permohonan-lainnya.show', $id)->with('error', 'Surat tidak dapat dibuat untuk permohonan dengan status ini.');
        }
        return view('petugas.pengajuan.permohonan_lainnya.create_surat', compact('permohonan'));
    }

    public function generateSurat(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'judul_surat_final' => 'required|string|max:255',
            'konten_final_html' => 'required|string',
        ]);
        
        $permohonan = PermohonanLainnya::with('masyarakat')->findOrFail($id);
        
        $permohonan->nomor_surat = $request->nomor_surat;
        $permohonan->judul_surat_final = $request->judul_surat_final;
        $permohonan->konten_final_html = $request->konten_final_html;
        
        $pdf = Pdf::loadView('documents.surat_lainnya', ['permohonan' => $permohonan]);
        $fileName = 'Surat_Keterangan_' . Str::slug($permohonan->judul_surat_final) . '_' . $permohonan->id . '.pdf';
        $path = 'permohonan_lainnya/hasil_akhir/' . $fileName;
        Storage::disk('public')->put($path, $pdf->output());

        $permohonan->file_hasil_akhir = $path;
        $permohonan->status = 'selesai';
        $permohonan->tanggal_selesai_proses = Carbon::now();
        $permohonan->save();

        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        return redirect()->route('petugas.permohonan-lainnya.show', $id)->with('success', 'Surat berhasil dibuat dan permohonan diselesaikan.');
    }

    public function downloadFinal($id)
    {
        $permohonan = PermohonanLainnya::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}
