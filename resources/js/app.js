// Lokasi: resources/js/app.js

// Kita tidak perlu lagi mengimpor jquery, bootstrap, atau datatables di sini
// karena sudah dimuat dari CDN.
import "./bootstrap"; // Ini hanya untuk koneksi Echo/Pusher, biarkan saja.

// Kita hanya impor dan jalankan skrip spesifik dari template kita.
import "./sb-admin-2.min.js";

// Inisialisasi DataTables dengan konfigurasi yang Anda inginkan
$(document).ready(function () {
    if ($("#dataTable").length > 0) {
        $("#dataTable").DataTable({
            paging: true, // Pagination dari DataTables DIAKTIFKAN
            info: true, // Info "Showing 1 to X..." DIAKTIFKAN
            searching: false,
            order: [[3, "desc"]], // Contoh: urutkan berdasarkan kolom tanggal
        });
    }
});
