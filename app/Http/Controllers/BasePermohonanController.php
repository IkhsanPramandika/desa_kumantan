<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * BasePermohonanController
 * Controller induk yang menangani logika umum untuk semua jenis permohonan.
 * JANGAN DAFTARKAN CONTROLLER INI DI FILE RUTE.
 */
abstract class BasePermohonanController extends Controller
{
    /**
     * @var string Nama class dari Model yang akan digunakan (e.g., PermohonanKKBaru::class).
     * Wajib di-override oleh child controller.
     */
    protected $modelClass;

    /**
     * @var string Path ke folder view (e.g., 'petugas.permohonan-kk-baru').
     * Wajib di-override oleh child controller.
     */
    protected $viewPath;

    /**
     * @var string Nama awalan rute (e.g., 'petugas.permohonan-kk-baru').
     * Wajib di-override oleh child controller.
     */
    protected $routeName;

    /**
     * @var string Nama field untuk file hasil akhir.
     * Bisa di-override jika nama field berbeda.
     */
    protected $fileResultField = 'file_hasil_akhir';

    /**
     * @var string Folder untuk menyimpan file hasil akhir.
     * Bisa di-override jika nama folder berbeda.
     */
    protected $storagePath = 'dokumen_hasil_akhir';

    /**
     * Menampilkan daftar semua permohonan.
     */
    public function index()
    {
        $data = $this->modelClass::with('masyarakat')->latest()->get();
        return view("{$this->viewPath}.index", compact('data'));
    }

    /**
     * Menampilkan detail satu permohonan untuk diproses.
     */
    public function show($id)
    {
        $permohonan = $this->modelClass::with('masyarakat')->findOrFail($id);
        return view("{$this->viewPath}.show", compact('permohonan'));
    }

    /**
     * Memverifikasi permohonan (Status: pending -> diterima).
     */
    public function verifikasi($id)
    {
        $permohonan = $this->modelClass::findOrFail($id);
        $permohonan->status = 'diterima';
        $permohonan->save();

        return redirect()->route("{$this->routeName}.show", $id)->with('success', 'Permohonan berhasil diverifikasi.');
    }

    /**
     * Menolak permohonan (Status: -> ditolak).
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|max:500']);

        $permohonan = $this->modelClass::findOrFail($id);
        $permohonan->status = 'ditolak';
        $permohonan->catatan_penolakan = $request->catatan_penolakan;
        $permohonan->save();

        return redirect()->route("{$this->routeName}.show", $id)->with('error', 'Permohonan telah ditolak.');
    }

    /**
     * Menyelesaikan permohonan dengan mengunggah file hasil akhir.
     */
    public function selesaikan(Request $request, $id)
    {
        $request->validate([$this->fileResultField => 'required|file|mimes:pdf|max:2048']);

        $permohonan = $this->modelClass::findOrFail($id);

        if ($request->hasFile($this->fileResultField)) {
            // Hapus file lama jika ada
            if ($permohonan->{$this->fileResultField} && Storage::disk('public')->exists($permohonan->{$this->fileResultField})) {
                Storage::disk('public')->delete($permohonan->{$this->fileResultField});
            }
            
            $path = $request->file($this->fileResultField)->store($this->storagePath, 'public');
            $permohonan->{$this->fileResultField} = $path;
        }

        $permohonan->status = 'selesai';
        $permohonan->tanggal_selesai_proses = Carbon::now();
        $permohonan->save();

        return redirect()->route("{$this->routeName}.show", $id)->with('success', 'Proses permohonan berhasil diselesaikan dan file akhir telah diunggah.');
    }

    /**
     * Mengunduh file hasil akhir.
     */
    public function downloadFinal($id)
    {
        $permohonan = $this->modelClass::findOrFail($id);
        $filePath = $permohonan->{$this->fileResultField};

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }

        return redirect()->back()->with('error', 'File hasil akhir tidak ditemukan.');
    }
}