<?php
// Lokasi: app/Http/Controllers/Petugas/Permohonan/PermohonanSKKelahiranController.php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKKelahiran;
use App\Notifications\StatusPermohonanDiperbarui;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanSKKelahiranController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanSKKelahiran::with('masyarakat')->latest();
        if ($request->filled('search')) {
            $search = $request->search;
            // Pencarian diperluas untuk nama anak dan orang tua
            $query->where(function($q) use ($search) {
                $q->where('nama_anak', 'like', "%{$search}%")
                  ->orWhere('nama_ayah', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%")
                  ->orWhereHas('masyarakat', function($subq) use ($search) {
                      $subq->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage)->withQueryString();

        return view('petugas.pengajuan.sk_kelahiran.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanSKKelahiran::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.sk_kelahiran.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        $permohonan = PermohonanSKKelahiran::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();
        
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

        return redirect()->route('petugas.permohonan-sk-kelahiran.show', $id)->with('success', 'Permohonan berhasil diverifikasi!');
    }

    public function editSurat($id)
    {
        $permohonan = PermohonanSKKelahiran::findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-kelahiran.show', $id)->with('error', 'Surat hanya bisa diproses untuk permohonan yang sudah diverifikasi.');
        }
        return view('petugas.pengajuan.sk_kelahiran.edit_surat', compact('permohonan'));
    }

    public function selesaikan(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_anak' => 'required|string|max:255',
            'tempat_lahir_anak' => 'required|string|max:255',
            'tanggal_lahir_anak' => 'required|date',
            'jenis_kelamin_anak' => 'required|string|in:Laki-laki,Perempuan',
            'nama_ayah' => 'required|string|max:255',
            'nik_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'nik_ibu' => 'required|string|max:255',
        ]);

        $permohonan = PermohonanSKKelahiran::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-kelahiran.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            $permohonan->update($validatedData);
            $permohonan->generateNomorSurat('472.11');
            $permohonan->tanggal_selesai_proses = Carbon::now();
            $pdf = Pdf::loadView('documents.sk_kelahiran', ['permohonan' => $permohonan]);
            
            // [PERBAIKAN] Menggunakan nama anak untuk nama file agar lebih relevan
            $fileName = 'Surat Keterangan Kelahiran_' . Str::slug($permohonan->nama_anak) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_kelahiran/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->status = 'selesai';
            $permohonan->save();

            Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

            return redirect()->route('petugas.permohonan-sk-kelahiran.show', $id)->with('success', 'Surat Keterangan Kelahiran berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Kelahiran untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen: ' . $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        
        $permohonan = PermohonanSKKelahiran::with('masyarakat')->findOrFail($id);

        $permohonan->status = 'membutuhkan_revisi';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();
        
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        
        return redirect()->route('petugas.permohonan-sk-kelahiran.show', $id)
                         ->with('success', 'Permohonan telah dikembalikan kepada pengguna untuk direvisi.');
    }

    public function downloadFinal($id)
    {
        $permohonan = PermohonanSKKelahiran::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}
