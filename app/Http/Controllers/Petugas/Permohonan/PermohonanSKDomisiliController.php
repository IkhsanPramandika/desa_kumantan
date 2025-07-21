<?php
// Lokasi: app/Http/Controllers/Petugas/Permohonan/PermohonanSKDomisiliController.php

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
    public function index(Request $request)
    {
        $query = PermohonanSKDomisili::with('masyarakat')->latest();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('masyarakat', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            })->orWhere('nama_pemohon_atau_lembaga', 'like', "%{$search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage)->withQueryString();

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

    public function selesaikan(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_pemohon_atau_lembaga' => 'required|string|max:255',
            'nik_pemohon' => 'nullable|string|max:255',
            'alamat_lengkap_domisili' => 'required|string',
            'rt_domisili' => 'required|string|max:5',
            'rw_domisili' => 'required|string|max:5',
            'keperluan_domisili' => 'required|string',
        ]);

        $permohonan = PermohonanSKDomisili::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            $permohonan->update($validatedData);
            $permohonan->generateNomorSurat('474');
            $permohonan->tanggal_selesai_proses = Carbon::now();
            
            $pdf = Pdf::loadView('documents.sk_domisili', ['permohonan' => $permohonan]);
            $fileName = 'Surat Keterangan Domisili_' . Str::slug($permohonan->nama_pemohon_atau_lembaga) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_domisili/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->status = 'selesai';
            $permohonan->save();

            Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

            return redirect()->route('petugas.permohonan-sk-domisili.show', $id)->with('success', 'Surat Keterangan Domisili berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Domisili untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen: ' . $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        
        $permohonan = PermohonanSKDomisili::with('masyarakat')->findOrFail($id);

        $permohonan->status = 'membutuhkan_revisi';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        
        return redirect()->route('petugas.permohonan-sk-domisili.show', $id)
                         ->with('success', 'Permohonan telah dikembalikan kepada pengguna untuk direvisi.');
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
