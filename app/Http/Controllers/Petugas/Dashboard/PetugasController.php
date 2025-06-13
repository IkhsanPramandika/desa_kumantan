<?php

namespace App\Http\Controllers\Petugas\Dashboard;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;


class PetugasController extends Controller
{
    public function dashboard()
{
    // Screenshot Anda mengkonfirmasi nama model ini sudah benar.
    // Tidak ada perubahan di sini.
    $permohonanModels = [
        'kkBaru'          => \App\Models\PermohonanKKBaru::class,
        'kkHilang'        => \App\Models\PermohonanKKHilang::class,
        'kkPerubahanData' => \App\Models\PermohonanKKPerubahanData::class,
        'skDomisili'      => \App\Models\PermohonanSKDomisili::class,
        'skKelahiran'     => \App\Models\PermohonanSKKelahiran::class,
        'skPerkawinan'    => \App\Models\PermohonanSKPerkawinan::class,
        'skTidakMampu'    => \App\Models\PermohonanSKTidakMampu::class,
        'skUsaha'         => \App\Models\PermohonanSKUsaha::class,
        'skAhliWaris'     => \App\Models\PermohonanSKAhliWaris::class,
    ];

    $stats = [];
    foreach ($permohonanModels as $key => $modelClass) {
        if (class_exists($modelClass)) {
            $stats[$key]['total']    = $modelClass::count();
            $stats[$key]['pending']  = $modelClass::where('status', 'pending')->count();
            $stats[$key]['diterima'] = $modelClass::where('status', 'diterima')->count();
            $stats[$key]['diproses'] = $modelClass::where('status', 'diproses')->count(); // <-- BARIS BARU DITAMBAHKAN
            $stats[$key]['ditolak']  = $modelClass::where('status', 'ditolak')->count();
        } else {
            $stats[$key] = ['total' => 0, 'pending' => 0, 'diterima' => 0, 'diproses' => 0, 'ditolak' => 0];
        }
    }
    
    // Konfigurasi untuk card menu, tidak perlu diubah.
    $permohonanDetails = [
        'kkBaru'          => ['title' => 'Kartu Keluarga Baru', 'icon' => 'fas fa-id-card-alt', 'route' => 'petugas.permohonan-kk-baru.index', 'color' => 'primary'],
        'kkPerubahanData' => ['title' => 'KK Perubahan Data', 'icon' => 'fas fa-edit', 'route' => 'petugas.permohonan-kk-perubahan.index', 'color' => 'success'],
        'kkHilang'        => ['title' => 'Kartu Keluarga Hilang', 'icon' => 'fas fa-id-card', 'route' => 'petugas.permohonan-kk-hilang.index', 'color' => 'info'],
        'skKelahiran'     => ['title' => 'SK Kelahiran & Akta', 'icon' => 'fas fa-baby', 'route' => 'petugas.permohonan-sk-kelahiran.index', 'color' => 'warning'],
        'skAhliWaris'     => ['title' => 'SK Ahli Waris', 'icon' => 'fas fa-gavel', 'route' => 'petugas.permohonan-sk-ahli-waris.index', 'color' => 'danger'],
        'skPerkawinan'    => ['title' => 'Surat Pengantar Nikah', 'icon' => 'fas fa-ring', 'route' => 'petugas.permohonan-sk-perkawinan.index', 'color' => 'dark'],
        'skUsaha'         => ['title' => 'Surat Keterangan Usaha', 'icon' => 'fas fa-briefcase', 'route' => 'petugas.permohonan-sk-usaha.index', 'color' => 'secondary'],
        'skDomisili'      => ['title' => 'Surat Keterangan Domisili', 'icon' => 'fas fa-home', 'route' => 'petugas.permohonan-sk-domisili.index', 'color' => 'primary'],
        'skTidakMampu'    => ['title' => 'SK Tidak Mampu', 'icon' => 'fas fa-hand-holding-heart', 'route' => 'petugas.permohonan-sk-tidak-mampu.index', 'color' => 'info'],
    ];

    // Hitung total keseluruhan
    $overallTotalPending  = array_sum(array_column($stats, 'pending'));
    $overallTotalAccepted = array_sum(array_column($stats, 'diterima'));
    $overallTotalInProcess = array_sum(array_column($stats, 'diproses')); // <-- BARIS BARU DITAMBAHKAN
    $overallTotalRejected = array_sum(array_column($stats, 'ditolak'));

    // Hitung total pengguna
    $totalUsers = User::count();

    return view('petugas.dashboard', compact(
        'stats',
        'permohonanDetails',
        'totalUsers',
        'overallTotalPending',
        'overallTotalAccepted',
        'overallTotalInProcess', // <-- BARU, DIKIRIM KE VIEW
        'overallTotalRejected'
    ));
}
}