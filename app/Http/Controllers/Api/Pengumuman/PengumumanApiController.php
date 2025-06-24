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
        // Data yang masuk sudah pasti aman karena telah melewati validasi
        $validatedData = $request->validated();
        
        // Menambahkan user_id dari petugas yang sedang login (asumsi)
        $validatedData['user_id'] = auth()->id(); 
        
        // Membuat pengumuman hanya dengan data yang sudah divalidasi
        $pengumuman = Pengumuman::create($validatedData);

        // PERBAIKAN 4: Kirim notifikasi hanya ke warga yang aktif
        $semuaWargaAktif = Masyarakat::where('status_akun', 'active')->get();
        if ($semuaWargaAktif->isNotEmpty()) {
            Notification::send($semuaWargaAktif, new PengumumanBaru($pengumuman));
        }

        // Mengembalikan response dengan data pengumuman yang baru dibuat
        return (new PengumumanResource($pengumuman))
                ->additional(['message' => 'Pengumuman berhasil dibuat dan notifikasi telah dikirim.'])
                ->response()
                ->setStatusCode(201);
    }
}
