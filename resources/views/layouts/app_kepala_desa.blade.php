<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Dashboard Kepala Desa')</title>

    {{-- Fonts dan Styles --}}
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <meta name="user-id" content="{{ Auth::check() ? Auth::id() : '' }}">
    
    {{-- Menggunakan Vite untuk CSS dan JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Untuk style tambahan per halaman --}}
    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        
        {{-- Memuat sidebar khusus untuk KEPALA DESA --}}
        @include('layouts.sidebar_kepala_desa')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                @include('layouts.navbar')

                <div class="container-fluid px-4 py-4">
                    @yield('content')
                </div>

            </div>
            
            @include('layouts.footer')

        </div>
    </div>

    {{-- Script JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    
    {{-- Untuk script tambahan per halaman --}}
    @stack('scripts')
</body>
</html>