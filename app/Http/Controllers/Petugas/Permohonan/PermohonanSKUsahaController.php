<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKUsaha;

use App\Notifications\StatusPermohonanDiperbarui;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanSKUsahaController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanSKUsaha::with('masyarakat')->latest();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('masyarakat', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $data = $query->paginate(10)->withQueryString();
        return view('petugas.pengajuan.sk_usaha.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_usaha.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();

        // [PERBAIKAN] Mengirim notifikasi menggunakan kelas yang benar.
        // Judul dan pesan akan dibuat secara otomatis di dalam kelas notifikasi.
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

        return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('success', 'Permohonan berhasil diverifikasi!');
    }

     public function editSurat($id)
    {
        $permohonan = PermohonanSKUsaha::findOrFail($id);
        
        // Pastikan hanya permohonan yang sudah diverifikasi yang bisa diproses
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('error', 'Surat hanya bisa diproses untuk permohonan yang sudah diverifikasi.');
        }

        return view('petugas.pengajuan.sk_usaha.edit_surat', compact('permohonan'));
    }

    /**
     * METHOD LAMA (DIMODIFIKASI): Memproses data dari form edit dan membuat PDF.
     */
    public function selesaikan(Request $request, $id)
    {
        // 1. Validasi data yang masuk dari form edit
        $validatedData = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nik_pemohon' => 'required|string|max:255',
            'nama_usaha' => 'required|string|max:255',
            'alamat_usaha' => 'required|string',
            // Tambahkan validasi untuk field lain jika ada
        ]);

        $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            // 2. Update data permohonan di database dengan data yang sudah divalidasi
            $permohonan->update($validatedData);

            // 3. Generate PDF menggunakan data yang BARU di-update
            $pdf = Pdf::loadView('documents.sk_usaha', ['permohonan' => $permohonan]);
           $fileName = 'Surat Keterangan Usaha_' . Str::slug($permohonan->nama_pemohon) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_usaha/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());

            // 4. Simpan path file dan ubah status menjadi 'selesai'
            $permohonan->file_hasil_akhir = $path;
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();
            $permohonan->save();

            // 5. Kirim notifikasi ke masyarakat
            Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('success', 'Surat Keterangan Usaha berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Usaha untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen.');
        }
    }

    public function tolak(Request $request, $id)
{
    $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
    
    $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);

    // [PERUBAHAN] Status diubah menjadi 'membutuhkan_revisi'
    $permohonan->status = 'membutuhkan_revisi'; 
    
    // Simpan catatan penolakan dari petugas
    $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
    $permohonan->save();

    // Kirim notifikasi ke masyarakat bahwa permohonan mereka perlu direvisi
    // Pastikan Anda sudah membuat kelas notifikasi yang sesuai untuk ini.
    Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

    return redirect()->route('petugas.permohonan-sk-usaha.show', $id)
                     ->with('success', 'Permohonan telah dikembalikan kepada pengguna untuk direvisi.');
}

    public function downloadFinal($id)
    {
        $permohonan = PermohonanSKUsaha::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}
