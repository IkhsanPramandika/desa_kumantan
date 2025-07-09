<?php

namespace App\Http\Controllers\Api\Pengumuman;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pengumuman\PengumumanResource;
use App\Models\Masyarakat;
use App\Models\Pengumuman;
use App\Notifications\PengumumanBaru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

// PERBAIKAN 1: Import StorePengumumanRequest untuk validasi
use App\Http\Requests\Api\Pengumuman\StorePengumumanRequest;

class PengumumanApiController extends Controller
{
    /**
     * Menampilkan daftar semua pengumuman yang sudah dipublikasikan.
     */
    public function index()
    {
       
        $pengumuman = Pengumuman::with('user')
                                ->dipublikasikan()
                                ->latest('tanggal_publikasi')
                                ->paginate(10); 

        return PengumumanResource::collection($pengumuman);
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
                                ->firstOrFail();

        return new PengumumanResource($pengumuman);
    }

    /**
     * Method untuk membuat pengumuman baru (biasanya oleh admin/petugas).
     */
    // PERBAIKAN 3: Gunakan FormRequest untuk validasi yang bersih
    public function store(StorePengumumanRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id(); 
        
        $pengumuman = Pengumuman::create($validatedData);

        // <<< PERBAIKAN: Gunakan Queue untuk pengiriman notifikasi >>>
        // Ini akan mengirim notifikasi di latar belakang tanpa membuat API menunggu.
        $semuaWargaAktif = Masyarakat::where('status_akun', 'active')->get();
        if ($semuaWargaAktif->isNotEmpty()) {
            // Gunakan `Notification::sendLater` atau pastikan Notifikasi Anda mengimplementasikan `ShouldQueue`
            Notification::send($semuaWargaAktif, new PengumumanBaru($pengumuman));
        }

        return (new PengumumanResource($pengumuman))
                ->additional(['message' => 'Pengumuman berhasil dibuat dan notifikasi sedang dikirim.'])
                ->response()
                ->setStatusCode(201);
    }
}

