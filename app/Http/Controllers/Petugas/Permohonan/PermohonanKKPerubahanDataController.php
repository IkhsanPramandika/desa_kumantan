<?php


namespace App\Http\Controllers\Petugas\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\PermohonanKKPerubahanData;
use App\Notifications\StatusPermohonanDiperbarui;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanKKPerubahanDataController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanKKPerubahanData::with('masyarakat')->latest();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('masyarakat', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Menambahkan fitur jumlah data per halaman
        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage)->withQueryString();

        return view('petugas.pengajuan.kk_perubahan.index', compact('data'));
    }

    public function show($id)
    {
        $permohonan = PermohonanKKPerubahanData::with('masyarakat')->findOrFail($id);
        return view('petugas.pengajuan.kk_perubahan.show', compact('permohonan'));
    }

    public function verifikasi($id)
    {
        $permohonan = PermohonanKKPerubahanData::with('masyarakat')->findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();
        
        // [STANDARISASI] Menggunakan kelas notifikasi yang benar.
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

        return redirect()->route('petugas.permohonan-kk-perubahan.show', $id)->with('success', 'Permohonan berhasil diverifikasi.');
    }

     public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:1000']);
        
        $permohonan = PermohonanKKPerubahanData::with('masyarakat')->findOrFail($id);

        // Mengubah status menjadi 'membutuhkan_revisi'
        $permohonan->status = 'membutuhkan_revisi';
        
        // Menyimpan catatan penolakan dari petugas
        $permohonan->catatan_penolakan = $request->catatan_penolakan;
        $permohonan->save();
        
        // Mengirim notifikasi ke pengguna bahwa permohonan perlu direvisi
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));
        
        return redirect()->route('petugas.permohonan-kk-perubahan.show', $id)
                         ->with('success', 'Permohonan telah dikembalikan kepada pengguna untuk direvisi.');
    }

    public function selesaikan(Request $request, $id)
    {
        $request->validate(['file_hasil_akhir' => 'required|file|mimes:pdf|max:2048']);
        $permohonan = PermohonanKKPerubahanData::with('masyarakat')->findOrFail($id);

       if ($request->hasFile('file_hasil_akhir')) {
             if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
                 Storage::disk('public')->delete($permohonan->file_hasil_akhir);
             }
 
             $file = $request->file('file_hasil_akhir');
             $namaPemohonSlug = Str::slug($permohonan->masyarakat->nama_lengkap);
             $idPermohonan = $permohonan->id;
             $ekstensi = $file->getClientOriginalExtension();

             $namaFileKustom = "Kartu Keluarga_{$namaPemohonSlug}_{$idPermohonan}.{$ekstensi}";
 
             $path = $file->storeAs('permohonan_kk_perubahan/hasil_akhir', $namaFileKustom, 'public');
 
             $permohonan->file_hasil_akhir = $path;
        }

        $permohonan->status = 'selesai';
        $permohonan->tanggal_selesai_proses = Carbon::now();
        $permohonan->save();

        // [STANDARISASI] Menggunakan kelas notifikasi yang benar.
        Notification::send($permohonan->masyarakat, new StatusPermohonanDiperbarui($permohonan));

        return redirect()->route('petugas.permohonan-kk-perubahan.show', $id)->with('success', 'Proses permohonan berhasil diselesaikan.');
    }

    public function downloadFinal($id)
    {
        $permohonan = PermohonanKKPerubahanData::findOrFail($id);
        if ($permohonan->file_hasil_akhir && Storage::disk('public')->exists($permohonan->file_hasil_akhir)) {
            return Storage::disk('public')->download($permohonan->file_hasil_akhir);
        }
        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}
