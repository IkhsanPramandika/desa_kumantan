<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Controller untuk Autentikasi & Notifikasi ---
use App\Http\Controllers\Api\Auth\MasyarakatAuthController;
use App\Http\Controllers\Api\Auth\MasyarakatForgotPasswordController;
use App\Http\Controllers\Api\Auth\MasyarakatResetPasswordController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Pengumuman\PengumumanApiController;
// --- Controller untuk Riwayat Terpusat ---
use App\Http\Controllers\Api\Permohonan\RiwayatPermohonanController;

// --- Controller untuk Setiap Jenis Permohonan ---
use App\Http\Controllers\Api\Permohonan\KKBaruApiController;
use App\Http\Controllers\Api\Permohonan\KKHilangApiController;
use App\Http\Controllers\Api\Permohonan\KKPerubahanApiController;
use App\Http\Controllers\Api\Permohonan\SKAhliWarisApiController;
use App\Http\Controllers\Api\Permohonan\SKDomisiliApiController;
use App\Http\Controllers\Api\Permohonan\SKKelahiranApiController;
use App\Http\Controllers\Api\Permohonan\SKPerkawinanApiController;
use App\Http\Controllers\Api\Permohonan\SKTidakMampuApiController;
use App\Http\Controllers\Api\Permohonan\SKUsahaApiController;

/*
|--------------------------------------------------------------------------
| Rute API Publik (Tidak Perlu Login)
|--------------------------------------------------------------------------
*/
Route::prefix('pengumuman')->name('api.pengumuman.')->group(function () {
    Route::get('/', [PengumumanApiController::class, 'index'])->name('index');
    Route::get('/{slug}', [PengumumanApiController::class, 'show'])->name('show');
});

Route::prefix('masyarakat')->name('api.masyarakat.')->group(function () {
    Route::post('register', [MasyarakatAuthController::class, 'register'])->name('register');
    Route::post('login', [MasyarakatAuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [MasyarakatForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('reset-password', [MasyarakatResetPasswordController::class, 'reset'])->name('password.update');
});


/*
|--------------------------------------------------------------------------
| Rute API yang Memerlukan Login (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // --- Rute untuk Notifikasi ---
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/petugas/notifikasi/baca/{id}', [NotificationController::class, 'readAndRedirect'])->name('petugas.notifikasi.read');
        Route::put('/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('baca.semua'); // <-- TAMBAHKAN INI
         Route::post('/user/update-fcm-token', [NotificationController::class, 'updateFcmToken'])->middleware('auth:sanctum');
        
        });

    // --- Rute untuk Profil & Logout ---
    Route::prefix('masyarakat')->name('api.masyarakat.auth.')->group(function () {
        Route::post('logout', [MasyarakatAuthController::class, 'logout'])->name('logout');
        Route::get('profil', [MasyarakatAuthController::class, 'profil'])->name('profil');
        Route::put('profil', [MasyarakatAuthController::class, 'updateProfil'])->name('updateProfil');
            Route::put('password', [MasyarakatAuthController::class, 'changePassword'])->name('password.change');
    });

    // --- Rute untuk Riwayat Terpusat ---
    Route::get('/riwayat-semua-permohonan', [RiwayatPermohonanController::class, 'index']);
    Route::get('/masyarakat/riwayat-semua-permohonan', [RiwayatPermohonanController::class, 'index'])->name('api.masyarakat.auth.riwayat.semua');
    
    /*
    |--------------------------------------------------------------------------
    | Grup Rute untuk Semua Jenis Permohonan
    |--------------------------------------------------------------------------
    | Menggunakan Route::apiResource untuk menyingkat definisi rute
    | index(), store(), dan show(). Rute 'download' didefinisikan secara manual.
    */
    Route::prefix('masyarakat')->name('api.masyarakat.auth.')->group(function () {
        // Fungsi helper untuk membuat rute download
        $addDownloadRoute = function ($prefix, $controller) {
            Route::get("/{$prefix}/{id}/download", [$controller, 'downloadHasil'])->name("{$prefix}.download");
        };

        // Menggunakan apiResource untuk menyingkat
        Route::apiResource('permohonan-kk-baru', KKBaruApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-kk-baru', KKBaruApiController::class);

        Route::apiResource('permohonan-kk-hilang', KKHilangApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-kk-hilang', KKHilangApiController::class);

        Route::apiResource('permohonan-kk-perubahan-data', KKPerubahanApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-kk-perubahan-data', KKPerubahanApiController::class);
        
        Route::apiResource('permohonan-sk-ahli-waris', SKAhliWarisApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-sk-ahli-waris', SKAhliWarisApiController::class);
        
        Route::apiResource('permohonan-sk-domisili', SKDomisiliApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-sk-domisili', SKDomisiliApiController::class);

        Route::apiResource('permohonan-sk-kelahiran', SKKelahiranApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-sk-kelahiran', SKKelahiranApiController::class);

        Route::apiResource('permohonan-sk-perkawinan', SKPerkawinanApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-sk-perkawinan', SKPerkawinanApiController::class);

        Route::apiResource('permohonan-sk-tidak-mampu', SKTidakMampuApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-sk-tidak-mampu', SKTidakMampuApiController::class);

        Route::apiResource('permohonan-sk-usaha', SKUsahaApiController::class)->except(['update', 'destroy']);
        $addDownloadRoute('permohonan-sk-usaha', SKUsahaApiController::class);

       Route::get('/permohonan/{jenis_surat_slug}/{id}', [RiwayatPermohonanController::class, 'show']);
    });

});

// Rute fallback standar
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

