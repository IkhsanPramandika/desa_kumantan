<?php $__env->startSection('title', 'Profil Saya'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Profil Akun</h1>


<?php if(session('status') === 'profile-updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> Informasi profil Anda telah berhasil diperbarui.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php elseif(session('status') === 'password-updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> Password Anda telah berhasil diubah.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<div class="row">
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img class="img-profile rounded-circle mb-3" src="<?php echo e(asset('sbadmin/img/undraw_profile.svg')); ?>" style="max-width: 150px;">
                <h5 class="font-weight-bold"><?php echo e($user->name); ?></h5>
                <p class="text-muted"><?php echo e($user->email); ?></p>
                <span class="badge badge-primary"><?php echo e(ucfirst($user->role)); ?></span>
            </div>
        </div>
    </div>

    
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Edit Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Ubah Password</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    
                    
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <h6 class="font-weight-bold text-primary mb-3">Informasi Akun</h6>
                        <form method="POST" action="<?php echo e(route('petugas.profile.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>

                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input id="name" type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e(old('name', $user->name)); ?>" required autocomplete="name">
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="form-group">
                                <label for="email">Alamat Email</label>
                                <input id="email" type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email', $user->email)); ?>" required autocomplete="email">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>

                    
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <h6 class="font-weight-bold text-primary mb-3">Ubah Password</h6>
                        <form method="POST" action="<?php echo e(route('petugas.profile.password.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            
                            <div class="form-group">
                                <label for="current_password">Password Saat Ini</label>
                                <div class="input-group">
                                    <input id="current_password" type="password" class="form-control <?php $__errorArgs = ['current_password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="current_password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php $__errorArgs = ['current_password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback d-block" role="alert"><strong><?php echo e($message); ?></strong></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="form-group">
                                <label for="password">Password Baru</label>
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control <?php $__errorArgs = ['password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php $__errorArgs = ['password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback d-block" role="alert"><strong><?php echo e($message); ?></strong></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Ubah Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Ketika tombol dengan class .toggle-password di-klik
    $(".toggle-password").click(function() {
        // Cari elemen ikon di dalam tombol yang di-klik
        var icon = $(this).find('i');
        // Cari elemen input di dalam grup yang sama
        var input = $(this).closest('.input-group').find('input');

        // Toggle (ubah) tipe input
        if (input.attr("type") == "password") {
            // Jika tipenya password, ubah ke text
            input.attr("type", "text");
            // Ubah ikon mata menjadi mata-coret
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            // Jika tipenya text, ubah kembali ke password
            input.attr("type", "password");
            // Ubah ikon kembali menjadi mata
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/profile/edit.blade.php ENDPATH**/ ?>