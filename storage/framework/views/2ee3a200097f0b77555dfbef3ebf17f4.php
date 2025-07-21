

<?php $__env->startSection('title', 'Detail Permohonan Lainnya'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Permohonan #<?php echo e($permohonan->id); ?></h1>
    <a href="<?php echo e(route('petugas.permohonan-lainnya.index')); ?>" class="btn btn-secondary btn-sm">
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
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data Permohonan</h6></div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nama Pemohon</dt><dd class="col-sm-8 font-weight-bold"><?php echo e($permohonan->masyarakat->nama_lengkap ?? '-'); ?></dd>
                    <dt class="col-sm-4">NIK Pemohon</dt><dd class="col-sm-8"><?php echo e($permohonan->masyarakat->nik ?? '-'); ?></dd>
                </dl>
                <hr>
                <dl>
                    <dt>Judul Permohonan</dt>
                    <dd><?php echo e($permohonan->judul_permohonan ?? '-'); ?></dd>
                    <dt>Keperluan</dt>
                    <dd><?php echo e($permohonan->keperluan ?? '-'); ?></dd>
                    <dt>Rincian Lengkap dari Pemohon</dt>
                    <dd>
                        <div class="p-3 bg-light border rounded mt-1">
                            <?php echo nl2br(e($permohonan->rincian_pemohon ?? 'Tidak ada rincian.')); ?>

                        </div>
                    </dd>
                </dl>
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
                <?php if($permohonan->status == 'pending' || $permohonan->status == 'membutuhkan_revisi'): ?>
                    <p class="text-info"><i class="fas fa-info-circle fa-sm"></i> Periksa rincian dan lampiran. Jika valid, klik "Buat Surat". Jika perlu perbaikan, klik "Kembalikan untuk Revisi".</p>
                    <hr>
                    <a href="<?php echo e(route('petugas.permohonan-lainnya.create-surat', $permohonan->id)); ?>" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-pen-alt"></i> Buat Surat
                    </a>
                    <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-undo"></i> Kembalikan untuk Revisi</button>
                
                <?php elseif($permohonan->status == 'selesai'): ?>
                    <p>Surat telah dibuat pada <strong><?php echo e($permohonan->tanggal_selesai_proses ? \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses)->isoFormat('D MMMM YYYY') : ''); ?></strong>.</p>
                    <a href="<?php echo e(route('petugas.permohonan-lainnya.download-final', $permohonan->id)); ?>" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh Surat</a>

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
                <?php
                    // [PERBAIKAN] Decode JSON lampiran
                    $lampiranFiles = !empty($permohonan->lampiran) ? json_decode($permohonan->lampiran, true) : [];
                ?>

                <?php if(!empty($lampiranFiles) && is_array($lampiranFiles)): ?>
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $lampiranFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                                Lampiran <?php echo e($index + 1); ?>

                            </div>
                            <a href="<?php echo e(asset('storage/' . $file)); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye fa-sm"></i> Lihat</a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-center my-3">Tidak ada dokumen yang dilampirkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="tolakModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('petugas.permohonan-lainnya.tolak', $permohonan->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header"><h5 class="modal-title">Kembalikan Permohonan untuk Revisi</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan"><strong>Tulis Catatan Perbaikan (Wajib):</strong></label>
                        <textarea class="form-control" name="catatan_penolakan" rows="4" required placeholder="Contoh: Mohon lampirkan juga scan KTP."></textarea>
                        <small class="form-text text-muted">Catatan ini akan ditampilkan kepada pengguna.</small>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Ya, Kembalikan</button></div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/permohonan_lainnya/show.blade.php ENDPATH**/ ?>