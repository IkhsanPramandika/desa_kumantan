<?php
// Lokasi: app/Http/Controllers/Petugas/Pengumuman/PengumumanController.php

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
    protected $gambarPengumumanPath = 'pengumuman/gambar';
    protected $filePengumumanPath = 'pengumuman/file';

    public function index(Request $request)
    {
        $query = Pengumuman::with('user')->latest();

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

    public function create()
    {
        return view('petugas.pengumuman.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255|unique:pengumuman,judul',
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
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['judul']) . '-' . uniqid(); // Pastikan slug unik

        if ($request->hasFile('gambar_pengumuman')) {
            $data['gambar_pengumuman'] = $request->file('gambar_pengumuman')->store($this->gambarPengumumanPath, 'public');
        }

        if ($request->hasFile('file_pengumuman')) {
            $data['file_pengumuman'] = $request->file('file_pengumuman')->store($this->filePengumumanPath, 'public');
        }

        Pengumuman::create($data);

        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(Pengumuman $pengumuman)
    {
        return view('petugas.pengumuman.show', compact('pengumuman'));
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('petugas.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255|unique:pengumuman,judul,' . $pengumuman->id,
            'isi' => 'required|string',
            'gambar_pengumuman' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'file_pengumuman' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'tanggal_publikasi' => 'required|date',
            'status_publikasi' => 'required|in:draft,dipublikasikan',
            'hapus_gambar_pengumuman' => 'nullable|boolean',
            'hapus_file_pengumuman' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        if ($request->judul !== $pengumuman->judul) {
            $data['slug'] = Str::slug($data['judul']) . '-' . uniqid();
        }

        if ($request->hasFile('gambar_pengumuman')) {
            if ($pengumuman->gambar_pengumuman) Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
            $data['gambar_pengumuman'] = $request->file('gambar_pengumuman')->store($this->gambarPengumumanPath, 'public');
        } elseif ($request->boolean('hapus_gambar_pengumuman')) {
            if ($pengumuman->gambar_pengumuman) Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
            $data['gambar_pengumuman'] = null;
        }

        if ($request->hasFile('file_pengumuman')) {
            if ($pengumuman->file_pengumuman) Storage::disk('public')->delete($pengumuman->file_pengumuman);
            $data['file_pengumuman'] = $request->file('file_pengumuman')->store($this->filePengumumanPath, 'public');
        } elseif ($request->boolean('hapus_file_pengumuman')) {
            if ($pengumuman->file_pengumuman) Storage::disk('public')->delete($pengumuman->file_pengumuman);
            $data['file_pengumuman'] = null;
        }

        $pengumuman->update($data);

        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->gambar_pengumuman) Storage::disk('public')->delete($pengumuman->gambar_pengumuman);
        if ($pengumuman->file_pengumuman) Storage::disk('public')->delete($pengumuman->file_pengumuman);

        $pengumuman->delete();
        return redirect()->route('petugas.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
