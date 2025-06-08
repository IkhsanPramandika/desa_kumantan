<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKKelahiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PermohonanSKKelahiranController extends Controller
{
    /**
     * Menampilkan daftar permohonan.
     */
    public function index(Request $request)
    {
        $query = PermohonananSKKelahiran::with('masyarakat')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_anak', 'like', "%{$search}%")
                  ->orWhere('nama_ayah', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(10)->withQueryString();
        
        return view('petugas.pengajuan.sk_kelahiran.index', compact('data'));
    }

    /**
     * Menampilkan halaman detail.
     */
    public function show($id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);
        return view('petugas.pengajuan.sk_kelahiran.show', compact('permohonan'));
    }

    /**
     * Memverifikasi permohonan, mengubah status menjadi 'diterima'.
     * Pada alur ini, setelah verifikasi, petugas bisa langsung membuat surat.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);
        if ($permohonan->status !== 'pending') {
            return redirect()->route('petugas.permohonan-sk-kelahiran.show', $id)->with('error', 'Hanya permohonan dengan status "pending" yang bisa diverifikasi.');
        }
        $permohonan->status = 'diterima'; // Status diubah menjadi 'diterima', siap untuk dibuatkan surat.
        $permohonan->save();

        return redirect()->route('petugas.permohonan-sk-kelahiran.show', $permohonan->id)
            ->with('success', 'Permohonan berhasil diverifikasi! Anda sekarang dapat membuat surat.');
    }

    /**
     * Hapus method inputData() karena tidak diperlukan lagi dalam alur ini.
     * Petugas tidak menginput data, hanya men-trigger pembuatan PDF.
     */
    // public function inputData($id) { ... } // Method ini bisa dihapus

    /**
     * PERBAIKAN: Method ini sekarang TIDAK lagi memvalidasi atau menerima input dari petugas.
     * Ia hanya akan mengambil data yang sudah ada dari masyarakat dan membuat PDF.
     */
    public function selesaikan(Request $request, $id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);

        // Hanya izinkan pembuatan surat jika status sudah 'diterima'
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-kelahiran.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi (status: diterima).');
        }

        try {
            // TIDAK ADA LAGI ->fill($request->all())
            // Langsung update status dan tanggal
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();

            // Generate nomor surat jika belum ada
            if (is_null($permohonan->nomor_surat)) {
                $currentYear = now()->year;
                $lastNomorUrut = PermohonananSKKelahiran::whereYear('tanggal_selesai_proses', $currentYear)->max('nomor_urut') ?? 0;
                $permohonan->nomor_urut = $lastNomorUrut + 1;
                $formattedNomorUrut = str_pad($permohonan->nomor_urut, 3, '0', STR_PAD_LEFT);
                $permohonan->nomor_surat = "472.1/{$formattedNomorUrut}/SKK-KMT/" . $this->getRomanMonth(now()->month) . "/" . $currentYear;
            }

            // Generate PDF dari data yang SUDAH ADA di $permohonan
            $pdf = Pdf::loadView('documents.sk_kelahiran', ['permohonan' => $permohonan]);
            
            $fileName = 'SK_Kelahiran_' . Str::slug($permohonan->nama_anak) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_kelahiran/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->save();

            return redirect()->route('petugas.permohonan-sk-kelahiran.show', $id)->with('success', 'Surat Keterangan Kelahiran berhasil dibuat dan disimpan.');
        } catch (\Exception $e) {
            Log::error('[Selesaikan SK Kelahiran] Gagal untuk ID: ' . $id . ' - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses atau membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Menolak permohonan.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonananSKKelahiran::findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        return redirect()->route('petugas.permohonan-sk-kelahiran.index')->with('success', 'Permohonan berhasil ditolak.');
    }

    /**
     * Mengunduh file hasil akhir.
     */
    public function downloadFinal($id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }

    private function getRomanMonth($monthNumber)
    {
        $map = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[intval($monthNumber) - 1] ?? $monthNumber;
    }
}
