<?php

namespace App\Http\Controllers\Petugas\Pengumuman; 

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    protected $gambarPengumumanPath = 'pengumuman/gambar'; // di dalam storage/app/public/
    protected $filePengumumanPath = 'pengumuman/file';     // di dalam storage/app/public/

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pengumuman = Pengumuman::latest()->paginate(10); // Ambil semua dengan paginasi
        return view('petugas.pengumuman.index', compact('pengumuman'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('petugas.pengumuman.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255|unique:pengumuman,judul',
            'isi' => 'required|string',
            'gambar_pengumuman' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Opsional gambar
            'file_pengumuman' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120', // Opsional file lampiran
            'tanggal_publikasi' => 'required|date',
            'status_publikasi' => 'required|in:draft,dipublikasikan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id(); // Petugas yang login
        $data['slug'] = Str::slug($data['judul']); // Generate slug

        // Handle upload gambar_pengumuman
        if ($request->hasFile('gambar_pengumuman')) {
            $pathGambar = $request->file('gambar_pengumuman')->store($this->gambarPengumumanPath, 'public');
            $data['gambar_pengumuman'] = $pathGambar; // Simpan path relatif ke disk public
        }

        // Handle upload file_pengumuman
        if ($request->hasFile('file_pengumuman')) {
            $pathFile = $request->file('file_pengumuman')->store($this->filePengumumanPath, 'public');
            $data['file_pengumuman'] = $pathFile; // Simpan path relatif ke disk public
        }

        Pengumuman::create($data);

        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengumuman $pengumuman) // Menggunakan Route Model Binding
    {
        // Biasanya untuk petugas, show dialihkan ke edit atau tidak digunakan jika index sudah cukup detail
        return view('petugas.pengumuman.show', compact('pengumuman')); // Buat view show jika diperlukan
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengumuman $pengumuman) // Menggunakan Route Model Binding
    {
        return view('petugas.pengumuman.edit', compact('pengumuman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengumuman $pengumuman) // Menggunakan Route Model Binding
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255|unique:pengumuman,judul,' . $pengumuman->id,
            'isi' => 'required|string',
            'gambar_pengumuman' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'file_pengumuman' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'tanggal_publikasi' => 'required|date',
            'status_publikasi' => 'required|in:draft,dipublikasikan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        // Jika judul diubah, update slug
        if ($request->judul !== $pengumuman->judul) {
            $data['slug'] = Str::slug($data['judul']);
        }

        // Handle update gambar_pengumuman
        if ($request->hasFile('gambar_pengumuman')) {
            // Hapus gambar lama jika ada
            if ($pengumuman->gambar_pengumuman && Storage::disk('public')->exists($pengumuman->gambar_pengumuman)) {
                Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
            }
            $pathGambar = $request->file('gambar_pengumuman')->store($this->gambarPengumumanPath, 'public');
            $data['gambar_pengumuman'] = $pathGambar;
        } elseif ($request->boolean('hapus_gambar_pengumuman')) { // Jika ada checkbox untuk hapus gambar
             if ($pengumuman->gambar_pengumuman && Storage::disk('public')->exists($pengumuman->gambar_pengumuman)) {
                Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
            }
            $data['gambar_pengumuman'] = null;
        }


        // Handle update file_pengumuman
        if ($request->hasFile('file_pengumuman')) {
            // Hapus file lama jika ada
            if ($pengumuman->file_pengumuman && Storage::disk('public')->exists($pengumuman->file_pengumuman)) {
                Storage::disk('public')->delete($pengumuman->file_pengumuman);
            }
            $pathFile = $request->file('file_pengumuman')->store($this->filePengumumanPath, 'public');
            $data['file_pengumuman'] = $pathFile;
        } elseif ($request->boolean('hapus_file_pengumuman')) { // Jika ada checkbox untuk hapus file
             if ($pengumuman->file_pengumuman && Storage::disk('public')->exists($pengumuman->file_pengumuman)) {
                Storage::disk('public')->delete($pengumuman->file_pengumuman);
            }
            $data['file_pengumuman'] = null;
        }

        $pengumuman->update($data);

        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman) // Menggunakan Route Model Binding
    {
        // Hapus gambar dan file terkait jika ada
        if ($pengumuman->gambar_pengumuman && Storage::disk('public')->exists($pengumuman->gambar_pengumuman)) {
            Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
        }
        if ($pengumuman->file_pengumuman && Storage::disk('public')->exists($pengumuman->file_pengumuman)) {
            Storage::disk('public')->delete($pengumuman->file_pengumuman);
        }

        $pengumuman->delete();
        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
