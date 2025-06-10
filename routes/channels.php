<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel untuk notifikasi petugas yang sudah kita buat sebelumnya
Broadcast::channel('notifikasi-petugas', function ($user) {
    // Ganti 'petugas' dengan nama role yang Anda gunakan jika berbeda
    return $user->hasRole('petugas'); 
});