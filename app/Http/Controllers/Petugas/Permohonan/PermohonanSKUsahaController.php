<?php
// Lokasi: app/Http/Controllers/Petugas/Permohonan/PermohonanSKUsahaController.php

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
            })->orWhere('nama_usaha', 'like', "%{$search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage)->withQueryString();

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

        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('success', 'Permohonan berhasil diverifikasi!');
    }

    public function editSurat($id)
    {
        $permohonan = PermohonanSKUsaha::findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('error', 'Surat hanya bisa diproses untuk permohonan yang sudah diverifikasi.');
        }
        return view('petugas.pengajuan.sk_usaha.edit_surat', compact('permohonan'));
    }

    public function selesaikan(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nik_pemohon' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat_pemohon' => 'required|string',
            'nama_usaha' => 'required|string|max:255',
            'alamat_usaha' => 'required|string',
            'keperluan_surat' => 'required|string',
        ]);

        $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            $permohonan->update($validatedData);
            $permohonan->generateNomorSurat('503');
            $permohonan->tanggal_selesai_proses = Carbon::now();
            $pdf = Pdf::loadView('documents.sk_usaha', ['permohonan' => $permohonan]);
            
            // [PERBAIKAN] Menggunakan nama pemohon dari relasi masyarakat
            $fileName = 'Surat Keterangan Usaha_' . Str::slug($permohonan->masyarakat->nama_lengkap) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_usaha/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->status = 'selesai';
            $permohonan->save();

            Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
            return redirect()->route('petugas.permohonan-sk-usaha.show', $id)->with('success', 'Surat Keterangan Usaha berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Usaha untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen: ' . $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        
        $permohonan = PermohonanSKUsaha::with('masyarakat')->findOrFail($id);

        $permohonan->status = 'membutuhkan_revisi';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();

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
