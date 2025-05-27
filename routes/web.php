<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermohonanKKBaru; // Ini mungkin tidak perlu jika tidak langsung digunakan
use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\DashboardController; // Hapus atau komentari ini jika Anda tidak menggunakan DashboardController lagi
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\PermohonanKKBaruController;
use App\Http\Controllers\PermohonanSKUsahaController;
use App\Http\Controllers\PermohonanKKHilangController;
use App\Http\Controllers\PermohonanSKDomisiliController;
use App\Http\Controllers\PermohonanSKKematianController;
use App\Http\Controllers\PermohonanSKAhliWarisController;
use App\Http\Controllers\PermohonanSKKelahiranController;
use App\Http\Controllers\PermohonanSKPerkawinanController;
use App\Http\Controllers\PermohonanSKTidakMampuController;
use App\Http\Controllers\PermohonanKKPerubahanDataController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PetugasController; // PENTING: Pastikan ini diimpor!


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
require __DIR__.'/auth.php';


//petugas : dashboard
Route::get('petugas/dashboard', [PetugasController::class, 'dashboard'])->name('petugas.dashboard');


//route search
Route::get('/search', [SearchController::class, 'index'])->name('search');


//pentugas : pengajuan
Route::middleware(['auth'])->group(function () {

    // Route pengajuan surat
    Route::prefix('petugas')->group(function () {
        Route::get('/pengajuan', [PengajuanSuratController::class, 'index'])->name('petugas.pengajuan.index');
        Route::get('/pengajuan/create', [PengajuanSuratController::class, 'create'])->name('petugas.pengajuan.create');
        Route::post('/pengajuan', [PengajuanSuratController::class, 'store'])->name('petugas.pengajuan.store');
        Route::get('/pengajuan/{pengajuan}', [PengajuanSuratController::class, 'show'])->name('petugas.pengajuan.show');
    });


    Route::prefix('petugas')->group(function () {

    // --- Permohonan KK Baru ---
    Route::prefix('permohonan-kk-baru')->name('permohonan-kk.')->group(function () {
        Route::get('/', [PermohonanKKBaruController::class, 'index'])->name('index');
        Route::get('/create', [PermohonanKKBaruController::class, 'create'])->name('create');
        Route::post('/', [PermohonanKKBaruController::class, 'store'])->name('store');
        Route::post('/{id}/verifikasi', [PermohonanKKBaruController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/{id}/tolak', [PermohonanKKBaruController::class, 'tolak'])->name('tolak');

    // Route baru untuk mengunggah PDF final
    Route::post('/{id}/upload-final-pdf', [PermohonanKKBaruController::class, 'uploadFinalPdf'])->name('upload-final-pdf');

    // Route untuk mengunduh dokumen hasil akhir (pastikan ini ada)
    Route::get('/{id}/download-final', [PermohonanKKBaruController::class, 'downloadFinal'])->name('download-final');
    });

   // --- Permohonan KK Hilang ---
    Route::prefix('permohonan-kk-hilang')->name('permohonan-kk-hilang.')->group(function () {
        Route::get('/', [PermohonanKKHilangController::class, 'index'])->name('index');
        Route::get('/create', [PermohonanKKHilangController::class, 'create'])->name('create');
        Route::post('/', [PermohonanKKHilangController::class, 'store'])->name('store');
        Route::post('/{id}/verifikasi', [PermohonanKKHilangController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/{id}/tolak', [PermohonanKKHilangController::class, 'tolak'])->name('tolak');
        Route::post('/{id}/upload-final-pdf', [PermohonanKKHilangController::class, 'uploadFinalPdf'])->name('upload-final-pdf');
        Route::get('/{id}/download-final', [PermohonanKKHilangController::class, 'downloadFinal'])->name('download-final');
    });
    
    // --- Permohonan KK Perubahan Data ---
    Route::prefix('permohonan-kk-perubahan')->name('permohonan-kk-perubahan.')->group(function () {
        Route::get('/', [PermohonanKKPerubahanDataController::class, 'index'])->name('index');
        Route::get('/create', [PermohonanKKPerubahanDataController::class, 'create'])->name('create');
        Route::post('/', [PermohonanKKPerubahanDataController::class, 'store'])->name('store');
        Route::post('/{id}/verifikasi', [PermohonanKKPerubahanDataController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/{id}/tolak', [PermohonanKKPerubahanDataController::class, 'tolak'])->name('tolak');
        Route::post('/{id}/upload-final-pdf', [PermohonanKKPerubahanDataController::class, 'uploadFinalPdf'])->name('upload-final-pdf');
        Route::get('/{id}/download-final', [PermohonanKKPerubahanDataController::class, 'downloadFinal'])->name('download-final');
    });

    // --- Permohonan SK Domisili ---
    Route::get('/permohonan-sk-domisili', [PermohonanSKDomisiliController::class, 'index'])->name('permohonan-sk-domisili.index');
    Route::post('/permohonan-sk-domisili/{id}/verifikasi', [PermohonanSKDomisiliController::class, 'verifikasi'])->name('permohonan-sk-domisili.verifikasi');
    Route::post('/permohonan-sk-domisili/{id}/tolak', [PermohonanSKDomisiliController::class, 'tolak'])->name('permohonan-sk-domisili.tolak');

   // --- Permohonan SK Kelahiran ---
    Route::prefix('permohonan-sk-kelahiran')->name('permohonan-sk-kelahiran.')->group(function () {
        Route::get('/', [PermohonanSKKelahiranController::class, 'index'])->name('index');
        Route::get('/create', [PermohonanSKKelahiranController::class, 'create'])->name('create'); // Form upload dokumen oleh masyarakat
        Route::post('/', [PermohonanSKKelahiranController::class, 'store'])->name('store'); // Menyimpan upload dokumen
        Route::post('/{id}/verifikasi', [PermohonanSKKelahiranController::class, 'verifikasi'])->name('verifikasi'); // Verifikasi dokumen
        Route::get('/{id}/input-data', [PermohonanSKKelahiranController::class, 'inputData'])->name('input-data'); // Form input data rinci oleh petugas
        Route::post('/{id}/store-data-and-generate-pdf', [PermohonanSKKelahiranController::class, 'storeDataAndGeneratePdf'])->name('store-data-and-generate-pdf'); // Menyimpan data & generate PDF
        Route::post('/{id}/tolak', [PermohonanSKKelahiranController::class, 'tolak'])->name('tolak');
        Route::get('/{id}/download-final', [PermohonanSKKelahiranController::class, 'downloadFinal'])->name('download-final');
    });

    
    // --- Permohonan SK Perkawinan ---
    Route::get('/permohonan-sk-perkawinan', [PermohonanSKPerkawinanController::class, 'index'])->name('permohonan-sk-perkawinan.index');
    Route::post('/permohonan-sk-perkawinan/{id}/verifikasi', [PermohonanSKPerkawinanController::class, 'verifikasi'])->name('permohonan-sk-perkawinan.verifikasi');
    Route::post('/permohonan-sk-perkawinan/{id}/tolak', [PermohonanSKPerkawinanController::class, 'tolak'])->name('permohonan-sk-perkawinan.tolak');

    // --- Permohonan SK Tidak Mampu ---
    Route::get('/permohonan-sk-tidak-mampu', [PermohonanSKTidakMampuController::class, 'index'])->name('permohonan-sk-tidak-mampu.index');
    Route::post('/permohonan-sk-tidak-mampu/{id}/verifikasi', [PermohonanSKTidakMampuController::class, 'verifikasi'])->name('permohonan-sk-tidak-mampu.verifikasi');
    Route::post('/permohonan-sk-tidak-mampu/{id}/tolak', [PermohonanSKTidakMampuController::class, 'tolak'])->name('permohonan-sk-tidak-mampu.tolak');

    // --- Permohonan SK Usaha ---
    Route::get('/permohonan-sk-usaha', [PermohonanSKUsahaController::class, 'index'])->name('permohonan-sk-usaha.index');
    Route::post('/permohonan-sk-usaha/{id}/verifikasi', [PermohonanSKUsahaController::class, 'verifikasi'])->name('permohonan-sk-usaha.verifikasi');
    Route::post('/permohonan-sk-usaha/{id}/tolak', [PermohonanSKUsahaController::class, 'tolak'])->name('permohonan-sk-usaha.tolak');

    // --- Permohonan SK Ahli Waris ---
    Route::get('/permohonan-sk-ahli-waris', [PermohonanSKAhliWarisController::class, 'index'])->name('permohonan-sk-ahli-waris.index');
    Route::post('/permohonan-sk-ahli-waris/{id}/verifikasi', [PermohonanSKAhliWarisController::class, 'verifikasi'])->name('permohonan-sk-ahli-waris.verifikasi');
    Route::post('/permohonan-sk-ahli-waris/{id}/tolak', [PermohonanSKAhliWarisController::class, 'tolak'])->name('permohonan-sk-ahli-waris.tolak');

    });

});
