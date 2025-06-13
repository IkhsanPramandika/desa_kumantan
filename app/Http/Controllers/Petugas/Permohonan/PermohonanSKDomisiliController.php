<?php

namespace App\Http\Controllers\Petugas\Permohonan;
use App\Http\Controllers\Controller;

use App\Models\PermohonanSKDomisili;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PermohonanSKDomisiliController extends Controller
{
    /**
     * Menampilkan daftar permohonan untuk petugas.
     */
    public function index(Request $request)
    {
        $query = PermohonanSKDomisili::with('masyarakat')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pemohon_atau_lembaga', 'like', "%{$search}%")
                  ->orWhere('nik_pemohon', 'like', "%{$search}%")
                  ->orWhereHas('masyarakat', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(10)->withQueryString();
        
        return view('petugas.pengajuan.sk_domisili.index', compact('data'));
    }

    /**
     * Menampilkan halaman detail untuk verifikasi oleh petugas.
     */
    public function show($id)
    {
        $permohonan = PermohonanSKDomisili::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_domisili.show', compact('permohonan'));
    }

    /**
     * Memverifikasi, membuat PDF, dan menyelesaikan permohonan dalam satu klik.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonanSKDomisili::findOrFail($id);
        
        if ($permohonan->status !== 'pending') {
            return redirect()->route('petugas.permohonan-sk-domisili.show', $id)
                ->with('error', 'Hanya permohonan dengan status "pending" yang bisa diproses.');
        }

        try {
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();

            if (is_null($permohonan->nomor_surat)) {
                $currentYear = now()->year;
                $lastNomorUrut = PermohonanSKDomisili::whereYear('tanggal_selesai_proses', $currentYear)->max('nomor_urut') ?? 0;
                $permohonan->nomor_urut = $lastNomorUrut + 1;
                $formattedNomorUrut = str_pad($permohonan->nomor_urut, 3, '0', STR_PAD_LEFT);
                $permohonan->nomor_surat = "NOMOR/SKD/ANDA/" . $formattedNomorUrut . "/" . $currentYear; // Sesuaikan format Anda
            }

            // Generate PDF dari data yang sudah ada
            $pdf = Pdf::loadView('documents.sk_domisili', ['permohonan' => $permohonan]);
            $fileName = 'SK_Domisili_' . Str::slug($permohonan->nama_pemohon_atau_lembaga) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_domisili/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path; // Simpan path internal
            $permohonan->save();

            return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('success', 'Surat Keterangan Domisili berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Domisili untuk ID {$id}: " . $e->getMessage());
            // Jika gagal, kembalikan status agar bisa dicoba lagi
            $permohonan->status = 'pending';
            $permohonan->save();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Menolak permohonan.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonanSKDomisili::findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    /**
     * Mengunduh file hasil akhir untuk petugas (melalui route yang aman).
     */
    public function downloadFinal($id)
    {
        $permohonan = PermohonanSKDomisili::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan di server.');
    }
    
    /**
     * Metode untuk mengunduh file secara publik (misal: untuk QR Code).
     */
    public function publicDownload($id)
    {
        $permohonan = PermohonanSKDomisili::where('id', $id)->where('status', 'selesai')->firstOrFail();
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        abort(404, 'Dokumen tidak ditemukan atau belum selesai diproses.');
    }
}
