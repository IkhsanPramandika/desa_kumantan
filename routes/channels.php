<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan semua channel broadcast event yang didukung
| oleh aplikasi Anda. Callback otorisasi channel yang diberikan
| digunakan untuk memeriksa apakah pengguna yang terotentikasi dapat
| mendengarkan channel tersebut.
|
*/

// PASTIKAN BLOK KODE INI ADA DAN TIDAK DI DALAM KOMENTAR
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // Kode ini akan mengizinkan koneksi HANYA JIKA
    // ID user yang sedang login ($user->id) sama dengan ID
    // yang diminta di nama channel ({id}).
    return (int) $user->id === (int) $id;
});

