<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $__env->yieldContent('title', 'Sistem Informasi Layanan Desa Kumantan'); ?></title>

    <link href="<?php echo e(asset('sbadmin/vendor/fontawesome-free/css/all.min.css')); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="<?php echo e(asset('sbadmin/css/sb-admin-2.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('sbadmin/css/custom.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('sbadmin/vendor/datatables/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet">

   
    
</head>

<body id="page-top" class="sb-nav-fixed">
    <div id="wrapper">
        <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <div class="container-fluid px-4 py-4">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>

                <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('sbadmin/vendor/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sbadmin/vendor/jquery-easing/jquery.easing.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sbadmin/js/sb-admin-2.min.js')); ?>"></script>

    <script src="<?php echo e(asset('sbadmin/vendor/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sbadmin/vendor/datatables/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sbadmin/js/demo/datatables-demo.js')); ?>"></script> 

    <?php echo $__env->yieldPushContent('scripts'); ?>


    <script type="module">
        // Event listener ini memastikan semua elemen HTML sudah siap
        document.addEventListener('DOMContentLoaded', function() {
            // Cek dulu apakah Echo sudah terdefinisi (dimuat oleh app.js dari Vite)
            if (typeof window.Echo !== 'undefined') {
                const alertsDropdown = document.getElementById('alertsDropdown');
                
                // Hanya jalankan jika kita berada di halaman yang memiliki lonceng notifikasi
                if(alertsDropdown) {
                    const counterElement = document.getElementById('notifikasi-counter');
                    const listElement = document.getElementById('notifikasi-list');
                    
                    console.log('✅ Echo siap dan mendengarkan di channel notifikasi-petugas...');

                    window.Echo.private('notifikasi-petugas')
                        .listen('.permohonan.baru', (data) => {
                            console.log('🎉 Notifikasi baru diterima:', data.notifikasi);

                            // Hapus pesan "Tidak ada notifikasi" jika ada
                            const noNotificationElement = listElement.querySelector('.no-notification');
                            if (noNotificationElement) {
                                noNotificationElement.remove();
                            }
                            
                            // Update Counter
                            let currentCount = parseInt(counterElement.innerText) || 0;
                            currentCount++;
                            counterElement.innerText = currentCount > 9 ? '9+' : currentCount;
                            counterElement.style.display = 'inline-block';

                            // Buat HTML untuk notifikasi baru
                            const notif = data.notifikasi;
                            const newNotificationHtml = `
                                <a class="dropdown-item d-flex align-items-center" href="${notif.url}">
                                    <div class="mr-3">
                                        <div class="icon-circle ${notif.bg_color}">
                                            <i class="${notif.icon}"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">${notif.waktu}</div>
                                        <span class="font-weight-normal">Permohonan <strong>${notif.jenis_surat}</strong> dari <strong>${notif.nama_pemohon}</strong> baru saja masuk.</span>
                                    </div>
                                </a>
                            `;

                            // Tambahkan notifikasi baru ke atas
                            listElement.insertAdjacentHTML('afterbegin', newNotificationHtml);
                        });
                }
            } else {
                console.error('❌ Laravel Echo tidak terdefinisi. Pastikan "npm run dev" berjalan dan <?php echo app('Illuminate\Foundation\Vite')(); ?> sudah dimuat.');
            }
        });
    </script>
</body>
</html><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/layouts/app.blade.php ENDPATH**/ ?>