

<?php $__env->startSection('title', 'Reset Password Akun Masyarakat'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reset Password untuk: <?php echo e($masyarakat->nama_lengkap); ?></h1>
        <a href="<?php echo e(route('petugas.masyarakat.show', $masyarakat->id)); ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Detail Akun
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-key mr-2"></i>Masukkan Password Baru</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Anda akan mengubah password untuk akun dengan NIK <strong><?php echo e($masyarakat->nik); ?></strong>. Pengguna harus menggunakan password baru ini untuk login selanjutnya.</p>
                    <hr>
                    <form action="<?php echo e(route('petugas.masyarakat.resetPasswordByPetugas', $masyarakat->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="password">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password" name="password" required>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="form-text text-muted">Minimal 8 karakter.</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Reset Password</button>
                        <a href="<?php echo e(route('petugas.masyarakat.show', $masyarakat->id)); ?>" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/masyarakat/reset_password_form.blade.php ENDPATH**/ ?>