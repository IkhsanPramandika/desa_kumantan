

<?php $__env->startSection('title', 'Riwayat Notifikasi'); ?>

<?php $__env->startPush('styles'); ?>

<style>
    .notification-card {
        transition: transform .2s ease-out, box-shadow .2s ease-out;
    }
    .notification-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,.12)!important;
    }
    .notification-card .stretched-link::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
        content: "";
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-2 text-gray-800">Riwayat Notifikasi</h1>
<p class="mb-4">Lihat semua riwayat notifikasi permohonan yang masuk ke sistem.</p>


<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Riwayat</h6>
        <a href="<?php echo e(route('petugas.notifikasi.index')); ?>" class="btn btn-sm btn-secondary"><i class="fas fa-sync-alt fa-sm"></i> Reset Filter</a>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('petugas.notifikasi.index')); ?>" method="GET">
            <div class="form-row align-items-end">
                <div class="col-md-4 mb-3">
                    <label for="search">Cari Nama Pemohon</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Contoh: Budi" value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">Status Permohonan</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                        <option value="ditolak" <?php echo e(request('status') == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="jenis_surat">Jenis Surat</label>
                    <select class="form-control" id="jenis_surat" name="jenis_surat">
                        <option value="">-- Semua Jenis Surat --</option>
                        <?php if(isset($jenisSuratOptions)): ?>
                            <?php $__currentLoopData = $jenisSuratOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($jenis); ?>" <?php echo e(request('jenis_surat') == $jenis ? 'selected' : ''); ?>><?php echo e($jenis); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search fa-sm"></i> Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div>
    <?php $__empty_1 = true; $__currentLoopData = $semuaNotifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $status = $notif->data['status'] ?? 'pending';
            // PERBAIKAN 1: Ikon diubah menjadi statis (ikon surat)
            $iconClass = 'fas fa-envelope-open-text'; 
            $bgColorClass = 'bg-primary';
            $badgeColorClass = 'badge-info';

            switch ($status) {
                case 'selesai':
                    $bgColorClass = 'bg-success';
                    $badgeColorClass = 'badge-success';
                    break;
                case 'ditolak':
                    $bgColorClass = 'bg-danger';
                    $badgeColorClass = 'badge-danger';
                    break;
                case 'pending':
                    $bgColorClass = 'bg-warning';
                    $badgeColorClass = 'badge-warning';
                    break;
            }
        ?>

        
        <div class="card shadow-sm mb-3 notification-card">
            <div class="card-body p-3">
                <div class="row no-gutters align-items-center">
                    
                    <div class="col-auto">
                        <div class="d-flex align-items-center justify-content-center rounded-circle <?php echo e($bgColorClass); ?>" style="width: 50px; height: 50px;">
                            <i class="<?php echo e($iconClass); ?> text-white fa-lg"></i>
                        </div>
                    </div>
                    
                    
                    <div class="col pl-3">
                        <h6 class="font-weight-bold text-dark mb-0">
                            Permohonan <?php echo e($notif->data['jenis_surat'] ?? 'Tidak Diketahui'); ?>

                            
                            <a href="<?php echo e($notif->data['url'] ?? '#'); ?>" class="stretched-link"></a>
                        </h6>
                        <small class="text-muted">
                            Oleh <strong><?php echo e($notif->data['nama_pemohon'] ?? 'Warga'); ?></strong>
                        </small>
                    </div>

                    
                    <div class="col-auto text-right">
                        <?php if($notif->read_at == null): ?>
                            <span class="badge badge-danger font-weight-bold mb-2">BARU</span>
                        <?php else: ?>
                            <span class="badge <?php echo e($badgeColorClass); ?> mb-2"><?php echo e(ucfirst($status)); ?></span>
                        <?php endif; ?>
                        <div class="small text-gray-600">
                            
                            <?php echo e(\Carbon\Carbon::parse($notif->created_at)->translatedFormat('l')); ?>, <?php echo e(\Carbon\Carbon::parse($notif->created_at)->diffForHumans()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-folder-open fa-4x text-gray-300"></i>
            </div>
            <h4 class="font-weight-bold">Tidak Ada Riwayat Notifikasi</h4>
            <p class="text-muted">Belum ada notifikasi yang cocok dengan filter Anda.</p>
        </div>
    <?php endif; ?>
</div>


<?php if($semuaNotifikasi->hasPages()): ?>
<div class="mt-4 d-flex justify-content-center">
    <?php echo e($semuaNotifikasi->appends(request()->query())->links()); ?>

</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/notifikasi/index.blade.php ENDPATH**/ ?>