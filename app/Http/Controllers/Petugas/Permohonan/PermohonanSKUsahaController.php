<?php

namespace App\Http\Controllers\Petugas\Permohonan;
use App\Http\Controllers\Controller;

use App\Models\PermohonanSKUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // <-- Penting untuk API

class PermohonanSKUsahaController extends Controller
{
    // ... method index, show, verifikasi, tolak ... (tetap sama)

    /**
     * Menampilkan daftar permohonan untuk petugas.
     */
    public function index(Request $request)
    {
        $query = PermohonanSKUsaha::with('masyarakat')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nik_pemohon', 'like', "%{$search}%")
                  ->orWhere('nama_usaha', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(10)->withQueryString();
        
        return view('petugas.pengajuan.sk_usaha.index', compact('data'));
    }

    /**
     * Menampilkan halaman detail untuk verifikasi oleh petugas.
     */
    public function show($id)
    {
        $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_usaha.show', compact('permohonan'));
    }

    /**
     * Memverifikasi, membuat PDF, dan menyelesaikan permohonan dalam satu klik.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonanSKUsaha::findOrFail($id);
        
        if ($permohonan->status !== 'pending') {
            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)
                ->with('error', 'Hanya permohonan dengan status "pending" yang bisa diproses.');
        }

        try {
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();

            if (is_null($permohonan->nomor_surat)) {
                $currentYear = now()->year;
                $lastNomorUrut = PermohonanSKUsaha::whereYear('tanggal_selesai_proses', $currentYear)->max('nomor_urut') ?? 0;
                $permohonan->nomor_urut = $lastNomorUrut + 1;
                $formattedNomorUrut = str_pad($permohonan->nomor_urut, 3, '0', STR_PAD_LEFT);
                $permohonan->nomor_surat = "503/{$formattedNomorUrut}/SKU-KMT/" . now()->month . "/" . $currentYear;
            }

            $pdf = Pdf::loadView('documents.sk_usaha', ['permohonan' => $permohonan]);
            $fileName = 'SK_Usaha_' . Str::slug($permohonan->nama_usaha) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_usaha/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->save();

            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('success', 'Surat Keterangan Usaha berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Usaha untuk ID {$id}: " . $e->getMessage());
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
        $permohonan = PermohonanSKUsaha::findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    /**
     * Mengunduh file hasil akhir untuk petugas (melalui route yang aman).
     */
    public function downloadFinal($id)
    {
        $permohonan = PermohonanSKUsaha::findOrFail($id);
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
        $permohonan = PermohonanSKUsaha::where('id', $id)->where('status', 'selesai')->firstOrFail();
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        abort(404, 'Dokumen tidak ditemukan atau belum selesai diproses.');
    }

    /**
     * METODE BARU: Mengunduh file untuk pengguna aplikasi mobile yang sudah terotentikasi.
     */
    public function apiDownload($id)
    {
        // Mendapatkan pengguna yang login melalui API (misal: Sanctum)
        $user = Auth::user();
        
        $permohonan = PermohonanSKUsaha::where('id', $id)
                                        ->where('status', 'selesai')
                                        ->first();

        // Jika permohonan tidak ditemukan atau belum selesai
        if (!$permohonan) {
            return response()->json(['message' => 'Dokumen tidak ditemukan atau belum selesai.'], 404);
        }

        // Memastikan hanya pemilik permohonan yang bisa mengunduh
        if ($permohonan->masyarakat_id != $user->id) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk dokumen ini.'], 403); // 403 Forbidden
        }

        // Jika semua pengecekan lolos, izinkan unduhan
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }

        return response()->json(['message' => 'File fisik tidak ditemukan di server.'], 404);
    }
}
