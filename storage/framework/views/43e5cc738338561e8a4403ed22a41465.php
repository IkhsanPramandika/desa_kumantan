


<?php $__env->startSection('title', 'Hasil Pencarian'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Hasil Pencarian untuk: "<?php echo e($query); ?>"</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ditemukan <?php echo e($results->count()); ?> hasil</h6>
        </div>
        <div class="card-body">
            <?php if($results->isEmpty()): ?>
                <div class="text-center my-5">
                    <i class="fas fa-search fa-3x text-gray-400"></i>
                    <p class="mt-3 text-gray-600">Tidak ada permohonan yang cocok dengan kata kunci Anda.</p>
                    <p class="small">Coba gunakan kata kunci lain.</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($item->getRouteTujuan()); ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1 font-weight-bold text-primary"><?php echo e($item->getJudulNotifikasi()); ?></h5>
                                <small><?php echo e($item->created_at->diffForHumans()); ?></small>
                            </div>
                            <p class="mb-1">
                                Diajukan oleh: <strong><?php echo e($item->masyarakat->nama_lengkap ?? 'N/A'); ?></strong>
                                (NIK: <?php echo e($item->masyarakat->nik ?? 'N/A'); ?>)
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">ID Permohonan: #<?php echo e($item->id); ?></small>
                                <?php echo $__env->make('layouts.partials.status_badge', ['status' => $item->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/search/results.blade.php ENDPATH**/ ?>