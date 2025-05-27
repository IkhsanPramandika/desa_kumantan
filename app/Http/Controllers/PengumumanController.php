<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $pengumuman = Pengumuman::latest()->get();
        return view('petugas.pengumuman.index', compact('pengumuman'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request) {
    $request->validate([
        'judul' => 'required|max:100',
        'isi' => 'required'
    ]);
    
    Pengumuman::create([
        'judul' => $request->judul,
        'isi' => $request->isi,
        'user_id' => auth()->id()
    ]);
    
    return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dibuat!');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
