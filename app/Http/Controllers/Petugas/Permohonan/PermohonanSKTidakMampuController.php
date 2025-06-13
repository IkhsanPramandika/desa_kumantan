<?php

namespace App\Http\Controllers\Petugas\Permohonan;
use App\Http\Controllers\Controller;

use App\Models\PermohonanSKTidakMampu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PermohonanSKTidakMampuController extends Controller
{
    /**
     * Menampilkan daftar permohonan untuk petugas.
     */
    public function index(Request $request)
    {
        $query = PermohonanSKTidakMampu::with('masyarakat')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nik_pemohon', 'like', "%{$search}%")
                  ->orWhere('nama_terkait', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(10)->withQueryString();
        
        return view('petugas.pengajuan.sk_tidak_mampu.index', compact('data'));
    }

    /**
     * Menampilkan halaman detail untuk verifikasi oleh petugas.
     */
    public function show($id)
    {
        $permohonan = PermohonanSKTidakMampu::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_tidak_mampu.show', compact('permohonan'));
    }

    /**
     * Memverifikasi, membuat PDF, dan menyelesaikan permohonan dalam satu klik.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonanSKTidakMampu::findOrFail($id);
        
        if ($permohonan->status !== 'pending') {
            return redirect()->route('petugas.permohonan-sk-tidak-mampu.show', $id)
                ->with('error', 'Hanya permohonan dengan status "pending" yang bisa diproses.');
        }

        try {
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();

            if (is_null($permohonan->nomor_surat)) {
                $currentYear = now()->year;
                $lastNomorUrut = PermohonanSKTidakMampu::whereYear('tanggal_selesai_proses', $currentYear)->max('nomor_urut') ?? 0;
                $permohonan->nomor_urut = $lastNomorUrut + 1;
                $formattedNomorUrut = str_pad($permohonan->nomor_urut, 3, '0', STR_PAD_LEFT);
                $permohonan->nomor_surat = "470/{$formattedNomorUrut}/SKTM-KMT/" . now()->month . "/" . $currentYear; // Sesuaikan format Anda
            }

            // Generate PDF dari data yang sudah ada di $permohonan
            $pdf = Pdf::loadView('documents.sk_tidak_mampu', ['permohonan' => $permohonan]);
            $fileName = 'SKTM_' . Str::slug($permohonan->nama_pemohon) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_tidak_mampu/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path; // Simpan path internal
            $permohonan->save();

            return redirect()->route('petugas.permohonan-sk-tidak-mampu.show', $id)->with('success', 'Surat Keterangan Tidak Mampu berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SKTM untuk ID {$id}: " . $e->getMessage());
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
        $permohonan = PermohonanSKTidakMampu::findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        return redirect()->route('petugas.permohonan-sk-tidak-mampu.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    /**
     * Mengunduh file hasil akhir untuk petugas.
     */
    public function downloadFinal($id)
    {
        $permohonan = PermohonanSKTidakMampu::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan di server.');
    }

    /**
     * Mengunduh file secara publik.
     */
    public function publicDownload($id)
    {
        $permohonan = PermohonanSKTidakMampu::where('id', $id)->where('status', 'selesai')->firstOrFail();
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        abort(404, 'Dokumen tidak ditemukan atau belum selesai diproses.');
    }
}
