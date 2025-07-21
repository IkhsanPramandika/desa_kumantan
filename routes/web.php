<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Import semua controller yang dibutuhkan
use App\Http\Controllers\Petugas\NotificationController;
use App\Http\Controllers\Petugas\Dashboard\SearchController;
use App\Http\Controllers\Petugas\Dashboard\PetugasController;
use App\Http\Controllers\Petugas\Dashboard\ProfileController;
use App\Http\Controllers\Petugas\Pengumuman\PengumumanController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanKKBaruController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanLainnyaController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKUsahaController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanKKHilangController;
use App\Http\Controllers\Petugas\Dashboard\DocumentVerificationController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKDomisiliController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKAhliWarisController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKKelahiranController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKPerkawinanController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanSKTidakMampuController;
use App\Http\Controllers\Petugas\Permohonan\PermohonanKKPerubahanDataController;


// --- RUTE PUBLIK ---
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/verify-document/{id}', [DocumentVerificationController::class, 'verify'])->name('verify.document');

// --- RUTE OTENTIKASI ---
require __DIR__.'/auth.php';

// --- RUTE HALAMAN UTAMA (/) ---
Route::get('/', function () {
    if (Auth::check() && Auth::user()->role == 'petugas') {
        return redirect()->route('petugas.dashboard');
    }
    return view('auth.login');
})->name('home');


// =================================================================================
// GRUP UTAMA UNTUK SEMUA RUTE PETUGAS
// =================================================================================
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {

    // --- Dashboard & Notifikasi ---
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
   Route::get('/notifikasi/check', [NotificationController::class, 'check'])->name('notifikasi.check');
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/baca/{id}', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');

    // --- Profile Petugas ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Manajemen Akun Masyarakat ---
    Route::prefix('masyarakat')->name('masyarakat.')->group(function () {
        Route::get('/', [PetugasController::class, 'masyarakatIndex'])->name('index');
        Route::get('/{masyarakat}', [PetugasController::class, 'masyarakatShow'])->name('show');
        Route::post('/{masyarakat}/update-status', [PetugasController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/{masyarakat}/reset-password', [PetugasController::class, 'showResetPasswordFormByPetugas'])->name('showResetPasswordFormByPetugas');
        Route::post('/{masyarakat}/reset-password', [PetugasController::class, 'resetPasswordByPetugas'])->name('resetPasswordByPetugas');
    });

    // --- Manajemen Pengumuman ---
    Route::resource('pengumuman', PengumumanController::class);

    // =================================================================================
    // RUTE SEMUA JENIS PERMOHONAN DENGAN ALUR BARU (VERIFIKASI & EDIT)
    // =================================================================================
    $allPermohonanRoutes = [
        // Rute KK
        'permohonan-kk-baru'       => PermohonanKKBaruController::class,
        'permohonan-kk-hilang'     => PermohonanKKHilangController::class,
        'permohonan-kk-perubahan'  => PermohonanKKPerubahanDataController::class,
        // Rute SK
        'permohonan-sk-domisili'   => PermohonanSKDomisiliController::class,
        'permohonan-sk-kelahiran'  => PermohonanSKKelahiranController::class,
        'permohonan-sk-perkawinan' => PermohonanSKPerkawinanController::class,
        'permohonan-sk-tidak-mampu'=> PermohonanSKTidakMampuController::class,
        'permohonan-sk-usaha'      => PermohonanSKUsahaController::class,
        'permohonan-sk-ahli-waris' => PermohonanSKAhliWarisController::class,
    ];

    foreach ($allPermohonanRoutes as $uri => $controller) {
        Route::prefix($uri)->name($uri . '.')->group(function () use ($controller) {
            Route::get('/', [$controller, 'index'])->name('index');
            Route::get('/{id}', [$controller, 'show'])->name('show');
            Route::post('/{id}/verifikasi', [$controller, 'verifikasi'])->name('verifikasi');
            Route::post('/{id}/tolak', [$controller, 'tolak'])->name('tolak');
            
            // Rute baru untuk menampilkan halaman edit
            Route::get('/{id}/edit-surat', [$controller, 'editSurat'])->name('edit-surat');
            
            // Rute untuk memproses form edit dan menyelesaikan permohonan
            Route::post('/{id}/selesaikan', [$controller, 'selesaikan'])->name('selesaikan');
            
            Route::get('/{id}/download-final', [$controller, 'downloadFinal'])->name('download-final');
        });
    }
    
    // --- RUTE PERMOHONAN LAINNYA (ALUR KHUSUS) ---
    Route::prefix('permohonan-lainnya')->name('permohonan-lainnya.')->group(function () {
        $controller = PermohonanLainnyaController::class;
        Route::get('/', [$controller, 'index'])->name('index');
        Route::get('/{id}', [$controller, 'show'])->name('show');
        Route::post('/{id}/tolak', [$controller, 'tolak'])->name('tolak');
        Route::get('/{id}/create-surat', [$controller, 'createSurat'])->name('create-surat');
        Route::post('/{id}/generate-surat', [$controller, 'generateSurat'])->name('generate-surat');
        Route::get('/{id}/download-final', [$controller, 'downloadFinal'])->name('download-final');
    });

});
