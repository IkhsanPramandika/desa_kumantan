<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Sistem Informasi Layanan Desa Kumantan')</title>


    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    {{-- <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  
    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sbadmin/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('sbadmin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"> --}}

    <meta name="user-id" content="{{ Auth::check() ? Auth::id() : '' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>

<body id="page-top" class="sb-nav-fixed">
    <div id="wrapper">
        @include('layouts.sidebar')
      

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layouts.navbar')

                <div class="container-fluid px-4 py-4">
                    @yield('content')
                </div>

                @include('layouts.footer')
            </div>
        </div>
    </div>
    @stack('scripts')
     <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    
    @vite('resources/js/app.js')

  <script>
document.addEventListener('DOMContentLoaded', function () {

    // URL untuk memeriksa notifikasi. Pastikan route ini benar.
    const checkNotifUrl = "{{ route('petugas.notifikasi.check') }}";

    // Elemen-elemen HTML di navbar Anda
    const badgeElement = document.getElementById('notification-badge');
    const dropdownListElement = document.getElementById('notification-dropdown-list');

    // Fungsi untuk mengambil dan menampilkan notifikasi
    function fetchNotifications() {
        fetch(checkNotifUrl)
            .then(response => {
                if (!response.ok) {
                    // Jika ada error dari server, hentikan proses
                    return Promise.reject('Gagal mengambil data notifikasi.');
                }
                return response.json();
            })
            .then(data => {
                // Update badge counter di sebelah lonceng
                if (data.unread_count > 0) {
                    badgeElement.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    badgeElement.style.display = 'inline-block';
                } else {
                    badgeElement.style.display = 'none';
                }

                // Kosongkan daftar notifikasi yang lama
                dropdownListElement.innerHTML = '';

                // Isi kembali daftar dropdown dengan data baru
                if (data.notifications.length === 0) {
                    dropdownListElement.innerHTML = '<a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada notifikasi baru</a>';
                } else {
                    data.notifications.forEach(notif => {
                        // Tampilan notifikasi baru yang lebih baik
                        const itemHTML = `
                        <a href="${notif.url}" class="dropdown-item d-flex align-items-center ${notif.is_unread ? 'bg-light' : ''}">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="${notif.icon} text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">${notif.waktu}</div>
                                <span class="font-weight-bold d-block">${notif.judul}</span>
                                <span>${notif.sub_judul}</span>
                            </div>
                        </a>`;
                        dropdownListElement.innerHTML += itemHTML;
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                dropdownListElement.innerHTML = '<a class="dropdown-item text-center small text-danger" href="#">Gagal memuat notifikasi</a>';
            });
    }

    // Panggil fungsi saat halaman pertama kali dimuat
    fetchNotifications();

    // Atur interval untuk memanggil fungsi setiap 8 detik (8000 milidetik)
    setInterval(fetchNotifications, 5000);
});
</script>



</body>
</html>