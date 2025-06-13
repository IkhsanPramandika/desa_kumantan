

<?php $__env->startSection('title', 'Semua Notifikasi Permohonan'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Semua Riwayat Notifikasi</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian Riwayat</h6>
    </div>
    <div class="card-body">
     
        
        <div class="mb-4">
            
                <div class="form-row align-items-end">
                    <div class="col-md-4 mb-2">
                        <label for="search" class="sr-only">Cari</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Cari nama pemohon..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="status" class="sr-only">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="diterima" <?php echo e(request('status') == 'diterima' ? 'selected' : ''); ?>>Diterima</option>
                            <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                            <option value="ditolak" <?php echo e(request('status') == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="jenis_surat" class="sr-only">Jenis Surat</label>
                        <select class="form-control" id="jenis_surat" name="jenis_surat">
                            <option value="">-- Semua Jenis Surat --</option>
                            <?php $__currentLoopData = $jenisSuratOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($jenis); ?>" <?php echo e(request('jenis_surat') == $jenis ? 'selected' : ''); ?>><?php echo e($jenis); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </div>
            </form>
        </div>

        
        <div class="list-group list-group-flush">
            <?php $__empty_1 = true; $__currentLoopData = $semuaNotifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($notif['url']); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle <?php echo e($notif['bg_color']); ?> mr-3">
                            <i class="<?php echo e($notif['icon']); ?>"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold text-dark">
                                Permohonan <?php echo e($notif['jenis_surat']); ?>

                                <?php if($notif['status'] == 'selesai'): ?>
                                    <span class="badge badge-success ml-2">Selesai</span>
                                <?php elseif($notif['status'] == 'ditolak'): ?>
                                    <span class="badge badge-danger ml-2">Ditolak</span>
                                <?php elseif($notif['status'] == 'pending'): ?>
                                    <span class="badge badge-warning ml-2">Pending</span>
                                <?php else: ?>
                                     <span class="badge badge-info ml-2"><?php echo e(ucfirst($notif['status'])); ?></span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                Oleh: <strong><?php echo e($notif['nama_pemohon']); ?></strong> &bull; Dibuat pada: <?php echo e(\Carbon\Carbon::parse($notif['waktu'])->format('d M Y, H:i')); ?>

                            </small>
                        </div>
                    </div>
                    <small class="text-gray-500"><?php echo e(\Carbon\Carbon::parse($notif['waktu'])->diffForHumans()); ?></small>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-center">
                    Tidak ada riwayat yang cocok dengan filter Anda. <a href="<?php echo e(route('notifikasi.index')); ?>">Reset Filter</a>.
                </div>
            <?php endif; ?>
        </div>

        
        <div class="mt-4 d-flex justify-content-center">
            
            <?php echo e($semuaNotifikasi ->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/notifikasi/index.blade.php ENDPATH**/ ?>