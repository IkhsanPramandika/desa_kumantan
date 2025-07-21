

<?php $__env->startSection('title', 'Proses Surat Keterangan Domisili'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses & Edit Surat Keterangan Domisili</h1>
    <a href="<?php echo e(route('petugas.permohonan-sk-domisili.show', $permohonan->id)); ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Detail
    </a>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row">
    
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Data Referensi dari Pemohon</h6>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Pemohon/Lembaga</h5>
                <dl class="row">
                    <dt class="col-sm-5">Nama</dt><dd class="col-sm-7"><?php echo e($permohonan->nama_pemohon_atau_lembaga ?? '-'); ?></dd>
                    <dt class="col-sm-5">NIK</dt><dd class="col-sm-7"><?php echo e($permohonan->nik_pemohon ?? '-'); ?></dd>
                    <dt class="col-sm-5">Alamat Domisili</dt><dd class="col-sm-7"><?php echo e($permohonan->alamat_lengkap_domisili ?? '-'); ?></dd>
                    <dt class="col-sm-5">RT/RW</dt><dd class="col-sm-7"><?php echo e($permohonan->rt_domisili ?? '-'); ?> / <?php echo e($permohonan->rw_domisili ?? '-'); ?></dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Keperluan Surat</h5>
                <p class="text-muted"><em><?php echo e($permohonan->keperluan_domisili ?? 'Tidak ada keterangan.'); ?></em></p>
            </div>
        </div>
    </div>

    
    <div class="col-lg-7">
        <form action="<?php echo e(route('petugas.permohonan-sk-domisili.selesaikan', $permohonan->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i>Form Isian Surat oleh Petugas</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final. Nomor surat akan dibuat secara otomatis.</p>
                    
                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Data Pemohon/Lembaga</h5>
                    <div class="form-group">
                        <label>Nama Pemohon atau Lembaga</label>
                        <input type="text" class="form-control" name="nama_pemohon_atau_lembaga" value="<?php echo e(old('nama_pemohon_atau_lembaga', $permohonan->nama_pemohon_atau_lembaga)); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>NIK Pemohon (kosongkan jika lembaga)</label>
                        <input type="text" class="form-control" name="nik_pemohon" value="<?php echo e(old('nik_pemohon', $permohonan->nik_pemohon)); ?>">
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap Domisili</label>
                        <textarea class="form-control" name="alamat_lengkap_domisili" rows="3" required><?php echo e(old('alamat_lengkap_domisili', $permohonan->alamat_lengkap_domisili)); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>RT Domisili</label>
                                <input type="text" class="form-control" name="rt_domisili" value="<?php echo e(old('rt_domisili', $permohonan->rt_domisili)); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label>RW Domisili</label>
                                <input type="text" class="form-control" name="rw_domisili" value="<?php echo e(old('rw_domisili', $permohonan->rw_domisili)); ?>" required>
                            </div>
                        </div>
                    </div>

                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Keperluan Surat</h5>
                    <div class="form-group">
                        <label for="keperluan_domisili">Tulis ulang atau perbaiki redaksi keperluan surat</label>
                        <textarea class="form-control" name="keperluan_domisili" id="keperluan_domisili" rows="3" required><?php echo e(old('keperluan_domisili', $permohonan->keperluan_domisili)); ?></textarea>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <a href="<?php echo e(route('petugas.permohonan-sk-domisili.show', $permohonan->id)); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')">
                        <i class="fas fa-print"></i> Buat Surat Final & Selesaikan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_domisili/edit_surat.blade.php ENDPATH**/ ?>