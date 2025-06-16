<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSKAhliWaris;
use App\Notifications\PermohonanStatusUpdated;
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
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $data = $query->paginate(10)->withQueryString();
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
        
        $title = "Permohonan Diverifikasi";
        $message = "Permohonan SK Ahli Waris Anda (#{$permohonan->id}) telah kami verifikasi.";
        Notification::send($permohonan->masyarakat, new PermohonanStatusUpdated($permohonan, $title, $message, '#'));

        return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('success', 'Permohonan berhasil diverifikasi!');
    }

    public function selesaikan(Request $request, $id)
    {
        $permohonan = PermohonanSKAhliWaris::with('masyarakat')->findOrFail($id);
        if ($permohonan->status !== 'diterima') {
            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('error', 'Surat hanya bisa dibuat untuk permohonan yang sudah diverifikasi.');
        }

        try {
            $permohonan->status = 'selesai';
            $permohonan->tanggal_selesai_proses = Carbon::now();
            $permohonan->generateNomorSurat('470');

            $pdf = Pdf::loadView('documents.sk_ahli_waris', ['permohonan' => $permohonan]);
            $fileName = 'SK_Ahli_Waris_' . Str::slug($permohonan->nama_pewaris) . '_' . $permohonan->id . '.pdf';
            $path = 'permohonan_sk_ahli_waris/hasil_akhir/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());
            
            $permohonan->file_hasil_akhir = $path;
            $permohonan->save();

            $title = "Permohonan Selesai";
            $message = "Selamat! Permohonan SK Ahli Waris Anda (#{$permohonan->id}) telah selesai diproses.";
            Notification::send($permohonan->masyarakat, new PermohonanStatusUpdated($permohonan, $title, $message, '#'));

            return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('success', 'Surat Keterangan Ahli Waris berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error("Gagal membuat PDF SK Ahli Waris untuk ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen.');
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);
        $permohonan = PermohonanSKAhliWaris::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan');
        $permohonan->save();

        $title = "Permohonan Ditolak";
        $message = "Maaf, permohonan SK Ahli Waris Anda (#{$permohonan->id}) kami tolak. Alasan: " . $request->catatan_penolakan;
        Notification::send($permohonan->masyarakat, new PermohonanStatusUpdated($permohonan, $title, $message, '#'));
        
        return redirect()->route('petugas.permohonan-sk-ahli-waris.show', $id)->with('error', 'Permohonan telah ditolak.');
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
