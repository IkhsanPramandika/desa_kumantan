<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Auth\MasyarakatAuthController;
use App\Http\Controllers\Api\Permohonan\KKBaruApiController;
use App\Http\Controllers\Api\Permohonan\SKUsahaApiController;
use App\Http\Controllers\Api\Permohonan\KKHilangApiController;
use App\Http\Controllers\Api\Pengumuman\PengumumanApiController;
use App\Http\Controllers\Api\Permohonan\SKDomisiliApiController;
use App\Http\Controllers\Api\Permohonan\KKPerubahanApiController;
use App\Http\Controllers\Api\Permohonan\SKAhliWarisApiController;
use App\Http\Controllers\Api\Permohonan\SKKelahiranApiController;
use App\Http\Controllers\Api\Permohonan\SKPerkawinanApiController;
use App\Http\Controllers\Api\Permohonan\SKTidakMampuApiController;
use App\Http\Controllers\Api\Auth\MasyarakatResetPasswordController;
use App\Http\Controllers\Api\Permohonan\RiwayatPermohonanController;
use App\Http\Controllers\Api\Auth\MasyarakatForgotPasswordController;
use App\Http\Controllers\Api\Permohonan\PermohonanLainnyaApiController;

Route::prefix('pengumuman')->name('api.pengumuman.')->group(function () {
    Route::get('/', [PengumumanApiController::class, 'index'])->name('index');
    Route::post('/', [PengumumanApiController::class, 'store'])->name('store')->middleware('auth:sanctum'); // Tambahkan middleware untuk proteksi
    Route::get('/{slug}', [PengumumanApiController::class, 'show'])->name('show');
});
Route::prefix('masyarakat')->name('api.masyarakat.')->group(function () {
    Route::post('register', [MasyarakatAuthController::class, 'register'])->name('register');
    Route::post('login', [MasyarakatAuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [MasyarakatForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('reset-password', [MasyarakatResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::put('/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('baca.semua');
        Route::post('/user/update-fcm-token', [NotificationController::class, 'updateFcmToken']);
    });

    Route::prefix('masyarakat')->name('api.masyarakat.auth.')->group(function () {
        Route::get('profil', [MasyarakatAuthController::class, 'profil'])->name('profil');
        Route::put('profil', [MasyarakatAuthController::class, 'updateProfil'])->name('updateProfil');
        Route::put('password', [MasyarakatAuthController::class, 'changePassword'])->name('password.change');
        Route::post('logout', [MasyarakatAuthController::class, 'logout'])->name('logout');

        Route::get('riwayat-semua-permohonan', [RiwayatPermohonanController::class, 'index'])->name('riwayat.index');
        Route::get('permohonan/{jenis_surat_slug}/{id}', [RiwayatPermohonanController::class, 'show'])->name('riwayat.detail');

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

        Route::apiResource('permohonan-lainnya', PermohonanLainnyaApiController::class)->except(['update', 'destroy', 'index', 'show']);
         $addDownloadRoute('permohonan-lainnya', PermohonanLainnyaApiController::class);

        Route::prefix('draft')->name('draft.')->group(function() {
            $draftRoutes = [
                'permohonan-kk-baru' => KKBaruApiController::class,
                'permohonan-kk-hilang' => KKHilangApiController::class,
                'permohonan-kk-perubahan-data' => KKPerubahanApiController::class,
                'permohonan-sk-ahli-waris' => SKAhliWarisApiController::class,
                'permohonan-sk-domisili' => SKDomisiliApiController::class,
                'permohonan-sk-kelahiran' => SKKelahiranApiController::class,
                'permohonan-sk-perkawinan' => SKPerkawinanApiController::class,
                'permohonan-sk-tidak-mampu' => SKTidakMampuApiController::class,
                'permohonan-sk-usaha' => SKUsahaApiController::class,
                'permohonan-lainnya' => PermohonanLainnyaApiController::class
            ];

            foreach ($draftRoutes as $uri => $controller) {
                Route::post($uri, [$controller, 'storeAsDraft'])->name($uri . '.store');
                Route::put($uri . '/{id}', [$controller, 'updateDraft'])->name($uri . '.update');
                Route::delete($uri . '/{id}', [$controller, 'destroyDraft'])->name($uri . '.destroy');
            }
        });
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
