<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKDomisili;
use App\Notifications\StatusPermohonanDiperbarui;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanSKDomisiliController extends Controller
{
    // ... method index, show, verifikasi, editSurat, tolak, downloadFinal tetap sama ...
    public function index(Request $request)
    {
        $query = PermohonanSKDomisili::with('masyarakat')->latest();
        $data = $query->paginate(10)->withQueryString();
        return view('petugas.pengajuan.sk_domisili.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanSKDomisili::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_domisili.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        $permohonan = PermohonanSKDomisili::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();

        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('success', 'Permohonan berhasil diverifikasi!');
    }

    public function editSurat($id)
    {
        $permohonan = PermohonanSKDomisili::findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('error', 'Surat hanya bisa diproses untuk permohonan yang sudah diverifikasi.');
        }
        return view('petugas.pengajuan.sk_domisili.edit_surat', compact('permohonan'));
    }

    /**
     * Memproses data dari form edit dan membuat PDF.
     */
    public function selesaikan(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_pemohon_atau_lembaga' => 'required|string|max:255',
            'nik_pemohon' => 'nullable|string|max:255',
            'alamat_lengkap_domisili' => 'required|string',
            'rt_domisili' => 'required|string|max:5',
            'rw_domisili' => 'required|string|max:5',
        ]);

        $permohonan = PermohonanSKDomisili::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            // 1. Update data permohonan di database
            $permohonan->update($validatedData);
            
            // 2. Set semua data yang akan ditampilkan di PDF SEBELUM PDF dibuat
            $permohonan->generateNomorSurat('474'); 
            $permohonan->tanggal_selesai_proses = Carbon::now();

            // --- PERBAIKAN KUNCI ADA DI SINI ---
            // 3. Buat array data untuk dikirim ke view PDF secara eksplisit
            $pdfData = [
                'permohonan' => $permohonan,
                'tanggal_surat' => $permohonan->tanggal_selesai_proses->isoFormat('D MMMM YYYY'),
                // Ganti nama variabel '$tanggal_selesai_proses' menjadi '$tanggal_surat' agar lebih jelas
            ];

            // 4. Generate PDF menggunakan array data yang sudah lengkap
            $pdf = Pdf::loadView('documents.sk_domisili', $pdfData);
            $fileName = 'Surat Keterangan Domisili_' . Str::slug($permohonan->nama_pemohon) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_domisili/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            // 5. Simpan path file, ubah status, dan simpan semua perubahan ke database
            $permohonan->file_hasil_akhir = $path;
            $permohonan->status = 'selesai';
            $permohonan->save();

            // 6. Kirim notifikasi
            Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

            return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('success', 'Surat Keterangan Domisili berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Domisili untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen: ' . $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonanSKDomisili::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();

        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    public function downloadFinal($id)
    {
        $permohonan = PermohonanSKDomisili::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}
