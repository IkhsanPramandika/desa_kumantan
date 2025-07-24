<?php
// Lokasi: app/Http/Controllers/Petugas/Pengumuman/PengumumanController.php

namespace App\Http\Controllers\Petugas\Pengumuman; 

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    /**
     * Mendefinisikan path folder untuk menyimpan file.
     * Ini akan membuat file tersimpan di 'storage/app/public/pengumuman/gambar'
     */
    protected $gambarPengumumanPath = 'pengumuman/gambar';
    protected $filePengumumanPath = 'pengumuman/file';

    /**
     * Menampilkan daftar semua pengumuman dengan fitur pencarian dan filter.
     */
    public function index(Request $request)
    {
        $query = Pengumuman::with('user')->latest('tanggal_publikasi');

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status_publikasi', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $pengumuman = $query->paginate($perPage)->withQueryString();
        
        return view('petugas.pengumuman.index', compact('pengumuman'));
    }

    /**
     * Menampilkan form untuk membuat pengumuman baru.
     */
    public function create()
    {
        return view('petugas.pengumuman.create');
    }

    /**
     * Menyimpan pengumuman baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255|unique:pengumuman,judul',
            'isi' => 'required|string',
            'gambar_pengumuman' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'file_pengumuman' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'tanggal_publikasi' => 'required|date',
            'status_publikasi' => 'required|in:draft,dipublikasikan',
        ]);

        // Menambahkan user_id dan slug secara otomatis
        $validatedData['user_id'] = auth()->id();
        $validatedData['slug'] = Str::slug($validatedData['judul']) . '-' . uniqid();

        // Logika untuk upload dan simpan path gambar
        if ($request->hasFile('gambar_pengumuman')) {
            $path = $request->file('gambar_pengumuman')->store($this->gambarPengumumanPath, 'public');
            $validatedData['gambar_pengumuman'] = $path;
        }

        // Logika untuk upload dan simpan path file lampiran
        if ($request->hasFile('file_pengumuman')) {
            $path = $request->file('file_pengumuman')->store($this->filePengumumanPath, 'public');
            $validatedData['file_pengumuman'] = $path;
        }

        Pengumuman::create($validatedData);

        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * Menampilkan detail satu pengumuman.
     */
    public function show(Pengumuman $pengumuman)
    {
        return view('petugas.pengumuman.show', compact('pengumuman'));
    }

    /**
     * Menampilkan form untuk mengedit pengumuman.
     */
    public function edit(Pengumuman $pengumuman)
    {
        return view('petugas.pengumuman.edit', compact('pengumuman'));
    }

    /**
     * Memperbarui data pengumuman yang ada di database.
     */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        // Validasi input dari form, mengabaikan judul yang sama untuk record ini
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255|unique:pengumuman,judul,' . $pengumuman->id,
            'isi' => 'required|string',
            'gambar_pengumuman' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'file_pengumuman' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'tanggal_publikasi' => 'required|date',
            'status_publikasi' => 'required|in:draft,dipublikasikan',
            'hapus_gambar_pengumuman' => 'nullable|boolean',
            'hapus_file_pengumuman' => 'nullable|boolean',
        ]);
        
        // Buat ulang slug jika judulnya berubah
        if ($request->judul !== $pengumuman->judul) {
            $validatedData['slug'] = Str::slug($validatedData['judul']) . '-' . uniqid();
        }

        // Logika untuk update atau hapus gambar
        if ($request->hasFile('gambar_pengumuman')) {
            if ($pengumuman->gambar_pengumuman) Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
            $validatedData['gambar_pengumuman'] = $request->file('gambar_pengumuman')->store($this->gambarPengumumanPath, 'public');
        } elseif ($request->boolean('hapus_gambar_pengumuman')) {
            if ($pengumuman->gambar_pengumuman) Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
            $validatedData['gambar_pengumuman'] = null;
        }

        // Logika untuk update atau hapus file lampiran
        if ($request->hasFile('file_pengumuman')) {
            if ($pengumuman->file_pengumuman) Storage::disk('public')->delete($pengumuman->file_pengumuman);
            $validatedData['file_pengumuman'] = $request->file('file_pengumuman')->store($this->filePengumumanPath, 'public');
        } elseif ($request->boolean('hapus_file_pengumuman')) {
            if ($pengumuman->file_pengumuman) Storage::disk('public')->delete($pengumuman->file_pengumuman);
            $validatedData['file_pengumuman'] = null;
        }

        $pengumuman->update($validatedData);

        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Menghapus pengumuman dari database beserta file terkait.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        // Hapus file dari storage sebelum menghapus record dari database
        if ($pengumuman->gambar_pengumuman) {
            Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
        }
        if ($pengumuman->file_pengumuman) {
            Storage::disk('public')->delete($pengumuman->file_pengumuman);
        }

        $pengumuman->delete();

        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
