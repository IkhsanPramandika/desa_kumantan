 

<?php $__env->startSection('title', 'Manajemen Akun Masyarakat'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .action-buttons form,
    .action-buttons a,
    .action-buttons button {
        margin-bottom: 0.25rem; /* Jarak antar tombol */
        margin-right: 0.25rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manajemen Akun Masyarakat</h1>
    <p class="mb-4">Kelola dan verifikasi akun pengguna masyarakat.</p>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Akun Masyarakat</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('petugas.masyarakat.index')); ?>" class="mb-4">
                <div class="form-row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIK, Nama, No. HP, Email..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status_akun" class="form-control form-control-sm">
                            <option value="">Semua Status</option>
                            <option value="pending_verification" <?php echo e(request('status_akun') == 'pending_verification' ? 'selected' : ''); ?>>Pending Verifikasi</option>
                            <option value="active" <?php echo e(request('status_akun') == 'active' ? 'selected' : ''); ?>>Aktif</option>
                            <option value="inactive" <?php echo e(request('status_akun') == 'inactive' ? 'selected' : ''); ?>>Tidak Aktif</option>
                            <option value="rejected" <?php echo e(request('status_akun') == 'rejected' ? 'selected' : ''); ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="<?php echo e(route('petugas.masyarakat.index')); ?>" class="btn btn-secondary btn-sm btn-block">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableMasyarakat" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Lengkap</th>
                            <th>NIK</th>
                            <th>No. HP</th>
                            <th>Email</th>
                            <th>Status Akun</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $masyarakat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration + ($masyarakat->currentPage() - 1) * $masyarakat->perPage()); ?></td>
                            <td><?php echo e($item->nama_lengkap); ?></td>
                            <td><?php echo e($item->nik); ?></td>
                            <td><?php echo e($item->nomor_hp ?? '-'); ?></td>
                            <td><?php echo e($item->email ?? '-'); ?></td>
                            <td>
                                <?php if($item->status_akun == 'pending_verification'): ?>
                                    <span class="badge badge-warning">Pending Verifikasi</span>
                                <?php elseif($item->status_akun == 'active'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php elseif($item->status_akun == 'inactive'): ?>
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                <?php elseif($item->status_akun == 'rejected'): ?>
                                    <span class="badge badge-danger">Ditolak</span>
                                    <?php if($item->catatan_verifikasi): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger p-0 ml-1" data-toggle="tooltip" data-placement="top" title="Alasan: <?php echo e($item->catatan_verifikasi); ?>">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-light"><?php echo e(ucfirst(str_replace('_', ' ', $item->status_akun))); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($item->created_at->format('d/m/Y H:i')); ?></td>
                            <td class="action-buttons">
                                <a href="<?php echo e(route('petugas.masyarakat.show', $item->id)); ?>" class="btn btn-secondary btn-sm" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if($item->status_akun == 'pending_verification'): ?>
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#verifikasiModal-<?php echo e($item->id); ?>" title="Verifikasi/Aktifkan Akun">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#tolakModal-<?php echo e($item->id); ?>" title="Tolak Akun">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                <?php elseif($item->status_akun == 'active'): ?>
                                     <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#nonaktifkanModal-<?php echo e($item->id); ?>" title="Nonaktifkan Akun">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                <?php elseif($item->status_akun == 'inactive' || $item->status_akun == 'rejected'): ?>
                                     <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#verifikasiModal-<?php echo e($item->id); ?>" title="Aktifkan Kembali Akun">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                <?php endif; ?>
                                <a href="<?php echo e(route('petugas.masyarakat.showResetPasswordFormByPetugas', $item->id)); ?>" class="btn btn-info btn-sm" title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </a>
                            </td>
                        </tr>

                        
                        <div class="modal fade" id="verifikasiModal-<?php echo e($item->id); ?>" tabindex="-1" role="dialog" aria-labelledby="verifikasiModalLabel-<?php echo e($item->id); ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="<?php echo e(route('petugas.masyarakat.updateStatus', $item->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="verifikasiModalLabel-<?php echo e($item->id); ?>">Aktifkan Akun: <?php echo e($item->nama_lengkap); ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Anda yakin ingin mengaktifkan akun untuk <strong><?php echo e($item->nama_lengkap); ?></strong> (NIK: <?php echo e($item->nik); ?>)?</p>
                                            <input type="hidden" name="status_akun" value="active">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Ya, Aktifkan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        
                        <div class="modal fade" id="tolakModal-<?php echo e($item->id); ?>" tabindex="-1" role="dialog" aria-labelledby="tolakModalLabel-<?php echo e($item->id); ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="<?php echo e(route('petugas.masyarakat.updateStatus', $item->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="tolakModalLabel-<?php echo e($item->id); ?>">Tolak Akun: <?php echo e($item->nama_lengkap); ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Anda yakin ingin menolak akun untuk <strong><?php echo e($item->nama_lengkap); ?></strong> (NIK: <?php echo e($item->nik); ?>)?</p>
                                            <input type="hidden" name="status_akun" value="rejected">
                                            <div class="form-group">
                                                <label for="catatan_verifikasi_tolak_<?php echo e($item->id); ?>">Alasan Penolakan (Wajib):</label>
                                                <textarea name="catatan_verifikasi" id="catatan_verifikasi_tolak_<?php echo e($item->id); ?>" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Ya, Tolak Akun</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                         
                        <div class="modal fade" id="nonaktifkanModal-<?php echo e($item->id); ?>" tabindex="-1" role="dialog" aria-labelledby="nonaktifkanModalLabel-<?php echo e($item->id); ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="<?php echo e(route('petugas.masyarakat.updateStatus', $item->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="nonaktifkanModalLabel-<?php echo e($item->id); ?>">Nonaktifkan Akun: <?php echo e($item->nama_lengkap); ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Anda yakin ingin menonaktifkan akun untuk <strong><?php echo e($item->nama_lengkap); ?></strong> (NIK: <?php echo e($item->nik); ?>)?</p>
                                            <input type="hidden" name="status_akun" value="inactive">
                                            <div class="form-group">
                                                <label for="catatan_verifikasi_nonaktif_<?php echo e($item->id); ?>">Alasan Penonaktifan (Opsional):</label>
                                                <textarea name="catatan_verifikasi" id="catatan_verifikasi_nonaktif_<?php echo e($item->id); ?>" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">Ya, Nonaktifkan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data akun masyarakat.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <?php echo e($masyarakat->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Hapus inisialisasi DataTables jika Anda menggunakan paginasi server-side Laravel
        // Jika ingin client-side (untuk data sedikit):
        // $('#dataTableMasyarakat').DataTable({
        //     "language": {
        //         "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
        //     },
        //     "columnDefs": [
        //         { "orderable": false, "targets": [7] } 
        //     ],
        //     "order": [[ 0, "asc" ]] 
        // });
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/masyarakat/index.blade.php ENDPATH**/ ?>