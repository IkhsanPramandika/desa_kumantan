<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('petugas.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3 full-text">
            Desa Kumantan
        </div>
        {{-- <div class="sidebar-brand-text mx-3 short-text">
            SIL Desa
        </div> --}}
    </a>

    <hr class="sidebar-divider my-0">
        <li class="nav-item active">
        <a class="nav-link" href="{{ route('petugas.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Layanan Desa
    </div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-fw fa-folder"></i>
            <span>Pengajuan Surat</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Kartu Keluarga:</h6>
                {{-- Penerbitan Kartu Keluarga Baru --}}
                <a class="collapse-item" href="{{ route('permohonan-kk.index') }}">Penerbitan Kartu Keluarga Baru</a>
                {{-- Kartu Keluarga Perubahan Data --}}
                <a class="collapse-item" href="{{ route('permohonan-kk-perubahan.index') }}">Kartu Keluarga Perubahan Data</a>
                {{-- Penerbitan Kartu Keluarga Hilang --}}
                <a class="collapse-item" href="{{ route('permohonan-kk-hilang.index') }}">Penerbitan Kartu Keluarga Hilang</a>

                <div class="collapse-divider"></div>

                <h6 class="collapse-header">Surat Keterangan:</h6>
                {{-- Surat Keterangan Kelahiran & Proses Akta Kelahiran --}}
                <a class="collapse-item" href="{{ route('permohonan-sk-kelahiran.index') }}">Surat Keterangan Kelahiran & Proses Akta Kelahiran</a>
                {{-- Surat Keterangan Ahli Waris --}}
                <a class="collapse-item" href="{{ route('permohonan-sk-ahli-waris.index') }}">Surat Keterangan Ahli Waris</a>
                {{-- Surat Pengantar Nikah --}}
                <a class="collapse-item" href="{{ route('permohonan-sk-perkawinan.index') }}">Surat Pengantar Nikah</a>
                {{-- Surat Keterangan Usaha --}}
                <a class="collapse-item" href="{{ route('permohonan-sk-usaha.index') }}">Surat Keterangan Usaha</a>
                {{-- Surat Keterangan Domisili --}}
                <a class="collapse-item" href="{{ route('permohonan-sk-domisili.index') }}">Surat Keterangan Domisili</a>
                {{-- Surat Keterangan Tidak Mampu --}}
                <a class="collapse-item" href="{{ route('permohonan-sk-tidak-mampu.index') }}">Surat Keterangan Tidak Mampu</a>
                {{-- Surat Keterangan Kematian (jika ada) --}}
                {{-- <a class="collapse-item" href="{{ route('permohonan-sk-kematian.index') }}">Surat Keterangan Kematian</a> --}}
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Pengumuman Desa</span>
        </a>
        {{-- Jika ada sub-menu untuk Pengumuman Desa, tambahkan di sini --}}
        {{-- <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Utilities:</h6>
                <a class="collapse-item" href="utilities-color.html">Colors</a>
            </div>
        </div> --}}
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

   

</ul>