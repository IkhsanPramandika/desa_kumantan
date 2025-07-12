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
Route::get('/pengumuman', [PengumumanApiController::class, 'index'])->name('api.pengumuman.index');
Route::get('/pengumuman/{slug}', [PengumumanApiController::class, 'show'])->name('api.pengumuman.show');

// Grup untuk registrasi, login, dan lupa password
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
        Route::put('/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('baca.semua');
        Route::post('/user/update-fcm-token', [NotificationController::class, 'updateFcmToken']);
    });

    /*
    |--------------------------------------------------------------------------
    | >> SEMUA RUTE APLIKASI MOBILE DI DALAM GRUP INI <<
    |--------------------------------------------------------------------------
    | Ini membuat semua URL konsisten: /api/masyarakat/...
    */
    Route::prefix('masyarakat')->name('api.masyarakat.auth.')->group(function () {
        
        // --- Profil, Logout, Ganti Password ---
        Route::get('profil', [MasyarakatAuthController::class, 'profil'])->name('profil');
        Route::put('profil', [MasyarakatAuthController::class, 'updateProfil'])->name('updateProfil');
        Route::put('password', [MasyarakatAuthController::class, 'changePassword'])->name('password.change');
        Route::post('logout', [MasyarakatAuthController::class, 'logout'])->name('logout');

        // --- Riwayat & Detail Permohonan (Terpusat) ---
        Route::get('riwayat-semua-permohonan', [RiwayatPermohonanController::class, 'index'])->name('riwayat.index');
        Route::get('permohonan/{jenis_surat_slug}/{id}', [RiwayatPermohonanController::class, 'show'])->name('riwayat.detail');

        // --- Pengajuan & Download Permohonan ---
        $addDownloadRoute = function ($prefix, $controller) {
            Route::get("{$prefix}/{id}/download", [$controller, 'downloadHasil'])->name("{$prefix}.download");
        };

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

         /*
        |--------------------------------------------------------------------------
        | >> BLOK BARU: RUTE UNTUK DRAFT PERMOHONAN <<
        |--------------------------------------------------------------------------
        */
        Route::prefix('draft')->name('draft.')->group(function() {
            // Contoh untuk SK Usaha. Ulangi pola ini untuk permohonan lain.
            Route::post('permohonan-sk-usaha', [\App\Http\Controllers\Api\Permohonan\SKUsahaApiController::class, 'storeAsDraft'])->name('sk-usaha.store');
            Route::put('permohonan-sk-usaha/{id}', [\App\Http\Controllers\Api\Permohonan\SKUsahaApiController::class, 'updateDraft'])->name('sk-usaha.update');
            Route::delete('permohonan-sk-usaha/{id}', [\App\Http\Controllers\Api\Permohonan\SKUsahaApiController::class, 'destroyDraft'])->name('sk-usaha.destroy');


        
    });
});

// Rute fallback standar
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


});
