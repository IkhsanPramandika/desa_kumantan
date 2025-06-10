import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",

    // [PENJELASAN] wsHost dan wsPort sudah ditangani secara default oleh konfigurasi di bawah,
    // kita bisa menyederhanakannya agar lebih rapi.

    // [PERBAIKAN] Typo 'httpshttps' diubah menjadi 'https'
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",

    // [PERBAIKAN PENTING] Menambahkan konfigurasi untuk autentikasi channel privat
    authEndpoint: "/broadcasting/auth",
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                axios
                    .post("/broadcasting/auth", {
                        socket_id: socketId,
                        channel_name: channel.name,
                    })
                    .then((response) => {
                        callback(false, response.data);
                    })
                    .catch((error) => {
                        callback(true, error);
                    });
            },
        };
    },
});
