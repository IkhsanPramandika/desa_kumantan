import "./bootstrap"; // bootstrap.js sekarang bisa lebih kosong

// Variabel untuk menyimpan jumlah notifikasi terakhir, untuk menghindari update UI yang tidak perlu
let lastUnreadCount = -1;

/**
 * Fungsi untuk mengambil notifikasi dari server.
 */
async function fetchNotifications() {
    try {
        // =======================================================
        // PERBAIKAN: URL diubah ke route web, bukan route api
        const response = await fetch("/petugas/notifications/check", {
            // =======================================================
            method: "GET",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": document.head.querySelector(
                    'meta[name="csrf-token"]'
                )?.content,
            },
        });

        if (!response.ok) {
            // Menampilkan status error di console jika request gagal
            console.error(
                "Gagal mengambil notifikasi:",
                response.status,
                response.statusText
            );
            return;
        }

        const data = await response.json();

        // Hanya update UI jika jumlah notifikasi berubah untuk efisiensi
        if (data.unread_count !== lastUnreadCount) {
            console.log(
                `Notifikasi baru terdeteksi: ${data.unread_count} belum dibaca. Memperbarui UI...`
            );
            updateNotificationUI(data.unread_count, data.notifications);
            lastUnreadCount = data.unread_count;
        }
    } catch (error) {
        console.error("Terjadi error saat fetch notifikasi:", error);
    }
}

/**
 * Fungsi untuk memperbarui tampilan (UI) notifikasi di navbar.
 * @param {number} count - Jumlah notifikasi yang belum dibaca.
 * @param {array} notifications - Array berisi objek notifikasi.
 */
function updateNotificationUI(count, notifications) {
    // 1. Update badge angka
    const badge = document.getElementById("notification-badge");
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? "inline" : "none";
    }

    // 2. Update header di dalam dropdown
    const headerCount = document.getElementById("notification-header-count");
    if (headerCount) {
        headerCount.textContent = `${count} Notifikasi Baru`;
    }

    // 3. Update daftar notifikasi di dalam dropdown
    const dropdownList = document.getElementById("notification-dropdown-list");
    if (dropdownList) {
        // Kosongkan list yang lama sebelum mengisi dengan yang baru
        dropdownList.innerHTML = "";

        if (notifications.length > 0) {
            notifications.forEach((notif) => {
                // Template HTML untuk setiap item notifikasi
                const itemHtml = `
                    <a href="${notif.url}" class="dropdown-item d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">${notif.waktu}</div>
                            <span class="font-weight-bold">${notif.pesan}</span>
                        </div>
                    </a>
                `;
                // Tambahkan item baru ke dalam list
                dropdownList.innerHTML += itemHtml;
            });
        } else {
            // Tampilkan pesan jika tidak ada notifikasi
            dropdownList.innerHTML =
                '<a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada notifikasi baru</a>';
        }
    }
}

// =========================================================================
// INTI DARI POLLING: Jalankan fungsi fetchNotifications setiap 10 detik
// =========================================================================
setInterval(fetchNotifications, 5000);

// Jalankan juga fungsi ini satu kali saat halaman pertama kali dimuat
// untuk memastikan notifikasi langsung tampil tanpa menunggu 10 detik pertama.
document.addEventListener("DOMContentLoaded", () => {
    // Ambil nilai awal dari badge, jika ada, untuk inisialisasi
    const initialBadge = document.getElementById("notification-badge");
    if (initialBadge) {
        lastUnreadCount = parseInt(initialBadge.textContent) || 0;
    }
    fetchNotifications();
});
