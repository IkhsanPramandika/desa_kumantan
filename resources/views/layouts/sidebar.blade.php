{{-- Lokasi: resources/views/layouts/sidebar.blade.php --}}
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('petugas.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-file-signature fa-lg"></i>
        </div>
        <div class="sidebar-brand-text mx-2" style="text-align: left; line-height: 1.1;">
            <span style="font-size: 0.7rem;">Sistem Informasi</span>
            <span class="d-block" style="font-size: 0.95rem; font-weight: bold;">Layanan Desa</span>
            <span class="d-block" style="font-size: 0.85rem;">Kumantan</span>
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Layanan Desa
    </div>

    <!-- Nav Item - Layanan Kartu Keluarga -->
    @php $isKKActive = request()->is('petugas/permohonan-kk-*'); @endphp
    <li class="nav-item {{ $isKKActive ? 'active' : '' }}">
        <a class="nav-link {{ $isKKActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKK"
            aria-expanded="{{ $isKKActive ? 'true' : 'false' }}" aria-controls="collapseKK">
            <i class="fas fa-fw fa-id-card"></i>
            {{-- [PERBAIKAN] Menambahkan style white-space: normal agar teks bisa turun baris --}}
            <span style="white-space: normal; line-height: 1.2;">Layanan Kartu Keluarga</span>
        </a>
        <div id="collapseKK" class="collapse {{ $isKKActive ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Urusan Kartu Keluarga:</h6>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-kk-baru.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-kk-baru.index') }}">Permohonan KK Baru</a>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-kk-perubahan.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-kk-perubahan.index') }}">Perubahan Data KK</a>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-kk-hilang.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-kk-hilang.index') }}">Penerbitan KK Hilang</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Layanan Surat Keterangan -->
    @php $isSKActive = request()->is('petugas/permohonan-sk-*') || request()->is('petugas/permohonan-lainnya*'); @endphp
    <li class="nav-item {{ $isSKActive ? 'active' : '' }}">
        <a class="nav-link {{ $isSKActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSK"
            aria-expanded="{{ $isSKActive ? 'true' : 'false' }}" aria-controls="collapseSK">
            <i class="fas fa-fw fa-file-alt"></i>
            {{-- [PERBAIKAN] Menambahkan style white-space: normal agar teks bisa turun baris --}}
            <span style="white-space: normal; line-height: 1.2;">Layanan Surat Keterangan</span>
        </a>
        <div id="collapseSK" class="collapse {{ $isSKActive ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Jenis Surat Keterangan:</h6>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-sk-kelahiran.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-sk-kelahiran.index') }}">SK Kelahiran</a>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-sk-ahli-waris.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-sk-ahli-waris.index') }}">SK Ahli Waris</a>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-sk-perkawinan.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-sk-perkawinan.index') }}">SK Pengantar Nikah</a>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-sk-usaha.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-sk-usaha.index') }}">SK Usaha</a>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-sk-domisili.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-sk-domisili.index') }}">SK Domisili</a>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-sk-tidak-mampu.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-sk-tidak-mampu.index') }}">SK Tidak Mampu</a>
                <a class="collapse-item {{ request()->routeIs('petugas.permohonan-lainnya.*') ? 'active' : '' }}" href="{{ route('petugas.permohonan-lainnya.index') }}">SK Lainnya</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Manajemen Konten & Warga
    </div>

    <!-- Nav Item - Pengumuman -->
    @php $isPengumumanActive = request()->is('petugas/pengumuman*'); @endphp
    <li class="nav-item {{ $isPengumumanActive ? 'active' : '' }}">
        <a class="nav-link {{ $isPengumumanActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePengumuman"
            aria-expanded="{{ $isPengumumanActive ? 'true' : 'false' }}" aria-controls="collapsePengumuman">
            <i class="fas fa-fw fa-bullhorn"></i>
            <span>Pengumuman Desa</span>
        </a>
        <div id="collapsePengumuman" class="collapse {{ $isPengumumanActive ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('petugas.pengumuman.index') ? 'active' : '' }}" href="{{ route('petugas.pengumuman.index') }}">Kelola Pengumuman</a>
                <a class="collapse-item {{ request()->routeIs('petugas.pengumuman.create') ? 'active' : '' }}" href="{{ route('petugas.pengumuman.create') }}">Tambah Pengumuman</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Manajemen Warga -->
    @php $isMasyarakatActive = request()->is('petugas/masyarakat*'); @endphp
    <li class="nav-item {{ $isMasyarakatActive ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.masyarakat.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Manajemen Warga</span>
        </a>
    </li>
    
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
