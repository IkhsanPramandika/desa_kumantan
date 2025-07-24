<?php
// Lokasi: app/Http/Controllers/Petugas/Permohonan/PermohonanSKAhliWarisController.php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKAhliWaris;
use App\Notifications\StatusPermohonanDiperbarui;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanSKAhliWarisController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanSKAhliWaris::with('masyarakat')->latest();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('masyarakat', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            })->orWhere('nama_pewaris', 'like', "%{$search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // [FITUR BARU] Menambahkan fitur jumlah data per halaman
        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage)->withQueryString();

        return view('petugas.pengajuan.sk_ahli_waris.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanSKAhliWaris::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_ahli_waris.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        $permohonan = PermohonanSKAhliWaris::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();
        
        // [STANDARISASI] Menggunakan kelas notifikasi yang benar.
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

        return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('success', 'Permohonan berhasil diverifikasi!');
    }

    public function editSurat($id)
    {
        $permohonan = PermohonanSKAhliWaris::findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('error', 'Surat hanya bisa diproses untuk permohonan yang sudah diverifikasi.');
        }
        return view('petugas.pengajuan.sk_ahli_waris.edit_surat', compact('permohonan'));
    }

    public function selesaikan(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_pewaris' => 'required|string|max:255',
            'nik_pewaris' => 'required|string|max:255',
            'tempat_lahir_pewaris' => 'required|string|max:255',
            'tanggal_lahir_pewaris' => 'required|date',
            'tanggal_meninggal_pewaris' => 'required|date',
            'alamat_pewaris' => 'required|string',
            'daftar_ahli_waris' => 'required|array', // Validasi dasar, bisa diperkuat jika perlu
        ]);

        $permohonan = PermohonanSKAhliWaris::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            $permohonan->update($validatedData);
            $permohonan->generateNomorSurat('470');
            $permohonan->tanggal_selesai_proses = Carbon::now();
            $pdf = Pdf::loadView('documents.sk_ahli_waris', ['permohonan' => $permohonan]);
            $fileName = 'Surat Keterangan Ahli Waris_' . Str::slug($permohonan->masyarakat->nama_lengkap) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_ahli_waris/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->status = 'selesai';
            $permohonan->save();

            Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('success', 'Surat Keterangan Ahli Waris berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Ahli Waris untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen: ' . $e->getMessage());
        }
    }

    /**
     * [PERBAIKAN] Fungsi tolak diubah untuk alur revisi.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        
        $permohonan = PermohonanSKAhliWaris::with('masyarakat')->findOrFail($id);

        $permohonan->status = 'membutuhkan_revisi';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        
        return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)
                         ->with('success', 'Permohonan telah dikembalikan kepada pengguna untuk direvisi.');
    }

    public function downloadFinal($id)
    {
        $permohonan = PermohonanSKAhliWaris::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}
