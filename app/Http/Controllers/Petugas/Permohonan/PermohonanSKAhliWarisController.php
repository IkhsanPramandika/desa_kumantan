<?php

namespace App\Http\Controllers\Petugas\Permohonan;
use App\Http\Controllers\Controller;

use App\Models\PermohonanSKAhliWaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PermohonanSKAhliWarisController extends Controller
{
    /**
     * Menampilkan daftar permohonan.
     */
    public function index(Request $request)
    {
        $query = PermohonanSKAhliWaris::with('masyarakat')->latest();

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
        $permohonan = PermohonanSKAhliWaris::with('masyarakat')->findOrFail($id);
        
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
        $permohonan = PermohonanSKAhliWaris::findOrFail($id);
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
        $permohonan = PermohonanSKAhliWaris::findOrFail($id);
        
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)
                ->with('error', 'Surat hanya bisa dibuat untuk permohonan yang telah diverifikasi.');
        }

        try {
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();

            // [PENINGKATAN] Generate nomor surat hanya jika belum ada.
            if (is_null($permohonan->nomor_surat)) {
                $now = Carbon::now();
                $romawiBulan = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                
                // Mengambil nomor urut terakhir di tahun ini, jika tidak ada, mulai dari 0.
                $lastNomorUrut = PermohonanSKAhliWaris::whereYear('tanggal_selesai_proses', $now->year)->max('nomor_urut') ?? 0;
                
                $permohonan->nomor_urut = $lastNomorUrut + 1;
                
                // [PENINGKATAN] Format nomor surat yang lebih baik. Sesuaikan dengan standar desa Anda.
                // Contoh: 474/001/SKAW/DS-KMTN/VI/2025
                $nomorSurat = sprintf("474/%03d/SKAW/DS-KMTN/%s/%d", 
                    $permohonan->nomor_urut,
                    $romawiBulan[$now->month - 1], // Bulan dalam romawi
                    $now->year
                );
                $permohonan->nomor_surat = $nomorSurat;
            }

            // Data yang akan dikirim ke view PDF
            $dataForPdf = ['permohonan' => $permohonan];

            $pdf = Pdf::loadView('documents.sk_ahli_waris', $dataForPdf);
            
            $fileName = 'SK-Ahli-Waris_' . Str::slug($permohonan->nama_pewaris) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan/sk_ahli_waris/' . $fileName;
            
            // Simpan PDF ke storage
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->save();

            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('success', 'Surat Keterangan Ahli Waris berhasil dibuat dan disimpan.');
        
        } catch (\Exception $e) {
            // [PENINGKATAN] Logging yang lebih detail untuk debugging
            Log::error("Gagal membuat PDF SK Ahli Waris untuk ID {$id}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Ini akan memberikan jejak lengkap error
            ]);
            
            // [PENINGKATAN] Pesan error yang lebih membantu saat mode debug aktif
            $errorMessage = config('app.debug') 
                ? 'Terjadi kesalahan saat membuat dokumen: ' . $e->getMessage()
                : 'Terjadi kesalahan saat membuat dokumen. Silakan hubungi administrator.';

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Menolak permohonan.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonanSKAhliWaris::findOrFail($id);
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
        $permohonan = PermohonanSKAhliWaris::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan di server.');
    }
}
