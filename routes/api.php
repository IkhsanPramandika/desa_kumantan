<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controller untuk Autentikasi Masyarakat
use App\Http\Controllers\Api\Auth\MasyarakatAuthController;
use App\Http\Controllers\Api\Permohonan\KKBaruApiController;
use App\Http\Controllers\Api\Permohonan\SKUsahaApiController;

// Controller untuk Pengumuman


// Controller untuk Permohonan Surat (oleh Masyarakat)

use App\Http\Controllers\Api\Pengumuman\PengumumanApiController; 

use App\Http\Controllers\Api\Permohonan\SKDomisiliApiController;
use App\Http\Controllers\Api\Permohonan\KKHilangApiController;
use App\Http\Controllers\Api\Permohonan\SKAhliWarisApiController;
use App\Http\Controllers\Api\Permohonan\SKKelahiranApiController;
use App\Http\Controllers\Api\Permohonan\KKPerubahanApiController; 
use App\Http\Controllers\Api\Permohonan\SKPerkawinanApiController;
use App\Http\Controllers\Api\Permohonan\SKTidakMampuApiController;
use App\Http\Controllers\Api\Auth\MasyarakatResetPasswordController;
use App\Http\Controllers\Api\Auth\MasyarakatForgotPasswordController;


// Rute API Publik (tidak memerlukan autentikasi)
// URL akan menjadi /api/pengumuman, /api/pengumuman/{slug}


Route::prefix('pengumuman')->name('api.pengumuman.')->group(function () {
    Route::get('/', [PengumumanApiController::class, 'index'])->name('index');
    Route::get('/{slug}', [PengumumanApiController::class, 'show'])->name('show');
});

// Rute API untuk Autentikasi Masyarakat
// URL akan menjadi /api/masyarakat/register, /api/masyarakat/login, dst.
Route::prefix('masyarakat')->name('api.masyarakat.')->group(function () {
    Route::post('register', [MasyarakatAuthController::class, 'register'])->name('register');
    Route::post('login', [MasyarakatAuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [MasyarakatForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('reset-password', [MasyarakatResetPasswordController::class, 'reset'])->name('password.update');
});

 


// Rute API yang Memerlukan Autentikasi Masyarakat (Sanctum)
// URL akan menjadi /api/masyarakat/logout, /api/masyarakat/profil, 
// /api/masyarakat/permohonan-kk-baru, dll.
Route::middleware('auth:sanctum')->prefix('masyarakat')->name('api.masyarakat.auth.')->group(function () {
    Route::post('logout', [MasyarakatAuthController::class, 'logout'])->name('logout');
    Route::get('profil', [MasyarakatAuthController::class, 'profil'])->name('profil');
    Route::put('profil', [MasyarakatAuthController::class, 'updateProfil'])->name('updateProfil');
    
    // --- API untuk Permohonan Surat oleh Masyarakat ---
    Route::prefix('permohonan-kk-baru')->name('permohonan-kk-baru.')->group(function(){
        Route::get('/', [KKBaruApiController::class, 'index'])->name('index');
        Route::post('/', [KKBaruApiController::class, 'store'])->name('store');
        Route::get('/{id}', [KKBaruApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [KKBaruApiController::class, 'downloadHasil'])->name('download'); // Pastikan method downloadHasil ada di controller
    });

    Route::prefix('permohonan-sk-domisili')->name('permohonan-sk-domisili.')->group(function(){
        Route::get('/', [SKDomisiliApiController::class, 'index'])->name('index');
        Route::post('/', [SKDomisiliApiController::class, 'store'])->name('store');
        Route::get('/{id}', [SKDomisiliApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [SKDomisiliApiController::class, 'downloadHasil'])->name('download'); // Pastikan method downloadHasil ada
    });

    Route::prefix('permohonan-kk-hilang')->name('permohonan-kk-hilang.')->group(function(){
        Route::get('/', [KKHilangApiController::class, 'index'])->name(name: 'index');
        Route::post('/', [KKHilangApiController::class, 'store'])->name('store');
        Route::get('/{id}', [KKHilangApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [KKHilangApiController::class, 'downloadHasil'])->name('download');
    });

     Route::prefix('permohonan-kk-perubahan-data')->name('permohonan-kk-perubahan-data.')->group(function(){
        Route::get('/', [KKPerubahanApiController::class, 'index'])->name('index');
        Route::post('/', [KKPerubahanApiController::class, 'store'])->name('store');
        Route::get('/{id}', [KKPerubahanApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [KKPerubahanApiController::class, 'downloadHasil'])->name('download');
    });

    Route::prefix('permohonan-sk-ahli-waris')->name('permohonan-sk-ahli-waris.')->group(function(){
        Route::get('/', [SKAhliWarisApiController::class, 'index'])->name('index');
        Route::post('/', [SKAhliWarisApiController::class, 'store'])->name('store');
        Route::get('/{id}', [SKAhliWarisApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [SKAhliWarisApiController::class, 'downloadHasil'])->name('download');
    });

    Route::prefix('permohonan-sk-kelahiran')->name('permohonan-sk-kelahiran.')->group(function(){
        Route::get('/', [SKKelahiranApiController::class, 'index'])->name('index');
        Route::post('/', [SKKelahiranApiController::class, 'store'])->name('store');
        Route::get('/{id}', [SKKelahiranApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [SKKelahiranApiController::class, 'downloadHasil'])->name('download');
    });

    Route::prefix('permohonan-sk-perkawinan')->name('permohonan-sk-perkawinan.')->group(function(){
        Route::get('/', [SKPerkawinanApiController::class, 'index'])->name('index');
        Route::post('/', [SKPerkawinanApiController::class, 'store'])->name('store');
        Route::get('/{id}', [SKPerkawinanApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [SKPerkawinanApiController::class, 'downloadHasil'])->name('download');
    });

    Route::prefix('permohonan-sk-tidak-mampu')->name('permohonan-sk-tidak-mampu.')->group(function(){
        Route::get('/', [SKTidakMampuApiController::class, 'index'])->name('index');
        Route::post('/', [SKTidakMampuApiController::class, 'store'])->name('store');
        Route::get('/{id}', [SKTidakMampuApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [SKTidakMampuApiController::class, 'downloadHasil'])->name('download');
    });

    Route::prefix('permohonan-sk-usaha')->name('permohonan-sk-usaha.')->group(function(){
        Route::get('/', [SKUsahaApiController::class, 'index'])->name('index');
        Route::post('/', [SKUsahaApiController::class, 'store'])->name('store');
        Route::get('/{id}', [SKUsahaApiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [SKUsahaApiController::class, 'downloadHasil'])->name('download');
    });
    
});



// Rute fallback jika user mencoba mengakses /api/user tanpa token yang valid (standar Laravel)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


