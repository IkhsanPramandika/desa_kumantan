    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>@yield('title', 'Sistem Informasi Layanan Desa Kumantan')</title>

        <!-- Custom fonts for this template-->
        <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
        <!-- Custom styles for this template-->
        <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">
      <link href="{{ asset('sbadmin/css/custom.css') }}" rel="stylesheet">

        
    </head>

    <body id="page-top" class="sb-nav-fixed">
        <div id="wrapper">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <!-- Navbar -->
                    @include('layouts.navbar')

                    <!-- Main Content -->
                    <div class="container-fluid px-4 py-4">
                        @yield('content')
                    </div>

                    <!-- Footer -->
                    @include('layouts.footer')
                </div>
                <script>
</script>

            </div>
        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- Core plugin JavaScript-->
        <script src="{{ asset('sbadmin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
        <!-- Custom scripts for all pages-->
        <script src="{{ asset('sbadmin/js/sb-admin-2.min.js') }}"></script>

        @stack('scripts')
    </body>
    </html>
