

<?php $__env->startSection('title', 'Detail Permohonan SK Usaha'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Permohonan #<?php echo e($permohonan->id); ?></h1>
    <a href="<?php echo e(route('petugas.permohonan-sk-usaha.index')); ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Daftar
    </a>
</div>


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

<div class="row">
    
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan</h6></div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Pemohon</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_pemohon ?? '-'); ?></dd>
                    <dt class="col-sm-4">NIK</dt><dd class="col-sm-8"><?php echo e($permohonan->nik_pemohon ?? '-'); ?></dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Data Usaha</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama Usaha</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_usaha ?? '-'); ?></dd>
                    <dt class="col-sm-4">Alamat Usaha</dt><dd class="col-sm-8"><?php echo e($permohonan->alamat_usaha ?? '-'); ?></dd>
                    <dt class="col-sm-4">Keperluan Surat</dt><dd class="col-sm-8"><?php echo e($permohonan->keperluan_surat ?? '-'); ?></dd>
                </dl>
                <hr>
                <h6 class="font-weight-bold">Catatan dari Pemohon</h6>
                <p class="text-muted"><em><?php echo e($permohonan->catatan_pemohon ?: 'Tidak ada catatan.'); ?></em></p>
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                <?php echo $__env->make('layouts.partials.status_badge', ['status' => $permohonan->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div class="card-body">
                <?php if($permohonan->status == 'pending'): ?>
                    <p class="text-info"><i class="fas fa-info-circle fa-sm"></i> Periksa dokumen. Jika valid, klik "Verifikasi". Jika perlu perbaikan, klik "Kembalikan untuk Revisi".</p>
                    <hr>
                    <form action="<?php echo e(route('petugas.permohonan-sk-usaha.verifikasi', $permohonan->id)); ?>" method="POST" class="mb-2">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data dan lampiran sudah valid?')"><i class="fas fa-check-circle"></i> Verifikasi & Lanjutkan</button>
                    </form>
                    <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-undo"></i> Kembalikan untuk Revisi</button>
                <?php elseif($permohonan->status == 'diterima'): ?>
                    <p>Permohonan telah diverifikasi. Klik tombol di bawah untuk memproses dan mengedit data sebelum membuat surat final.</p>
                    <a href="<?php echo e(route('petugas.permohonan-sk-usaha.edit-surat', $permohonan->id)); ?>" class="btn btn-primary btn-block mb-2"><i class="fas fa-edit"></i> Proses & Edit Surat</a>
                <?php elseif($permohonan->status == 'membutuhkan_revisi'): ?>
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold">Menunggu Revisi dari Pengguna</h6>
                        <p class="mb-0 small">Permohonan telah dikembalikan untuk diperbaiki. Anda akan menerima notifikasi jika pengguna sudah mengirimkan revisi.</p>
                    </div>
                    <h6 class="font-weight-bold">Catatan Perbaikan:</h6>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                <?php elseif($permohonan->status == 'selesai'): ?>
                    <p>Proses selesai pada <strong><?php echo e($permohonan->tanggal_selesai_proses ? \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses)->isoFormat('D MMMM YYYY') : 'N/A'); ?></strong>.</p>
                    <a href="<?php echo e(route('petugas.permohonan-sk-usaha.download-final', $permohonan->id)); ?>" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh Dokumen Final</a>
                <?php elseif($permohonan->status == 'ditolak'): ?>
                    <div class="alert alert-danger"><h6 class="font-weight-bold">Permohonan Ditolak</h6></div>
                    <h6 class="font-weight-bold">Alasan Penolakan:</h6>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                        $lampiran = [
                            'file_kk' => 'Kartu Keluarga',
                            'file_ktp' => 'KTP Pemohon',
                        ];
                    ?>
                    <?php $__currentLoopData = $lampiran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div><i class="fas fa-file-alt text-gray-500 mr-2"></i> <?php echo e($label); ?></div>
                        <?php if($permohonan->$field): ?>
                            <a href="<?php echo e(asset('storage/' . $permohonan->$field)); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye fa-sm"></i> Lihat</a>
                        <?php else: ?>
                            <span class="badge badge-secondary">Tidak Ada</span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="tolakModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('petugas.permohonan-sk-usaha.tolak', $permohonan->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header"><h5 class="modal-title">Kembalikan Permohonan untuk Revisi</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan"><strong>Tulis Catatan Perbaikan (Wajib):</strong></label>
                        <textarea class="form-control" name="catatan_penolakan" rows="4" required placeholder="Contoh: Scan KTP buram, mohon unggah ulang dengan jelas."></textarea>
                        <small class="form-text text-muted">Catatan ini akan ditampilkan kepada pengguna.</small>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Ya, Kembalikan</button></div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_usaha/show.blade.php ENDPATH**/ ?>