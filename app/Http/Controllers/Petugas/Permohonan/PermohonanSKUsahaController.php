<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKUsaha;
// [PERBAIKAN] Menggunakan kelas notifikasi yang benar
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

    public function selesaikan(Request $request, $id)
    {
        $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();
            // Baris ini sepertinya dari trait, pastikan trait-nya ada dan berfungsi
            // $permohonan->generateNomorSurat('503');

            $pdf = Pdf::loadView('documents.sk_usaha', ['permohonan' => $permohonan]);
            $fileName = 'SK_Usaha_' . Str::slug($permohonan->nama_usaha) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_usaha/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());

            $permohonan->file_hasil_akhir = $path;
            $permohonan->save();

            // [PERBAIKAN] Mengirim notifikasi menggunakan kelas yang benar.
            Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('success', 'Surat Keterangan Usaha berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Usaha untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen.');
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();

        // [PERBAIKAN] Mengirim notifikasi menggunakan kelas yang benar.
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

        return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('error', 'Permohonan telah ditolak.');
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
