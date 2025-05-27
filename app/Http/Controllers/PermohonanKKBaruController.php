<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermohonananKKBaru; // Pastikan nama model Anda benar
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Digunakan untuk timestamp

class PermohonanKKBaruController extends Controller
{
    /**
     * Menampilkan daftar permohonan Kartu Keluarga Baru.
     */
    public function index()
    {
        // Mengambil semua data permohonan KK Baru
        $data = PermohonananKKBaru::all();
        return view('petugas.pengajuan.kk_baru.index', compact('data'));
    }

    /**
     * Memverifikasi permohonan KK Baru.
     * Status diubah menjadi 'diterima'.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonananKKBaru::findOrFail($id);
        $permohonan->status = 'diterima'; // Mengubah status menjadi 'diterima'
        $permohonan->save();

        return redirect()->route('permohonan-kk.index')->with('success', 'Permohonan berhasil diverifikasi. Petugas dapat mengunggah file final.');
    }

    /**
     * Menolak permohonan KK Baru dan menyimpan catatan penolakan.
     */
    public function tolak(Request $request, $id)
    {
        $permohonan = PermohonananKKBaru::findOrFail($id);

        // Validasi catatan penolakan
        $request->validate([
            'catatan_penolakan' => 'required|string|max:500', // Catatan tidak boleh kosong, max 500 karakter
        ]);

        $permohonan->status = 'ditolak'; // Mengubah status menjadi 'ditolak'
        $permohonan->catatan_penolakan = $request->input('catatan_penolakan'); // Menyimpan catatan penolakan
        $permohonan->save();

        return redirect()->route('permohonan-kk.index')->with('error', 'Permohonan berhasil ditolak dengan catatan.');
    }

    /**
     * Menampilkan formulir untuk membuat permohonan KK Baru.
     */
    public function create()
    {
        return view('petugas.pengajuan.kk_baru.create');
    }

    /**
     * Menyimpan permohonan KK Baru yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Wajib, file, tipe, max 2MB
            'file_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_pengantar_rt_rw' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'buku_nikah_akta_cerai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Nullable jika tidak wajib
            'surat_pindah_datang' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'ijazah_terakhir' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'catatan' => 'nullable|string|max:500', // Catatan bisa kosong, max 500 karakter
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
            'buku_nikah_akta_cerai',
            'surat_pindah_datang',
            'ijazah_terakhir',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $filePath = $request->file($field)->store('permohonan_kk_baru', 'public'); // Simpan di storage/app/public/permohonan_kk_baru
                $uploadedFilePaths[$field] = $filePath;
            } else {
                $uploadedFilePaths[$field] = null; // Set null jika file tidak diunggah (untuk nullable fields)
            }
        }

        // 3. Simpan Data ke Database
        try {
            PermohonananKKBaru::create([
                'file_kk' => $uploadedFilePaths['file_kk'],
                'file_ktp' => $uploadedFilePaths['file_ktp'],
                'surat_pengantar_rt_rw' => $uploadedFilePaths['surat_pengantar_rt_rw'],
                'buku_nikah_akta_cerai' => $uploadedFilePaths['buku_nikah_akta_cerai'],
                'surat_pindah_datang' => $uploadedFilePaths['surat_pindah_datang'],
                'ijazah_terakhir' => $uploadedFilePaths['ijazah_terakhir'],
                'catatan' => $request->input('catatan'),
                'status' => 'pending', // Status awal selalu 'pending'
            ]);

            return redirect()->route('permohonan-kk.index')->with('success', 'Permohonan KK Baru berhasil diajukan.');
        } catch (\Exception $e) {
            // Jika terjadi error saat menyimpan ke DB, hapus file yang sudah diunggah
            foreach ($uploadedFilePaths as $path) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan permohonan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mengunggah file PDF Kartu Keluarga final setelah permohonan diterima.
     */
    public function uploadFinalPdf(Request $request, $id)
    {
        $permohonan = PermohonananKKBaru::findOrFail($id);

        // Validasi file PDF yang diunggah
        $request->validate([
            'file_hasil_akhir' => 'required|file|mimes:pdf|max:2048', // Wajib, file PDF, max 2MB
        ]);

        try {
            if ($request->hasFile('file_hasil_akhir')) {
                // Hapus file lama jika ada
                if ($permohonan->file_hasil_akhir) {
                    // Pastikan path yang disimpan adalah path relatif dari storage/app
                    $oldPath = str_replace('/storage/', 'public/', $permohonan->file_hasil_akhir);
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }

                // Simpan file baru di direktori public/dokumen_hasil_kk
                $path = $request->file('file_hasil_akhir')->store('dokumen_hasil_kk', 'public');
                $permohonan->file_hasil_akhir = Storage::url($path); // Simpan path yang bisa diakses publik (misal: /storage/dokumen_hasil_kk/namafile.pdf)
                $permohonan->status = 'selesai'; // Ubah status menjadi 'selesai' setelah upload
                $permohonan->tanggal_selesai_proses = Carbon::now(); // Catat tanggal selesai proses
                $permohonan->save();

                return redirect()->back()->with('success', 'File Kartu Keluarga final berhasil diunggah dan status diperbarui.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file Kartu Keluarga final.');
    }

    /**
     * Mengunduh dokumen hasil akhir.
     */
    public function downloadFinal(Request $request, $id)
    {
        $permohonan = PermohonananKKBaru::findOrFail($id);

        if ($permohonan->file_hasil_akhir) {
            // Ambil path relatif dari storage/app/public
            // Misalnya, jika file_hasil_akhir adalah '/storage/dokumen_hasil_kk/file.pdf',
            // kita perlu mengubahnya menjadi 'dokumen_hasil_kk/file.pdf' untuk Storage::download
            $filePath = str_replace('/storage/', '', $permohonan->file_hasil_akhir);

            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath);
            }
        }

        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan atau tidak dapat diunduh.');
    }
}
