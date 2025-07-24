




<?php $__env->startSection('title', 'Detail Akun: ' . $masyarakat->nama_lengkap); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Akun: <?php echo e($masyarakat->nama_lengkap); ?></h1>
    <a href="<?php echo e(route('petugas.masyarakat.index')); ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar
    </a>
</div>


<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<?php endif; ?>

<div class="row">
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img class="img-profile rounded-circle mb-3" src="<?php echo e(asset('sbadmin/img/undraw_profile.svg')); ?>" style="max-width: 150px;">
                <h5 class="font-weight-bold"><?php echo e($masyarakat->nama_lengkap); ?></h5>
                <p class="text-muted mb-1">NIK: <?php echo e($masyarakat->nik); ?></p>
                <p class="text-muted"><?php echo e($masyarakat->email ?? $masyarakat->nomor_hp); ?></p>
                <?php echo $__env->make('layouts.partials.akun_status_badge', ['status' => $masyarakat->status_akun], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Aksi Petugas</h6></div>
            <div class="card-body">
                <?php if($masyarakat->status_akun == 'pending_verification'): ?>
                    <p class="text-info small"><i class="fas fa-info-circle"></i> Periksa data dan KTP sebelum memverifikasi akun.</p>
                    <button class="btn btn-success btn-block" data-toggle="modal" data-target="#verifikasiModal"><i class="fas fa-user-check"></i> Verifikasi & Aktifkan Akun</button>
                    <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-user-times"></i> Tolak Pendaftaran</button>
                <?php elseif($masyarakat->status_akun == 'active'): ?>
                     <button class="btn btn-warning btn-block" data-toggle="modal" data-target="#nonaktifkanModal"><i class="fas fa-user-slash"></i> Nonaktifkan Akun</button>
                <?php elseif(in_array($masyarakat->status_akun, ['inactive', 'rejected'])): ?>
                     <button class="btn btn-success btn-block" data-toggle="modal" data-target="#verifikasiModal"><i class="fas fa-user-check"></i> Aktifkan Kembali Akun</button>
                <?php endif; ?>
                <hr>
                <a href="<?php echo e(route('petugas.masyarakat.showResetPasswordFormByPetugas', $masyarakat->id)); ?>" class="btn btn-outline-primary btn-block"><i class="fas fa-key"></i> Reset Password</a>
            </div>
        </div>
    </div>

    
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="data-diri-tab" data-toggle="tab" href="#data-diri" role="tab">Data Diri</a></li>
                    <li class="nav-item"><a class="nav-link" id="ktp-tab" data-toggle="tab" href="#ktp" role="tab">Dokumen KTP</a></li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="data-diri" role="tabpanel">
                        <div class="p-3">
                            <dl class="row">
                                <dt class="col-sm-4">Tempat Lahir</dt><dd class="col-sm-8"><?php echo e($masyarakat->tempat_lahir ?? '-'); ?></dd>
                                <dt class="col-sm-4">Tanggal Lahir</dt><dd class="col-sm-8"><?php echo e($masyarakat->tanggal_lahir ? \Carbon\Carbon::parse($masyarakat->tanggal_lahir)->isoFormat('D MMMM YYYY') : '-'); ?></dd>
                                <dt class="col-sm-4">Jenis Kelamin</dt><dd class="col-sm-8"><?php echo e($masyarakat->jenis_kelamin ?? '-'); ?></dd>
                                <dt class="col-sm-4">Agama</dt><dd class="col-sm-8"><?php echo e($masyarakat->agama ?? '-'); ?></dd>
                                <dt class="col-sm-4">Pekerjaan</dt><dd class="col-sm-8"><?php echo e($masyarakat->pekerjaan ?? '-'); ?></dd>
                                <dt class="col-sm-4">Status Perkawinan</dt><dd class="col-sm-8"><?php echo e($masyarakat->status_perkawinan ?? '-'); ?></dd>
                                <hr class="col-12">
                                <dt class="col-sm-4">Alamat Lengkap</dt><dd class="col-sm-8"><?php echo e($masyarakat->alamat_lengkap ?? '-'); ?></dd>
                                <dt class="col-sm-4">RT/RW</dt><dd class="col-sm-8"><?php echo e($masyarakat->rt ?? '-'); ?>/<?php echo e($masyarakat->rw ?? '-'); ?></dd>
                                <dt class="col-sm-4">Dusun/Lingkungan</dt><dd class="col-sm-8"><?php echo e($masyarakat->dusun_atau_lingkungan ?? '-'); ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ktp" role="tabpanel">
                        <div class="p-3">
                            <?php if($masyarakat->foto_ktp): ?>
                                <a href="<?php echo e(Storage::url($masyarakat->foto_ktp)); ?>" target="_blank">
                                    <img src="<?php echo e(Storage::url($masyarakat->foto_ktp)); ?>" alt="Foto KTP <?php echo e($masyarakat->nama_lengkap); ?>" class="img-fluid rounded">
                                </a>
                            <?php else: ?>
                                <div class="text-center text-muted my-5"><i class="fas fa-image fa-3x d-block mb-2"></i> Foto KTP tidak diunggah.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php echo $__env->make('layouts.partials.modals', ['item' => $masyarakat], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/masyarakat/show.blade.php ENDPATH**/ ?>