

<?php $__env->startSection('title', 'Detail Pengumuman: ' . $pengumuman->judul); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .pengumuman-header-meta {
        font-size: 0.875rem; color: #858796; margin-bottom: 1rem;
    }
    .pengumuman-header-meta .meta-item {
        margin-right: 1.5rem;
    }
    .pengumuman-header-meta .meta-item i {
        margin-right: 0.35rem;
    }
    .pengumuman-gambar-utama {
        width: 100%; max-height: 450px; object-fit: cover;
        border-radius: 0.35rem; margin-bottom: 1.5rem;
    }
    .pengumuman-isi-lengkap {
        font-size: 1.05rem; line-height: 1.8; color: #5a5c69;
    }
    .pengumuman-isi-lengkap p, 
    .pengumuman-isi-lengkap ul, 
    .pengumuman-isi-lengkap ol,
    .pengumuman-isi-lengkap blockquote,
    .pengumuman-isi-lengkap table {
        margin-bottom: 1.25rem;
    }
    .pengumuman-isi-lengkap h1, h2, h3, h4, h5, h6 {
        margin-top: 1.75rem; margin-bottom: 1rem; font-weight: 600; color: #3a3b45;
    }
    .lampiran-section {
        margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e3e6f0;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <a href="<?php echo e(route('petugas.pengumuman.index')); ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar
        </a>
        <div>
            <a href="<?php echo e(route('petugas.pengumuman.edit', $pengumuman->id)); ?>" class="btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Pengumuman
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-md-5">
            <h1 class="h2 mb-3 text-gray-900 font-weight-bold"><?php echo e($pengumuman->judul); ?></h1>

            <div class="pengumuman-header-meta">
                <span class="meta-item" title="Tanggal Publikasi"><i class="fas fa-calendar-alt"></i><?php echo e($pengumuman->tanggal_publikasi->translatedFormat('d F Y')); ?></span>
                <span class="meta-item" title="Penulis"><i class="fas fa-user"></i>Oleh: <?php echo e($pengumuman->user->name ?? 'Admin'); ?></span>
                <span class="meta-item" title="Status">
                     <?php if($pengumuman->status_publikasi == 'dipublikasikan'): ?>
                        <i class="fas fa-check-circle text-success"></i> <span class="text-success">Dipublikasikan</span>
                    <?php else: ?>
                        <i class="fas fa-clock text-warning"></i> <span class="text-warning">Draft</span>
                    <?php endif; ?>
                </span>
            </div>

            <?php if($pengumuman->gambar_pengumuman): ?>
                <img src="<?php echo e(Storage::url($pengumuman->gambar_pengumuman)); ?>" alt="Gambar <?php echo e($pengumuman->judul); ?>" class="pengumuman-gambar-utama img-fluid">
            <?php endif; ?>

            <div class="pengumuman-isi-lengkap mt-4">
                <?php echo $pengumuman->isi; ?> 
            </div>

            <?php if($pengumuman->file_pengumuman): ?>
                <div class="lampiran-section">
                    <h5><i class="fas fa-paperclip"></i> File Lampiran:</h5>
                    <a href="<?php echo e(Storage::url($pengumuman->file_pengumuman)); ?>" target="_blank" class="btn btn-info">
                        <i class="fas fa-download fa-sm"></i> Unduh Lampiran (<?php echo e(basename($pengumuman->file_pengumuman)); ?>)
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer text-muted">
            <small>
                Dibuat: <?php echo e($pengumuman->created_at->translatedFormat('d M Y, H:i')); ?> | 
                Terakhir diperbarui: <?php echo e($pengumuman->updated_at->translatedFormat('d M Y, H:i')); ?>

            </small>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengumuman/show.blade.php ENDPATH**/ ?>