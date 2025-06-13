/**
 * File: notifikasi-polling.js
 * Deskripsi: Skrip untuk melakukan AJAX Polling ke server Laravel
 * guna memeriksa notifikasi baru secara real-time untuk petugas.
 */

$(document).ready(function () {
    // 1. DEFINISI ELEMEN PENTING
    // Mengambil elemen dari navbar.blade.php berdasarkan ID-nya.
    const notifCounter = $("#notifikasi-counter");
    const notifList = $("#notifikasi-list");
    const defaultMessage =
        '<a class="dropdown-item text-center small text-gray-500 no-notification" href="#">Tidak ada notifikasi baru</a>';

    /**
     * Fungsi untuk mengupdate tampilan (UI) notifikasi di navbar.
     * @param {number} jumlah - Jumlah total notifikasi yang belum dibaca.
     * @param {Array} data - Array berisi objek notifikasi.
     */
    function updateNotifikasiUI(jumlah, data) {
        if (jumlah > 0) {
            // Jika ada notifikasi baru, tampilkan angkanya di ikon lonceng.
            notifCounter.text(jumlah).show();

            // Kosongkan daftar notifikasi lama dan isi dengan yang baru.
            notifList.empty();
            data.forEach((notif) => {
                // Format tanggal agar mudah dibaca (misal: 13 Juni 2025)
                const tanggal = new Date(notif.created_at).toLocaleDateString(
                    "id-ID",
                    {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                    }
                );

                // Membuat satu baris item notifikasi HTML
                const notifHtml = `
                    <a class="dropdown-item d-flex align-items-center" href="${notif.url}" data-id="${notif.id}">
                        <div class="mr-3">
                            <div class="icon-circle ${notif.warna_ikon}">
                                <i class="${notif.tipe_ikon} text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">${tanggal}</div>
                            <span class="font-weight-bold">${notif.judul}: ${notif.pesan}</span>
                        </div>
                    </a>
                `;
                notifList.append(notifHtml);
            });
        } else {
            // Jika tidak ada notifikasi, sembunyikan angka dan tampilkan pesan default.
            notifCounter.hide();
            notifList.html(defaultMessage);
        }
    }

    /**
     * Fungsi yang akan dijalankan berulang kali untuk memeriksa server.
     */
    function pollNotifikasi() {
        // Mengirim request GET ke API endpoint yang sudah kita buat.
        $.get("/api/notifikasi/baru", function (response) {
            updateNotifikasiUI(response.jumlah_baru, response.data);
        }).fail(function () {
            console.error(
                "Gagal mengambil data notifikasi. Mungkin sesi telah berakhir."
            );
            // Hentikan polling jika gagal (misalnya karena user logout atau error server)
            clearInterval(pollingInterval);
        });
    }

    /**
     * Fungsi untuk menandai notifikasi sebagai "sudah dibaca" saat petugas membuka dropdown.
     */
    $("#alertsDropdown").on("click", function () {
        // Kumpulkan ID dari notifikasi yang sedang ditampilkan di dropdown.
        const idsToMark = [];
        notifList.find("a[data-id]").each(function () {
            idsToMark.push($(this).data("id"));
        });

        // Jika tidak ada notifikasi baru di dropdown, tidak perlu melakukan apa-apa.
        if (idsToMark.length === 0) {
            return;
        }

        // Kirim request POST ke server untuk menandai notifikasi sebagai sudah dibaca.
        $.post("/api/notifikasi/tandai-dibaca", {
            _token: $('meta[name="csrf-token"]').attr("content"), // Mengambil CSRF token dari meta tag
            ids: idsToMark,
        })
            .done(function () {
                // Langsung set counter jadi 0 di tampilan agar terasa instan.
                notifCounter.hide();
            })
            .fail(function () {
                console.error(
                    "Gagal menandai notifikasi sebagai sudah dibaca."
                );
            });
    });

    // 2. EKSEKUSI UTAMA
    // Jalankan fungsi `pollNotifikasi` setiap 10 detik (10000 milidetik).
    // Anda bisa mengubah angka ini jika ingin lebih cepat atau lebih lambat.
    const pollingInterval = setInterval(pollNotifikasi, 10000);

    // Jalankan juga fungsi ini sekali saat halaman pertama kali dimuat,
    // agar tidak perlu menunggu 10 detik untuk tampilan notifikasi pertama.
    pollNotifikasi();
});
