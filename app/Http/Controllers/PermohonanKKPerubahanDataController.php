<?php

namespace App\Http\Controllers;

use App\Models\PermohonananKKPerubahanData; // Pastikan nama model sesuai
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Tambahkan ini
use Illuminate\Support\Facades\Validator; // Tambahkan ini
use Carbon\Carbon; // Tambahkan ini untuk timestamp

class PermohonanKKPerubahanDataController extends Controller
{
    /**
     * Menampilkan daftar permohonan KK Perubahan Data.
     */
    public function index()
    {
        $data = PermohonananKKPerubahanData::all();
        return view('petugas.pengajuan.kk_perubahan.index', compact('data'));
    }

    /**
     * Menampilkan formulir untuk membuat permohonan KK Perubahan Data.
     */
    public function create()
    {
        return view('petugas.pengajuan.kk_perubahan.create');
    }

    /**
     * Menyimpan permohonan KK Perubahan Data yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_pengantar_rt_rw' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_keterangan_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Bisa nullable
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 2. Proses Upload File
        $uploadedFilePaths = [];
        $fileFields = [
            'file_kk',
            'file_ktp',
            'surat_pengantar_rt_rw',
            'surat_keterangan_pendukung',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $filePath = $request->file($field)->store('permohonan_kk_perubahan_data', 'public'); // Simpan di storage/app/public/permohonan_kk_perubahan_data
                $uploadedFilePaths[$field] = $filePath;
            } else {
                $uploadedFilePaths[$field] = null;
            }
        }

        // 3. Simpan Data ke Database
        try {
            PermohonananKKPerubahanData::create([
                'file_kk' => $uploadedFilePaths['file_kk'],
                'file_ktp' => $uploadedFilePaths['file_ktp'],
                'surat_pengantar_rt_rw' => $uploadedFilePaths['surat_pengantar_rt_rw'],
                'surat_keterangan_pendukung' => $uploadedFilePaths['surat_keterangan_pendukung'],
                'catatan' => $request->input('catatan'),
                'status' => 'pending', // Status awal selalu 'pending'
            ]);

            return redirect()->route('permohonan-kk-perubahan.index')->with('success', 'Permohonan KK Perubahan Data berhasil diajukan.');
        } catch (\Exception $e) {
            foreach ($uploadedFilePaths as $path) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan permohonan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Memverifikasi permohonan KK Perubahan Data.
     * Status diubah menjadi 'diterima'.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonananKKPerubahanData::findOrFail($id);
        $permohonan->status = 'diterima'; // Mengubah status menjadi 'diterima'
        $permohonan->save();

        return redirect()->back()->with('success', 'Permohonan berhasil diverifikasi. Petugas dapat mengunggah file final.');
    }

    /**
     * Menolak permohonan KK Perubahan Data dan menyimpan catatan penolakan.
     */
    public function tolak(Request $request, $id)
    {
        $permohonan = PermohonananKKPerubahanData::findOrFail($id);

        // Validasi catatan penolakan
        $request->validate([
            'catatan_penolakan' => 'required|string|max:500', // Catatan tidak boleh kosong, max 500 karakter
        ]);

        $permohonan->status = 'ditolak'; // Mengubah status menjadi 'ditolak'
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan'); // Menyimpan catatan penolakan
        $permohonan->save();

        return redirect()->back()->with('error', 'Permohonan berhasil ditolak dengan catatan.');
    }

    /**
     * Mengunggah file PDF Kartu Keluarga Perubahan Data final setelah permohonan diterima.
     */
    public function uploadFinalPdf(Request $request, $id)
    {
        $permohonan = PermohonananKKPerubahanData::findOrFail($id);

        // Validasi file PDF yang diunggah
        $request->validate([
            'file_hasil_akhir' => 'required|file|mimes:pdf|max:2048', // Wajib, file PDF, max 2MB
        ]);

        try {
            if ($request->hasFile('file_hasil_akhir')) {
                // Hapus file lama jika ada
                if ($permohonan->file_hasil_akhir) {
                    $oldPath = str_replace('/storage/', 'public/', $permohonan->file_hasil_akhir);
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }

                // Simpan file baru di direktori public/dokumen_hasil_kk_perubahan_data
                $path = $request->file('file_hasil_akhir')->store('dokumen_hasil_kk_perubahan_data', 'public');
                $permohonan->file_hasil_akhir = Storage::url($path); // Simpan path yang bisa diakses publik
                $permohonan->status = 'selesai'; // Ubah status menjadi 'selesai' setelah upload
                $permohonan->tanggal_selesai_proses = Carbon::now(); // Catat tanggal selesai proses
                $permohonan->save();

                return redirect()->back()->with('success', 'File Kartu Keluarga Perubahan Data final berhasil diunggah dan status diperbarui.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file Kartu Keluarga Perubahan Data final.');
    }

    /**
     * Mengunduh dokumen hasil akhir.
     */
    public function downloadFinal(Request $request, $id)
    {
        $permohonan = PermohonananKKPerubahanData::findOrFail($id);

        if ($permohonan->file_hasil_akhir) {
            $filePath = str_replace('/storage/', '', $permohonan->file_hasil_akhir);
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath);
            }
        }

        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan atau tidak dapat diunduh.');
    }
}
