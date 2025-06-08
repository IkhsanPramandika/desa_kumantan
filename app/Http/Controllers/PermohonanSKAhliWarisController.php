<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKAhliWaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PermohonanSKAhliWarisController extends Controller
{
    /**
     * Menampilkan daftar permohonan.
     */
    public function index(Request $request)
    {
        $query = PermohonananSKAhliWaris::with('masyarakat')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pewaris', 'like', "%{$search}%")
                  ->orWhereHas('masyarakat', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(10)->withQueryString();
        
        return view('petugas.pengajuan.sk_ahli_waris.index', compact('data'));
    }

    /**
     * Menampilkan halaman detail untuk verifikasi.
     */
    public function show($id)
    {
        $permohonan = PermohonananSKAhliWaris::with('masyarakat')->findOrFail($id);
        
        // ====================================================================
        // PERBAIKAN: Blok kode manual json_decode dihapus.
        // Properti $casts di dalam Model sudah secara otomatis mengubah
        // 'daftar_ahli_waris' dari JSON string menjadi array PHP.
        // ====================================================================
        
        return view('petugas.pengajuan.sk_ahli_waris.show', compact('permohonan'));
    }

    /**
     * Memverifikasi permohonan, mengubah status menjadi 'diterima'.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonananSKAhliWaris::findOrFail($id);
        if ($permohonan->status !== 'pending') {
            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)
                ->with('error', 'Hanya permohonan dengan status "pending" yang bisa diverifikasi.');
        }
        $permohonan->status = 'diterima';
        $permohonan->save();

        return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $permohonan->id)
            ->with('success', 'Permohonan berhasil diverifikasi! Anda sekarang dapat membuat surat.');
    }

    /**
     * Membuat PDF dari data yang sudah ada (diinput masyarakat) dan menyelesaikan permohonan.
     */
    public function selesaikan(Request $request, $id)
    {
        $permohonan = PermohonananSKAhliWaris::findOrFail($id);
        
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)
                ->with('error', 'Surat hanya bisa dibuat untuk permohonan yang telah diverifikasi.');
        }

        try {
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();

            if (is_null($permohonan->nomor_surat)) {
                $currentYear = now()->year;
                $lastNomorUrut = PermohonananSKAhliWaris::whereYear('tanggal_selesai_proses', $currentYear)->max('nomor_urut') ?? 0;
                $permohonan->nomor_urut = $lastNomorUrut + 1;
                $permohonan->nomor_surat = "NOMOR/SKAW/ANDA/" . $permohonan->nomor_urut; // Sesuaikan format Anda
            }

            // Karena $casts sudah bekerja, kita bisa langsung meneruskan data ke view PDF.
            $pdf = Pdf::loadView('documents.sk_ahli_waris', ['permohonan' => $permohonan]);
            $fileName = 'SK_Ahli_Waris_' . Str::slug($permohonan->nama_pewaris) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_ahli_waris/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->save();

            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('success', 'Surat Keterangan Ahli Waris berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Ahli Waris untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen.');
        }
    }

    /**
     * Menolak permohonan.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonananSKAhliWaris::findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    /**
     * Mengunduh file hasil akhir.
     */
    public function downloadFinal($id)
    {
        $permohonan = PermohonananSKAhliWaris::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan di server.');
    }
}
