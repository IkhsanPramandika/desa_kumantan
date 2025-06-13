<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('notifikasi-petugas', function (User $user) {
    
    Log::info('[AUTH CHANNEL] Memverifikasi izin untuk User ID: ' . $user->id);

    // [PERBAIKAN FINAL] Kita periksa rolenya dengan sangat spesifik.
    if ($user->role === 'petugas') {
        
        // [PERUBAHAN] Mengembalikan 'true' adalah cara paling sederhana dan
        // anti gagal untuk memberikan izin. Laravel akan mengurus format response-nya.
        Log::info('[AUTH CHANNEL] Izin DIBERIKAN untuk User ID: ' . $user->id);
        return true;

    }

    Log::warning('[AUTH CHANNEL] Izin DITOLAK untuk User ID: ' . $user->id . '. Role bukan "petugas".');
    return false;
});
