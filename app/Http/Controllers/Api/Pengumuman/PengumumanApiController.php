<?php

namespace App\Http\Controllers\Api\Pengumuman;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pengumuman\PengumumanResource;
use App\Models\Masyarakat;
use App\Models\Pengumuman;
use App\Notifications\PengumumanBaru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Api\Pengumuman\StorePengumumanRequest; // [cite: 2]

class PengumumanApiController extends Controller
{
    /**
     * Menampilkan daftar semua pengumuman yang sudah dipublikasikan.
     */
    public function index()
    {
        // Kode ini sudah sangat baik dan efisien.
        $pengumuman = Pengumuman::with('user')
                                ->dipublikasikan()
                                ->latest('tanggal_publikasi')
                                ->paginate(10); // [cite: 3, 4]
        return PengumumanResource::collection($pengumuman); // [cite: 5]
    }

    /**
     * Menampilkan detail satu pengumuman berdasarkan slug.
     */
    public function show(string $slug)
    {
        // Logika ini sudah sangat bagus, tidak perlu diubah.
        $pengumuman = Pengumuman::with('user')
                                ->dipublikasikan()
                                ->where('slug', $slug)
                                ->firstOrFail(); // [cite: 7]
        return new PengumumanResource($pengumuman); // [cite: 8]
    }

    /**
     * Method untuk membuat pengumuman baru.
     */
    public function store(StorePengumumanRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id(); // [cite: 10]

        $pengumuman = Pengumuman::create($validatedData);

        // SOLUSI: Pastikan Notifikasi `PengumumanBaru` mengimplementasikan `ShouldQueue`.
        // Dengan begitu, baris kode di bawah ini akan bekerja secara asinkron (background job).
        // Ini memastikan respons API tetap cepat.
        $semuaWargaAktif = Masyarakat::where('status_akun', 'active')->get(); // 
        if ($semuaWargaAktif->isNotEmpty()) {
            Notification::send($semuaWargaAktif, new PengumumanBaru($pengumuman)); // 
        }

        return (new PengumumanResource($pengumuman))
                ->additional(['message' => 'Pengumuman berhasil dibuat. Notifikasi akan segera dikirim ke warga.'])
                ->response()
                ->setStatusCode(201);
    }
}