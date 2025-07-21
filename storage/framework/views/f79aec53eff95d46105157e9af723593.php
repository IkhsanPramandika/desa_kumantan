

<?php $__env->startSection('title', 'Edit Pengumuman: ' . $pengumuman->judul); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Pengumuman: <?php echo e(Str::limit($pengumuman->judul, 40)); ?></h1>

    <div class="card shadow mb-4">
        <div class="card-body p-4">
            <form action="<?php echo e(route('petugas.pengumuman.update', $pengumuman->id)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <?php echo $__env->make('layouts.partials.form-fields', ['pengumuman' => $pengumuman], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <?php echo $__env->make('layouts.partials.form-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengumuman/edit.blade.php ENDPATH**/ ?>