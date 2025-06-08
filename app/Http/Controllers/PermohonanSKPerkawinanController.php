<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKPerkawinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PermohonanSKPerkawinanController extends Controller
{
    /**
     * Menampilkan daftar permohonan.
     */
   public function index(Request $request)
    {
        $query = PermohonananSKPerkawinan::with('masyarakat')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pria', 'like', "%{$search}%")
                  ->orWhere('nama_wanita', 'like', "%{$search}%")
                  ->orWhereHas('masyarakat', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(10)->withQueryString();
        
        return view('petugas.pengajuan.sk_nikah.index', compact('data'));
    }   

    /**
     * Menampilkan halaman detail untuk verifikasi.
     */
    public function show($id)
    {
        $permohonan = PermohonananSKPerkawinan::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_nikah.show', compact('permohonan'));
    }

    /**
     * Memverifikasi permohonan.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonananSKPerkawinan::findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();
        return redirect()->route('petugas.permohonan-sk-perkawinan.show', $permohonan->id)
            ->with('success', 'Permohonan berhasil diverifikasi! Anda sekarang dapat membuat surat.');
    }

    /**
     * Membuat PDF dari data yang sudah ada (diinput masyarakat) dan menyelesaikan permohonan.
     */
    public function selesaikan(Request $request, $id)
    {
        $permohonan = PermohonananSKPerkawinan::findOrFail($id);
        
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-perkawinan.show', $id)
                ->with('error', 'Surat hanya bisa dibuat untuk permohonan yang telah diverifikasi.');
        }

        try {
            // Jika Anda memiliki data yang diinput dari masyarakat, pastikan model $fillable sudah sesuai.
            // Untuk alur ini, kita asumsikan data sudah ada dari pengajuan awal.
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();

            if (is_null($permohonan->nomor_surat)) {
                $currentYear = now()->year;
                $lastNomorUrut = PermohonananSKPerkawinan::whereYear('tanggal_selesai_proses', $currentYear)->max('nomor_urut') ?? 0;
                $permohonan->nomor_urut = $lastNomorUrut + 1;
                $permohonan->nomor_surat = "NOMOR/SKP/ANDA/" . $permohonan->nomor_urut;
            }

            $pdf = Pdf::loadView('documents.sk_nikah', ['permohonan' => $permohonan]);
            $fileName = 'SK_Perkawinan_' . Str::slug($permohonan->nama_pria) . '-' . Str::slug($permohonan->nama_wanita) . '_' . $permohonan->id . '.pdf';
            
            // PERBAIKAN: Menentukan path internal yang benar.
            $path = 'permohonan_sk_perkawinan/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            // PERBAIKAN: Menyimpan path internal ke database, BUKAN URL.
            $permohonan->file_hasil_akhir = $path;
            $permohonan->save();

            return redirect()->route('petugas.permohonan-sk-perkawinan.show', $id)->with('success', 'Surat Pengantar Nikah berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Perkawinan untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen.');
        }
    }

    /**
     * Menolak permohonan.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonananSKPerkawinan::findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        return redirect()->route('petugas.permohonan-sk-perkawinan.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    /**
     * Mengunduh file hasil akhir.
     */
    public function downloadFinal($id)
    {
        $permohonan = PermohonananSKPerkawinan::findOrFail($id);

        if ($permohonan->file_hasil_akhir) {
            // PERBAIKAN: Logika ini sekarang sudah benar karena path yang disimpan juga sudah benar.
            // Namun, kita tetap menggunakan str_replace untuk jaga-jaga jika ada data lama yang salah.
            $filePath = str_replace('/storage/', '', $permohonan->file_hasil_akhir);

            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath);
            }
        }
        
        // Pesan error ini muncul jika file tidak ada di storage atau path di DB kosong.
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan di server.');
    }
}
