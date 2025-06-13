<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Variabel reaktif untuk menyimpan data notifikasi
const notifications = ref([]);
const notificationCount = ref(0);

// Fungsi untuk mengambil notifikasi dari API Laravel Anda
const fetchNotifications = async () => {
    try {
        // Gunakan axios yang sudah kita siapkan
        const response = await axios.get('/api/petugas/notifikasi-baru'); // Sesuaikan dengan route API Anda
        const newNotifications = response.data;

        if (newNotifications && newNotifications.length > 0) {
            console.log(`🎉 Ditemukan ${newNotifications.length} notifikasi baru!`);
            // Tambahkan notifikasi baru ke awal daftar
            notifications.value = [...newNotifications, ...notifications.value];
            // Update jumlah notifikasi
            notificationCount.value += newNotifications.length;
        }
    } catch (error) {
        console.error('Gagal mengambil notifikasi:', error);
    }
};

// onMounted adalah hook yang berjalan saat komponen pertama kali dimuat
onMounted(() => {
    // Jalankan pertama kali setelah 1 detik
    setTimeout(fetchNotifications, 1000); 
    // Kemudian jalankan setiap 5 detik (atau sesuai kebutuhan)
    setInterval(fetchNotifications, 5000); 
});
</script>

<template>
  <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
      <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-bell fa-fw"></i>
          <span class="badge badge-danger badge-counter" v-if="notificationCount > 0">
            {{ notificationCount > 9 ? '9+' : notificationCount }}
          </span>
        </a>
        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
          <h6 class="dropdown-header">
            Pusat Notifikasi
          </h6>
          
          <a v-for="notif in notifications" :key="notif.url" class="dropdown-item d-flex align-items-center" :href="notif.url">
            <div class="mr-3">
              <div :class="['icon-circle', notif.bg_color]">
                <i :class="[notif.icon, 'text-white']"></i>
              </div>
            </div>
            <div>
              <div class="small text-gray-500">{{ notif.waktu }}</div>
              <span class="font-weight-normal">
                Permohonan <strong>{{ notif.jenis_surat }}</strong> dari <strong>{{ notif.nama_pemohon }}</strong> baru saja masuk.
              </span>
            </div>
          </a>
          
          <a v-if="notifications.length === 0" class="dropdown-item text-center small text-gray-500" href="#">
            Tidak ada notifikasi baru
          </a>

          <a class="dropdown-item text-center small text-gray-500" href="#">Tampilkan Semua Notifikasi</a>
        </div>
      </li>

      </ul>

  </nav>
</template>