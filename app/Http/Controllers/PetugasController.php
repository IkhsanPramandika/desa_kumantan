<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermohonananKKBaru; 
use App\Models\PermohonananKKHilang;
use App\Models\PermohonananKKPerubahanData;
use App\Models\PermohonananSKDomisili;
use App\Models\PermohonananSKKelahiran;
use App\Models\PermohonananSKPerkawinan;
use App\Models\PermohonananSKTidakMampu;
use App\Models\PermohonananSKUsaha;
use App\Models\PermohonananSKAhliWaris;
use App\Models\User; // Untuk menghitung total pengguna
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PetugasController extends Controller
{
    /**
     * Menampilkan halaman dashboard petugas dengan statistik per jenis permohonan.
     */
    public function dashboard()
    {
        // Inisialisasi array untuk menyimpan statistik per jenis permohonan
        $stats = [];

        // Daftar semua model permohonan yang ingin dihitung
        // Gunakan key yang mudah dibaca untuk Blade, misalnya 'kkBaru', 'skDomisili', dll.
        $permohonanModels = [
            'kkBaru'          => PermohonananKKBaru::class,
            'kkHilang'        => PermohonananKKHilang::class,
            'kkPerubahanData' => PermohonananKKPerubahanData::class,
            'skDomisili'      => PermohonananSKDomisili::class,
            'skKelahiran'     => PermohonananSKKelahiran::class,
            'skPerkawinan'    => PermohonananSKPerkawinan::class,
            'skTidakMampu'    => PermohonananSKTidakMampu::class,
            'skUsaha'         => PermohonananSKUsaha::class,
            'skAhliWaris'     => PermohonananSKAhliWaris::class,
        ];

        // Loop melalui setiap model untuk menghitung total per status
        foreach ($permohonanModels as $key => $modelClass) {
            // Pastikan model ada sebelum mencoba menggunakannya
            if (class_exists($modelClass)) {
                $stats[$key]['total']    = $modelClass::count();
                $stats[$key]['pending']  = $modelClass::where('status', 'pending')->count();
                $stats[$key]['diterima'] = $modelClass::where('status', 'diterima')->count();
                $stats[$key]['ditolak']  = $modelClass::where('status', 'ditolak')->count();
            } else {
                // Set semua ke 0 jika model tidak ditemukan
                $stats[$key]['total']    = 0;
                $stats[$key]['pending']  = 0;
                $stats[$key]['diterima'] = 0;
                $stats[$key]['ditolak']  = 0;
                // Opsional: Anda bisa log pesan error di sini
                // \Log::warning("Model permohonan tidak ditemukan: " . $modelClass);
            }
        }

        // Hitung total pengguna (jika Anda memiliki tabel 'users' dan model 'User')
        $totalUsers = 0;
        if (class_exists(User::class)) {
            $totalUsers = User::count();
        }

        // Hitung juga total keseluruhan untuk dashboard utama (opsional, jika Anda masih ingin menampilkannya)
        $overallTotalPending  = 0;
        $overallTotalAccepted = 0;
        $overallTotalRejected = 0;

        foreach ($stats as $typeStats) {
            $overallTotalPending  += $typeStats['pending'];
            $overallTotalAccepted += $typeStats['diterima'];
            $overallTotalRejected += $typeStats['ditolak'];
        }


        // Teruskan data ke view dashboard
        return view('petugas.dashboard', compact(
            'stats',
            'totalUsers',
            'overallTotalPending',
            'overallTotalAccepted',
            'overallTotalRejected'
        ));
    }

    // --- Metode lainnya (index, verifikasi, tolak, create, store) dari PermohonanKKBaruController
    // --- yang Anda pindahkan ke PetugasController jika itu maksud Anda,
    // --- saya tidak memasukkannya di sini untuk menjaga fokus pada dashboard,
    // --- tetapi pastikan metode-metode tersebut masih ada di PetugasController jika diperlukan.
    // --- Contoh:
    
}