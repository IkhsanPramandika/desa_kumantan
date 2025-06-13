

<?php $__env->startSection('title', 'Daftar Permohonan KK Baru'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan Kartu Keluarga Baru</h1>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Permohonan</h6>
    </div>
    <div class="card-body">

        
        <div class="mb-4">
            <form action="<?php echo e(route('petugas.permohonan-kk-baru.index')); ?>" method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <label for="search" class="sr-only">Cari</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Cari nama/NIK pemohon..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="form-group mr-2">
                    <label for="status" class="sr-only">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="diterima" <?php echo e(request('status') == 'diterima' ? 'selected' : ''); ?>>Diterima</option>
                        <option value="diproses" <?php echo e(request('status') == 'diproses' ? 'selected' : ''); ?>>Diproses</option>
                        <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                        <option value="ditolak" <?php echo e(request('status') == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Filter</button>
                <a href="<?php echo e(route('petugas.permohonan-kk-baru.index')); ?>" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pemohon</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Dokumen Hasil</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($item->id); ?></td>
                            <td>
                                <strong><?php echo e($item->masyarakat->nama_lengkap ?? 'N/A'); ?></strong><br>
                                <small>NIK: <?php echo e($item->masyarakat->nik ?? 'N/A'); ?></small>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')); ?></td>
                            <td>
                                <?php if($item->status == 'pending'): ?> <span class="badge badge-warning">Pending</span>
                                <?php elseif($item->status == 'diterima'): ?> <span class="badge badge-info">Diterima</span>
                                <?php elseif($item->status == 'diproses'): ?> <span class="badge badge-primary">Diproses</span>
                                <?php elseif($item->status == 'selesai'): ?> <span class="badge badge-success">Selesai</span>
                                <?php elseif($item->status == 'ditolak'): ?> <span class="badge badge-danger">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($item->status == 'selesai' && $item->file_hasil_akhir): ?>
                                    <a href="<?php echo e(route('petugas.permohonan-kk-baru.download-final', $item->id)); ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Unduh KK
                                    </a>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Belum Tersedia</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('petugas.permohonan-kk-baru.show', $item->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data yang cocok dengan filter Anda.</td>
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
$(document).ready(function() {
  // Hancurkan instance DataTable yang mungkin sudah dibuat oleh skrip bawaan tema
  if ($.fn.DataTable.isDataTable('#dataTable')) {
    $('#dataTable').DataTable().destroy();
  }

  // Inisialisasi ulang DataTable dengan konfigurasi yang kita inginkan
  $('#dataTable').DataTable({
    "searching": false, // Menonaktifkan kotak pencarian bawaan DataTables
    "paging": true,    // Menonaktifkan paginasi bawaan DataTables (kita pakai paginasi Laravel)
    "info": true,      // Menonaktifkan teks "Showing 1 to 10 of 57 entries"
    "order": [],        // Menonaktifkan sorting awal pada kolom pertama
    "columnDefs": [ {
      "targets": 'no-sort', // Menonaktifkan sorting untuk kolom dengan class 'no-sort'
      "orderable": true
    } ]
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/kk_baru/index.blade.php ENDPATH**/ ?>