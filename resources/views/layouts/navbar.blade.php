<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Tombol Sidebar -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Form Pencarian (tidak diubah) -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search"
        action="{{-- route('search') --}}" method="GET">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Pencarian ..."
                aria-label="Search" aria-describedby="basic-addon2" name="query" value="{{ request('query') }}">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

            <!-- Bagian Kanan Navbar -->
            <ul class="navbar-nav ml-auto">

            <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter" id="notification-badge" style="display: none;"></span>
            </a>
            
            
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Pusat Notifikasi
                </h6>

                <div id="notification-dropdown-list">
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="spinner-border spinner-border-sm mr-2" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                        Memuat notifikasi...
                    </a>
                </div>

                <a class="dropdown-item text-center small text-gray-500" href="{{ route('petugas.notifikasi.index') }}">Tampilkan Semua Notifikasi</a>
            </div>
        </li>
        <!-- Divider -->
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Info (tidak diubah) -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ Auth::user()->name ?? 'Guest' }}
                </span>
                <img class="img-profile rounded-circle" src="{{ asset('sbadmin/img/undraw_profile.svg') }}">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profil
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form-modal').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Keluar
                </a>
                <form id="logout-form-modal" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>

    </ul>
</nav>
