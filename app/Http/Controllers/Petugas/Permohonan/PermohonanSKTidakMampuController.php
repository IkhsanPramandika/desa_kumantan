<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKTidakMampu;
use App\Notifications\StatusPermohonanDiperbarui; // Menggunakan Notifikasi standar
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanSKTidakMampuController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanSKTidakMampu::with('masyarakat')->latest();
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
        return view('petugas.pengajuan.sk_tidak_mampu.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanSKTidakMampu::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_tidak_mampu.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        $permohonan = PermohonanSKTidakMampu::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();

        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        return redirect()->route('petugas.permohonan-sk-tidak-mampu.show', $id)->with('success', 'Permohonan berhasil diverifikasi!');
    }

    public function editSurat($id)
    {
        $permohonan = PermohonanSKTidakMampu::findOrFail($id);
        
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-tidak-mampu.show', $id)->with('error', 'Surat hanya bisa diproses untuk permohonan yang sudah diverifikasi.');
        }

        return view('petugas.pengajuan.sk_tidak_mampu.edit_surat', compact('permohonan'));
    }

    public function selesaikan(Request $request, $id)
    {
        // 1. Validasi data yang masuk dari form edit
        $validatedData = $request->validate([
            // --- PERBAIKAN KUNCI ADA DI SINI ---
            'nama_pemohon' => 'required|string|max:255',
            'nik_pemohon' => 'required|string|max:255',
            // ------------------------------------
            'nama_terkait' => 'nullable|string|max:255',
            'nik_terkait' => 'nullable|string|max:255',
            'tempat_lahir_terkait' => 'nullable|string|max:255',
            'tanggal_lahir_terkait' => 'nullable|date',
            'pekerjaan_atau_sekolah_terkait' => 'nullable|string|max:255',
            'alamat_terkait' => 'nullable|string',
            'keperluan_surat' => 'required|string',
        ]);

        $permohonan = PermohonanSKTidakMampu::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-tidak-mampu.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            // 2. Update data permohonan di database
            $permohonan->update($validatedData);
            
            // 3. Panggil fungsi penomoran otomatis
            $permohonan->generateNomorSurat('460');

            // 4. Set data lain yang diperlukan
            $permohonan->tanggal_selesai_proses = Carbon::now();

            // 5. Generate PDF
            $pdf = Pdf::loadView('documents.sk_tidak_mampu', ['permohonan' => $permohonan]);
            $fileName = 'Surat Keterangan Tidak Mampu_' . Str::slug($permohonan->nama_pemohon) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_tidak_mampu/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            // 6. Simpan path file, ubah status, dan simpan semua perubahan
            $permohonan->file_hasil_akhir = $path;
            $permohonan->status = 'selesai';
            $permohonan->save();

            // 7. Kirim notifikasi
            Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

            return redirect()->route('petugas.permohonan-sk-tidak-mampu.show', $id)->with('success', 'Surat Keterangan Tidak Mampu berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SKTM untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen: ' . $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonanSKTidakMampu::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();

        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        return redirect()->route('petugas.permohonan-sk-tidak-mampu.show', $id)->with('error', 'Permohonan telah ditolak.');
    }

    public function downloadFinal($id)
    {
        $permohonan = PermohonanSKTidakMampu::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}
