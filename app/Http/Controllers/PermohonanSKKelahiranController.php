<?php

namespace App\Http\Controllers;

use App\Models\PermohonananSKKelahiran; // Pastikan nama model sesuai
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Tambahkan ini
use Illuminate\Support\Facades\Validator; // Tambahkan ini
use Carbon\Carbon; // Tambahkan ini untuk timestamp
use PDF; // Tambahkan ini untuk Dompdf

class PermohonanSKKelahiranController extends Controller
{
    /**
     * Menampilkan daftar permohonan SK Kelahiran.
     */
    public function index()
    {
        $data = PermohonananSKKelahiran::all();
        return view('petugas.pengajuan.sk_kelahiran.index', compact('data'));
    }

    /**
     * Menampilkan formulir untuk membuat permohonan SK Kelahiran (oleh masyarakat).
     */
    public function create()
    {
        return view('petugas.pengajuan.sk_kelahiran.create');
    }

    /**
     * Menyimpan permohonan SK Kelahiran yang baru dibuat ke database (oleh masyarakat).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input dari Masyarakat (hanya upload dokumen)
        $validator = Validator::make($request->all(), [
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_pengantar_rt_rw' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_nikah_orangtua' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_keterangan_kelahiran' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
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
            'surat_nikah_orangtua',
            'surat_keterangan_kelahiran',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $filePath = $request->file($field)->store('permohonan_sk_kelahiran', 'public'); // Simpan di storage/app/public/permohonan_sk_kelahiran
                $uploadedFilePaths[$field] = $filePath;
            } else {
                $uploadedFilePaths[$field] = null;
            }
        }

        // 3. Simpan Data ke Database
        try {
            PermohonananSKKelahiran::create([
                'file_kk' => $uploadedFilePaths['file_kk'],
                'file_ktp' => $uploadedFilePaths['file_ktp'],
                'surat_pengantar_rt_rw' => $uploadedFilePaths['surat_pengantar_rt_rw'],
                'surat_nikah_orangtua' => $uploadedFilePaths['surat_nikah_orangtua'],
                'surat_keterangan_kelahiran' => $uploadedFilePaths['surat_keterangan_kelahiran'],
                'catatan' => $request->input('catatan'),
                'status' => 'pending', // Status awal selalu 'pending'
            ]);

            return redirect()->route('permohonan-sk-kelahiran.index')->with('success', 'Permohonan Surat Keterangan Kelahiran berhasil diajukan. Menunggu verifikasi petugas.');
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
     * Memverifikasi permohonan SK Kelahiran (oleh petugas).
     * Status diubah menjadi 'diterima', lalu petugas diarahkan ke form input data.
     */
    public function verifikasi($id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);
        $permohonan->status = 'diterima'; // Mengubah status menjadi 'diterima'
        $permohonan->save();

        // Setelah diverifikasi, petugas akan diarahkan ke form pengisian data rinci
        return redirect()->route('permohonan-sk-kelahiran.input-data', $permohonan->id)->with('success', 'Permohonan berhasil diverifikasi. Silakan lengkapi data anak dan orang tua.');
    }

    /**
     * Menampilkan form untuk input data rinci oleh petugas.
     */
    public function inputData($id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);
        // Pastikan permohonan sudah diterima atau diproses sebelum bisa diinput datanya
        if ($permohonan->status !== 'diterima' && $permohonan->status !== 'diproses') {
            return redirect()->route('permohonan-sk-kelahiran.index')->with('error', 'Permohonan belum diverifikasi atau sudah selesai.');
        }
        return view('petugas.pengajuan.sk_kelahiran.input_data', compact('permohonan'));
    }

    /**
     * Menyimpan data rinci yang diinput petugas dan generate PDF Surat Keterangan Kelahiran.
     */
    public function storeDataAndGeneratePdf(Request $request, $id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);

        // Validasi data yang diinput petugas
        $request->validate([
            'nama_anak' => 'required|string|max:255',
            'tempat_lahir_anak' => 'required|string|max:255',
            'tanggal_lahir_anak' => 'required|date',
            'jenis_kelamin_anak' => 'required|in:Laki-laki,Perempuan',
            'agama_anak' => 'required|string|max:255',
            'alamat_anak' => 'required|string|max:500',
            'nama_ayah' => 'required|string|max:255',
            'nik_ayah' => 'nullable|string|max:16',
            'nama_ibu' => 'required|string|max:255',
            'nik_ibu' => 'nullable|string|max:16',
            'no_buku_nikah' => 'nullable|string|max:255',
        ]);

        // Update data permohonan dengan input dari petugas
        $permohonan->fill($request->only([
            'nama_anak', 'tempat_lahir_anak', 'tanggal_lahir_anak', 'jenis_kelamin_anak',
            'agama_anak', 'alamat_anak', 'nama_ayah', 'nik_ayah', 'nama_ibu', 'nik_ibu', 'no_buku_nikah'
        ]));
        $permohonan->status = 'diproses'; // Status berubah menjadi diproses setelah data diinput
        $permohonan->save();

        // --- Logika Generate PDF Surat Keterangan Kelahiran ---
        try {
            // Generate nomor surat
            // Anda bisa menyimpan nomor surat ini di kolom permohonan jika ada, misal: $permohonan->nomor_surat = $nomorSurat;
            $nomorSurat = '01/SKK/DS/SM/' . Carbon::now()->format('m/Y'); // Sesuaikan format nomor surat Anda

            // Data yang akan dilewatkan ke view PDF
            $dataForPdf = [
                'permohonan' => $permohonan,
                'nomor_surat' => $nomorSurat,
                // Anda bisa menambahkan data kepala desa, dll. di sini jika tidak statis di template
            ];

            $pdf = PDF::loadView('documents.sk_kelahiran', $dataForPdf);

            // Tentukan path penyimpanan
            $fileName = 'SK_Kelahiran_' . \Illuminate\Support\Str::slug($permohonan->nama_anak ?? 'anak') . '_' . $id . '.pdf';
            $filePath = 'public/dokumen_hasil_sk_kelahiran/' . $fileName;

            // Simpan PDF ke storage
            Storage::put($filePath, $pdf->output());

            // Update status permohonan dan path file hasil akhir
            $permohonan->file_hasil_akhir = Storage::url($filePath);
            $permohonan->status = 'selesai'; // Status berubah menjadi selesai setelah PDF digenerate
            $permohonan->tanggal_selesai_proses = Carbon::now();
            $permohonan->save();

            return redirect()->route('permohonan-sk-kelahiran.index')->with('success', 'Data berhasil disimpan dan Surat Keterangan Kelahiran berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Menolak permohonan SK Kelahiran dan menyimpan catatan penolakan.
     */
    public function tolak(Request $request, $id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);

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
     * Mengunduh dokumen hasil akhir.
     */
    public function downloadFinal(Request $request, $id)
    {
        $permohonan = PermohonananSKKelahiran::findOrFail($id);

        if ($permohonan->file_hasil_akhir) {
            $filePath = str_replace('/storage/', '', $permohonan->file_hasil_akhir);
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath);
            }
        }

        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan atau tidak dapat diunduh.');
    }
}
