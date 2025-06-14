<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Dashboord
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Petugas\Dashboard\SearchController;
use App\Http\Controllers\Petugas\Dashboard\PetugasController;
use App\Http\Controllers\Petugas\Dashboard\ProfileController;
use App\Http\Controllers\Petugas\Dashboard\DashboardController;

// Pengumuman
use App\Http\Controllers\Petugas\Pengumuman\PengumumanController;

// Permohonan
use App\Http\Controllers\Petugas\Permohonan\PermohonanKKBaruController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKUsahaController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanKKHilangController;
use App\Http\Controllers\Petugas\Dashboard\DocumentVerificationController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKDomisiliController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKAhliWarisController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKKelahiranController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKPerkawinanController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKTidakMampuController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanKKPerubahanDataController;


// --- RUTE PUBLIK (Bisa diakses siapa saja tanpa login) ---
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/verify-document/{id}', [DocumentVerificationController::class, 'verify'])->name('verify.document');

// --- RUTE OTENTIKASI ---
require __DIR__.'/auth.php';

// --- RUTE HALAMAN UTAMA (/) ---
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role == 'petugas') {
            return redirect()->route('petugas.dashboard');
        }
    }
    return view('auth.login');
})->name('home');


// =================================================================================
// GRUP UTAMA UNTUK SEMUA RUTE PETUGAS (AMAN & TERSTRUKTUR)
// =================================================================================
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');

   // URL: /petugas/notifications/check (untuk dipanggil JavaScript)
    Route::get('/notifications/check', [NotificationController::class, 'check'])->name('notifications.check');
    // URL: /petugas/notifikasi (halaman untuk melihat semua notifikasi)
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
    // URL: /petugas/notifikasi/baca/{id} (untuk menandai sudah dibaca)
    Route::get('/notifikasi/baca/{id}', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');
   
   

    // --- Profile Petugas ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');   
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // --- Manajemen Akun Masyarakat ---
    Route::prefix('masyarakat')->name('masyarakat.')->group(function () {
        Route::get('/', [PetugasController::class, 'masyarakatIndex'])->name('index');
        Route::get('/{masyarakat}', [PetugasController::class, 'masyarakatShow'])->name('show');
        Route::post('/{masyarakat}/update-status', [PetugasController::class, 'updateMasyarakatStatus'])->name('updateStatus');
        Route::get('/{masyarakat}/reset-password', [PetugasController::class, 'showResetPasswordFormByPetugas'])->name('showResetPasswordFormByPetugas');
        Route::post('/{masyarakat}/reset-password', [PetugasController::class, 'resetPasswordByPetugas'])->name('resetPasswordByPetugas');
    });

    // --- Manajemen Pengumuman ---
    Route::resource('pengumuman', PengumumanController::class);

    // --- RUTE PERMOHONAN DENGAN ALUR SEDERHANA (Upload Final) ---
    $simplePermohonanRoutes = [
        'permohonan-kk-baru'        => PermohonanKKBaruController::class,
        'permohonan-kk-hilang'      => PermohonanKKHilangController::class,
        'permohonan-kk-perubahan'   => PermohonanKKPerubahanDataController::class,
    ];

    foreach ($simplePermohonanRoutes as $uri => $controller) {
        Route::prefix($uri)->name($uri . '.')->group(function () use ($controller) {
            Route::get('/', [$controller, 'index'])->name('index');
            Route::get('/{id}', [$controller, 'show'])->name('show');
            Route::post('/{id}/verifikasi', [$controller, 'verifikasi'])->name('verifikasi');
            Route::post('/{id}/tolak', [$controller, 'tolak'])->name('tolak');
            Route::post('/{id}/selesaikan', [$controller, 'selesaikan'])->name('selesaikan');
            Route::get('/{id}/download-final', [$controller, 'downloadFinal'])->name('download-final');
        });
    }

    // --- RUTE PERMOHONAN DENGAN ALUR KOMPLEKS (Input Data & Generate PDF) ---
    $complexPermohonanRoutes = [
        'permohonan-sk-domisili'    => PermohonanSKDomisiliController::class,
        'permohonan-sk-kelahiran'   => PermohonanSKKelahiranController::class,
        'permohonan-sk-perkawinan'  => PermohonanSKPerkawinanController::class,
        'permohonan-sk-tidak-mampu' => PermohonanSKTidakMampuController::class,
        'permohonan-sk-usaha'       => PermohonanSKUsahaController::class,
        'permohonan-sk-ahli-waris'  => PermohonanSKAhliWarisController::class,
    ];

    foreach ($complexPermohonanRoutes as $uri => $controller) {
        Route::prefix($uri)->name($uri . '.')->group(function () use ($controller) {
            Route::get('/', [$controller, 'index'])->name('index');
            Route::get('/{id}', [$controller, 'show'])->name('show');
            Route::post('/{id}/verifikasi', [$controller, 'verifikasi'])->name('verifikasi');
            Route::post('/{id}/tolak', [$controller, 'tolak'])->name('tolak');
            Route::get('/{id}/input-data', [$controller, 'inputData'])->name('input-data');
            // PERBAIKAN: Rute 'selesaikan' sekarang mengarah ke method 'selesaikan'
            Route::post('/{id}/selesaikan', [$controller, 'selesaikan'])->name('selesaikan');
            Route::get('/{id}/download-final', [$controller, 'downloadFinal'])->name('download-final');
        });
    }
});
