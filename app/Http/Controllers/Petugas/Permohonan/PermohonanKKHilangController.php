<?php

namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanKKHilang;
use App\Notifications\StatusPermohonanDiperbarui;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Masyarakat; // Pastikan ini ada
use Illuminate\Support\Facades\Log; // Pastikan ini ada

class PermohonanKKHilangController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanKKHilang::with('masyarakat')->latest();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('masyarakat', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        $perPage = $request->input('per_page', 10); 

        // Gunakan nilai tersebut di dalam paginate()
        $data = $query->paginate($perPage)->withQueryString(); 
        return view('petugas.pengajuan.kk_hilang.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanKKHilang::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.kk_hilang.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        Log::info('[DEBUG Notif] Metode verifikasi dipanggil untuk PermohonanKKHilang ID: ' . $id);

        $permohonan = PermohonanKKHilang::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();
        Log::info('[DEBUG Notif] Permohonan status diperbarui menjadi diterima.');
        
        // Dapatkan objek masyarakat yang terkait
        $masyarakat = $permohonan->masyarakat;

        if ($masyarakat) {
            Log::info('[DEBUG Notif] Masyarakat ditemukan untuk verifikasi: ' . $masyarakat->id);
            if (empty($masyarakat->fcm_token)) {
                Log::warning('[DEBUG Notif] Masyarakat ID: ' . $masyarakat->id . ' tidak memiliki FCM token untuk verifikasi.');
            }

            try {
                // Panggil notifikasi hanya dengan objek $permohonan
                $masyarakat->notify(new StatusPermohonanDiperbarui($permohonan));
                Log::info('[DEBUG Notif] Notifikasi StatusPermohonanDiperbarui berhasil dipanggil untuk verifikasi.');
            } catch (\Exception $e) {
                Log::error('[DEBUG Notif] Gagal mengirim notifikasi verifikasi: ' . $e->getMessage(), [
                    'exception' => $e,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        } else {
            Log::warning('[DEBUG Notif] Masyarakat tidak ditemukan untuk permohonan ID: ' . $id . ' saat verifikasi.');
        }

        return redirect()->route('petugas.permohonan-kk-hilang.show', $id)->with('success', 'Permohonan berhasil diverifikasi.');
    }

      public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        
        $permohonan = PermohonanKKHilang::with('masyarakat')->findOrFail($id);

        $permohonan->status = 'membutuhkan_revisi';
        $permohonan->catatan_penolakan = $request->catatan_penolakan;
        $permohonan->save();
        
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        
        return redirect()->route('petugas.permohonan-kk-hilang.show', $id)
                         ->with('success', 'Permohonan telah dikembalikan kepada pengguna untuk direvisi.');
    }

    public function selesaikan(Request $request, $id)
    {
        Log::info('[DEBUG Notif] Metode selesaikan dipanggil untuk PermohonanKKHilang ID: ' . $id);

        $request->validate(['file_hasil_akhir' => 'required|file|mimes:pdf|max:2048']);
        $permohonan = PermohonanKKHilang::with('masyarakat')->findOrFail($id);

        if ($request->hasFile('file_hasil_akhir')) {
            if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
                Storage::disk('public')->delete($permohonan->file_hasil_akhir);
            }

            $file = $request->file('file_hasil_akhir');
            $namaPemohonSlug = Str::slug($permohonan->masyarakat->nama_lengkap);
            $idPermohonan = $permohonan->id;
            $ekstensi = $file->getClientOriginalExtension();

            $namaFileKustom = "Kartu Keluarga - _{$namaPemohonSlug}_{$idPermohonan}.{$ekstensi}";

            $path = $file->storeAs('permohonan_kk_hilang/hasil_akhir', $namaFileKustom, 'public');

            $permohonan->file_hasil_akhir = $path;
        }

        $permohonan->status = 'selesai';
        $permohonan->tanggal_selesai_proses = Carbon::now();
        $permohonan->save();
        Log::info('[DEBUG Notif] Permohonan status diperbarui menjadi selesai.');

        // Dapatkan objek masyarakat yang terkait
        $masyarakat = $permohonan->masyarakat;

        if ($masyarakat) {
            Log::info('[DEBUG Notif] Masyarakat ditemukan untuk penyelesaian: ' . $masyarakat->id);
            if (empty($masyarakat->fcm_token)) {
                Log::warning('[DEBUG Notif] Masyarakat ID: ' . $masyarakat->id . ' tidak memiliki FCM token untuk penyelesaian.');
            }

            try {
                // Panggil notifikasi hanya dengan objek $permohonan
                $masyarakat->notify(new StatusPermohonanDiperbarui($permohonan));
                Log::info('[DEBUG Notif] Notifikasi StatusPermohonanDiperbarui berhasil dipanggil untuk penyelesaian.');
            } catch (\Exception $e) {
                Log::error('[DEBUG Notif] Gagal mengirim notifikasi penyelesaian: ' . $e->getMessage(), [
                    'exception' => $e,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        } else {
            Log::warning('[DEBUG Notif] Masyarakat tidak ditemukan untuk permohonan ID: ' . $id . ' saat penyelesaian.');
        }

        return redirect()->route('petugas.permohonan-kk-hilang.show', $id)->with('success', 'Proses permohonan berhasil diselesaikan.');
    }

    public function downloadFinal($id)
    {
        $permohonan = PermohonanKKHilang::where('status', 'selesai')->findOrFail($id);

        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }

        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan atau permohonan belum selesai.');
    }
}
