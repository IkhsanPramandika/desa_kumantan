@push('styles')
<style>
    .sidebar .nav-item .nav-link .sidebar-text-wrap {
        white-space: normal;
        line-height: 1.2;
        margin-left: 0.25rem;
    }
</style>
@endpush

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

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

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('petugas.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Layanan Desa</div>

    @php $isKKActive = request()->is('petugas/permohonan-kk-*'); @endphp
    <li class="nav-item {{ $isKKActive ? 'active' : '' }}">
        <a class="nav-link {{ $isKKActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKK"
           aria-expanded="{{ $isKKActive ? 'true' : 'false' }}" aria-controls="collapseKK">
            <i class="fas fa-fw fa-id-card"></i>
            <span class="sidebar-text-wrap">Layanan Kartu Keluarga</span>
        </a>
        <div id="collapseKK" class="collapse {{ $isKKActive ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Urusan Kartu Keluarga:</h6>
                <a class="collapse-item" href="{{ route('petugas.permohonan-kk-baru.index') }}">Permohonan KK Baru</a>
                <a class="collapse-item" href="{{ route('petugas.permohonan-kk-perubahan.index') }}">Perubahan Data KK</a>
                <a class="collapse-item" href="{{ route('petugas.permohonan-kk-hilang.index') }}">Penerbitan KK Hilang</a>
            </div>
        </div>
    </li>

    @php $isSKActive = request()->is('petugas/permohonan-sk-*') || request()->is('petugas/permohonan-lainnya*'); @endphp
    <li class="nav-item {{ $isSKActive ? 'active' : '' }}">
        <a class="nav-link {{ $isSKActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSK"
           aria-expanded="{{ $isSKActive ? 'true' : 'false' }}" aria-controls="collapseSK">
            <i class="fas fa-fw fa-file-alt"></i>
            <span class="sidebar-text-wrap">Surat Keterangan</span>
        </a>
        <div id="collapseSK" class="collapse {{ $isSKActive ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Jenis Surat Keterangan:</h6>
                <a class="collapse-item" href="{{ route('petugas.permohonan-sk-kelahiran.index') }}">SK Kelahiran</a>
                <a class="collapse-item" href="{{ route('petugas.permohonan-sk-ahli-waris.index') }}">SK Ahli Waris</a>
                <a class="collapse-item" href="{{ route('petugas.permohonan-sk-perkawinan.index') }}">SK Pengantar Nikah</a>
                <a class="collapse-item" href="{{ route('petugas.permohonan-sk-usaha.index') }}">SK Usaha</a>
                <a class="collapse-item" href="{{ route('petugas.permohonan-sk-domisili.index') }}">SK Domisili</a>
                <a class="collapse-item" href="{{ route('petugas.permohonan-sk-tidak-mampu.index') }}">SK Tidak Mampu</a>
                <a class="collapse-item" href="{{ route('petugas.permohonan-lainnya.index') }}">SK Lainnya</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Manajemen Konten & Warga</div>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('petugas.pengumuman.index') }}">
            <i class="fas fa-fw fa-bullhorn"></i>
            <span>Pengumuman Desa</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('petugas.masyarakat.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Manajemen Warga</span>
        </a>
    </li>
    
    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>