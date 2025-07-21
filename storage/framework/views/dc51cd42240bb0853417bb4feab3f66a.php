

<?php $__env->startSection('title', 'Daftar Permohonan SK Kelahiran'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan Surat Keterangan Kelahiran</h1>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Permohonan</h6>
    </div>
    <div class="card-body">

        <div class="card card-body mb-4 p-3 bg-light">
            <form action="<?php echo e(route('petugas.permohonan-sk-kelahiran.index')); ?>" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="font-weight-bold">Cari Nama Anak/Orang Tua</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan kata kunci..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="font-weight-bold">Status Permohonan</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" <?php if(request('status') == 'pending'): echo 'selected'; endif; ?>>Pending</option>
                            <option value="membutuhkan_revisi" <?php if(request('status') == 'membutuhkan_revisi'): echo 'selected'; endif; ?>>Perlu Revisi</option>
                            <option value="diterima" <?php if(request('status') == 'diterima'): echo 'selected'; endif; ?>>Diterima</option>
                            <option value="selesai" <?php if(request('status') == 'selesai'): echo 'selected'; endif; ?>>Selesai</option>
                            <option value="ditolak" <?php if(request('status') == 'ditolak'): echo 'selected'; endif; ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="font-weight-bold">Tampilkan</label>
                        <select class="form-control" id="per_page" name="per_page">
                            <option value="10" <?php if(request('per_page', 10) == 10): echo 'selected'; endif; ?>>10</option>
                            <option value="25" <?php if(request('per_page') == 25): echo 'selected'; endif; ?>>25</option>
                            <option value="50" <?php if(request('per_page') == 50): echo 'selected'; endif; ?>>50</option>
                            <option value="100" <?php if(request('per_page') == 100): echo 'selected'; endif; ?>>100</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search fa-sm"></i> Terapkan Filter</button>
                        <a href="<?php echo e(route('petugas.permohonan-sk-kelahiran.index')); ?>" class="btn btn-secondary ml-2" title="Reset Filter"><i class="fas fa-sync"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th>Nama Anak</th>
                        <th>Orang Tua</th>
                        <th style="width: 15%;">Tanggal Pengajuan</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($item->id); ?></td>
                            <td>
                                <div class="font-weight-bold"><?php echo e($item->nama_anak ?? 'N/A'); ?></div>
                                <div class="small text-gray-600"><?php echo e($item->jenis_kelamin_anak ?? ''); ?></div>
                            </td>
                            <td>
                                <div class="font-weight-bold"><?php echo e($item->nama_ayah ?? 'N/A'); ?></div>
                                <div class="small text-gray-600"><?php echo e($item->nama_ibu ?? 'N/A'); ?></div>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($item->created_at)->isoFormat('D MMMM YYYY, HH:mm')); ?></td>
                            <td>
                                <?php echo $__env->make('layouts.partials.status_badge', ['status' => $item->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('petugas.permohonan-sk-kelahiran.show', $item->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye fa-sm"></i> Proses
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="my-4">
                                    <i class="fas fa-box-open fa-3x text-gray-400"></i>
                                    <p class="mt-3 text-gray-600">Data tidak ditemukan.</p>
                                    <p class="small">Coba ubah atau reset filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($data->links()); ?>

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_kelahiran/index.blade.php ENDPATH**/ ?>