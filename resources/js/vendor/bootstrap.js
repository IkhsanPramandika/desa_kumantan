import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY, // Key bisa tetap dari .env

    // =================================================================
    // UJI COBA FINAL: Tulis langsung (hardcode) alamat server
    // untuk memastikan masalah bukan pada pembacaan file .env.
    wsHost: "127.0.0.1",
    wsPort: 8088,
    wssPort: 8088,
    forceTLS: false,
    disableStats: true, // Menonaktifkan fitur tambahan untuk menyederhanakan koneksi
    enabledTransports: ["ws"], // Memaksa hanya koneksi 'ws', bukan 'wss'
    // =================================================================
});
