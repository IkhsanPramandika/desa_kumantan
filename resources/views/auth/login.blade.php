<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistem Informasi Layanan Desa Kumantan - Login Petugas</title>

    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .login-branding-panel {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 3rem;
        }
        .login-branding-panel img {
            max-width: 150px;
            margin-bottom: 1.5rem;
        }
        .login-branding-panel h2 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .login-branding-panel p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
        }
        .form-group-icon {
            position: relative;
        }
        .form-group-icon .form-control-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #a0aec0;
            pointer-events: none;
        }
        .form-control-icon-input {
            padding-left: 2.75rem !important;
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block login-branding-panel">
                                <img src="{{ asset('sbadmin/img/logo_kampar.png') }}" alt="Logo Kabupaten Kampar">
                                <h2>Layanan Desa Kumantan</h2>
                                <p>Portal digital untuk mempermudah manajemen dan proses administrasi layanan desa bagi petugas.</p>
                            </div>

                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Selamat Datang Kembali!</h1>
                                        <p class="text-muted mb-4">Silakan masuk untuk melanjutkan.</p>
                                    </div>

                                    @if (session('status'))
                                        <div class="alert alert-success mb-4 text-sm">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login') }}" class="user">
                                        @csrf

                                        <div class="form-group form-group-icon">
                                            <i class="fas fa-envelope form-control-icon"></i>
                                            <input type="email" class="form-control form-control-user form-control-icon-input @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email') }}" required autofocus
                                                placeholder="Alamat Email">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>

                                        {{-- [PERUBAHAN DI SINI] Input Password sekarang menggunakan input-group --}}
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" style="padding-left: 1rem; padding-right: 1rem; background: #f8f9fc; border-right: none;"><i class="fas fa-lock text-gray-500"></i></span>
                                                </div>
                                                <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror" style="border-left: none; padding-left: 0.5rem;"
                                                    id="password" name="password" required placeholder="Password">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary toggle-password" type="button" style="border-left: none; border-top-right-radius: 0.35rem; border-bottom-right-radius: 0.35rem;">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                             @error('password')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
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
                                        @if (Route::has('password.request'))
                                            <a class="small" href="{{ route('password.request') }}">Lupa Password?</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin/js/sb-admin-2.min.js') }}"></script>

    <script>
    $(document).ready(function() {
        $(".toggle-password").click(function() {
            var icon = $(this).find('i');
            var input = $(this).closest('.input-group').find('input[type="password"], input[type="text"]'); // Target both types

            if (input.attr("type") == "password") {
                input.attr("type", "text");
                icon.removeClass("fa-eye").addClass("fa-eye-slash");
            } else {
                input.attr("type", "password");
                icon.removeClass("fa-eye-slash").addClass("fa-eye");
            }
        });
    });
    </script>

</body>
</html>