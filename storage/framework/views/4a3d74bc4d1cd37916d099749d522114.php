

<?php $__env->startSection('title', 'Manajemen Pengumuman'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Pengumuman & Berita</h1>
    <a href="<?php echo e(route('petugas.pengumuman.create')); ?>" class="btn btn-primary btn-icon-split">
        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
        <span class="text">Buat Pengumuman Baru</span>
    </a>
</div>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengumuman</h6>
    </div>
    <div class="card-body">
        <div class="card card-body mb-4 p-3 bg-light">
            <form action="<?php echo e(route('petugas.pengumuman.index')); ?>" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="font-weight-bold">Cari Judul</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan judul pengumuman..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="font-weight-bold">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua --</option>
                            <option value="dipublikasikan" <?php if(request('status') == 'dipublikasikan'): echo 'selected'; endif; ?>>Dipublikasikan</option>
                            <option value="draft" <?php if(request('status') == 'draft'): echo 'selected'; endif; ?>>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="font-weight-bold">Tampilkan</label>
                        <select class="form-control" id="per_page" name="per_page">
                            <option value="10" <?php if(request('per_page', 10) == 10): echo 'selected'; endif; ?>>10</option>
                            <option value="25" <?php if(request('per_page') == 25): echo 'selected'; endif; ?>>25</option>
                            <option value="50" <?php if(request('per_page') == 50): echo 'selected'; endif; ?>>50</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search fa-sm"></i> Cari</button>
                        <a href="<?php echo e(route('petugas.pengumuman.index')); ?>" class="btn btn-secondary ml-2" title="Reset Filter"><i class="fas fa-sync"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th style="width: 15%;">Tgl Publikasi</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 18%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($item->id); ?></td>
                            <td>
                                <a href="<?php echo e(route('petugas.pengumuman.show', $item->id)); ?>" class="font-weight-bold"><?php echo e(Str::limit($item->judul, 60)); ?></a>
                            </td>
                            <td><?php echo e($item->user->name ?? 'N/A'); ?></td>
                            <td><?php echo e($item->tanggal_publikasi->isoFormat('D MMM YYYY')); ?></td>
                            <td>
                                <?php if($item->status_publikasi == 'dipublikasikan'): ?>
                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Dipublikasikan</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fas fa-clock mr-1"></i> Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('petugas.pengumuman.show', $item->id)); ?>" class="btn btn-sm btn-info" title="Lihat"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('petugas.pengumuman.edit', $item->id)); ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('petugas.pengumuman.destroy', $item->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="my-4">
                                    <i class="fas fa-bullhorn fa-3x text-gray-400"></i>
                                    <p class="mt-3 text-gray-600">Belum ada pengumuman yang dibuat.</p>
                                    <a href="<?php echo e(route('petugas.pengumuman.create')); ?>" class="btn btn-primary btn-sm">Buat Pengumuman Pertama Anda</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($pengumuman->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const perPageSelect = document.getElementById('per_page');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                this.closest('form').submit();
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengumuman/index.blade.php ENDPATH**/ ?>