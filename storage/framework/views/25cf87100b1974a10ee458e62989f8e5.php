<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">


    <title>Sistem Informasi Layanan Desa Kumantan - Login Petugas Desa</title>

    <link href="<?php echo e(asset('sbadmin/vendor/fontawesome-free/css/all.min.css')); ?>" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="<?php echo e(asset('sbadmin/css/sb-admin-2.min.css')); ?>" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image" style="display: flex; align-items: center; justify-content: center; padding: 2rem;">
                                
                                
                                <img src="<?php echo e(asset('sbadmin/img/logo_kampar.png')); ?>" alt="Logo Kabupaten Kampar" style="max-width: 80%; max-height: 80%; object-fit: contain;">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Selamat Datang!</h1> 
                                        <p class="text-muted mb-4">di Sistem Informasi Layanan Desa Kumantan</p> 
                                    </div>

                                    <?php if(session('status')): ?>
                                        <div class="alert alert-success mb-4 font-medium text-sm text-green-600">
                                            <?php echo e(session('status')); ?>

                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('login')); ?>" class="user">
                                        <?php echo csrf_field(); ?>

                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                                                aria-describedby="emailHelp"
                                                placeholder="Masukkan Alamat Email...">
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="password" name="password" required
                                                placeholder="Password">
                                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                                                <label class="custom-control-label" for="remember_me">Ingat Saya</label>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        
                                        
                                        
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <?php if(Route::has('password.request')): ?>
                                            <a class="small" href="<?php echo e(route('password.request')); ?>">
                                                Lupa Password?
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="<?php echo e(asset('sbadmin/vendor/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>

    <script src="<?php echo e(asset('sbadmin/vendor/jquery-easing/jquery.easing.min.js')); ?>"></script>

    <script src="<?php echo e(asset('sbadmin/js/sb-admin-2.min.js')); ?>"></script>

</body>

</html><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/auth/login.blade.php ENDPATH**/ ?>