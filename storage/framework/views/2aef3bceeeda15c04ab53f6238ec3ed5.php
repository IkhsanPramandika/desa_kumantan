<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo e(route('petugas.dashboard')); ?>">
        <div class="sidebar-brand-icon">
            <i class="fas fa-file-signature fa-lg"></i>
        </div>

        <!-- 
        PERBAIKAN: Teks dipecah menjadi beberapa baris menggunakan <span> 
        dengan class d-block dari Bootstrap agar setiap span mengambil satu baris baru.
        -->
        <div class="sidebar-brand-text mx-2" style="text-align: left;">
            <span style="font-size: 0.65rem; line-height: 1;">Sistem Informasi</span>
            <span class="d-block" style="font-size: 0.9rem; line-height: 1; font-weight: bold;">Layanan Desa</span>
            <span class="d-block" style="font-size: 0.8rem; line-height: 1;">Kumantan</span>
        </div>
    </a>

    <hr class="sidebar-divider my-0">
        <li class="nav-item active">
        <a class="nav-link" href="<?php echo e(route('petugas.dashboard')); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Layanan Desa
    </div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengajuanSurat"
            aria-expanded="true" aria-controls="collapsePengajuanSurat">
            <i class="fas fa-fw fa-folder"></i>
            <span>Pengajuan Surat</span>
        </a>
        
        <div id="collapsePengajuanSurat" class="collapse" aria-labelledby="headingPengajuanSurat" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Kartu Keluarga:</h6>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-kk-baru.index')); ?>">Permohonan Kartu Keluarga Baru</a>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-kk-perubahan.index')); ?>">Kartu Keluarga Perubahan Data</a>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-kk-hilang.index')); ?>">Penerbitan Kartu Keluarga Hilang</a>

                <div class="collapse-divider"></div>

                <h6 class="collapse-header">Surat Keterangan:</h6>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-sk-kelahiran.index')); ?>">Surat Keterangan Kelahiran & Proses Akta Kelahiran</a>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-sk-ahli-waris.index')); ?>">Surat Keterangan Ahli Waris</a>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-sk-perkawinan.index')); ?>">Surat Pengantar Nikah</a>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-sk-usaha.index')); ?>">Surat Keterangan Usaha</a>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-sk-domisili.index')); ?>">Surat Keterangan Domisili</a>
                <a class="collapse-item" href="<?php echo e(route('petugas.permohonan-sk-tidak-mampu.index')); ?>">Surat Keterangan Tidak Mampu</a>
            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengumumanDesa"
            aria-expanded="true" aria-controls="collapsePengumumanDesa">
            <i class="fas fa-fw fa-bullhorn"></i> 
            <span>Pengumuman Desa</span>
        </a>
        
        <div id="collapsePengumumanDesa" class="collapse" aria-labelledby="headingPengumumanDesa" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Pengumuman Desa:</h6>
                <a class="collapse-item" href="<?php echo e(route('petugas.pengumuman.index')); ?>">Kelola Pengumuman</a>
                <a class="collapse-item" href="<?php echo e(route('petugas.pengumuman.create')); ?>">Tambah Pengumuman</a>
                
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Akun Masyarakat Desa Kumantan
    </div>

     <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseManajemenMasyarakat"
            aria-expanded="true" aria-controls="collapseManajemenMasyarakat">
            <i class="fas fa-fw fa-users-cog"></i> 
            <span>Manajemen Warga</span> 
        </a>
        <div id="collapseManajemenMasyarakat" class="collapse" aria-labelledby="headingManajemenMasyarakat" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Kelola Akun Warga:</h6>
                <a class="collapse-item" href="<?php echo e(route('petugas.masyarakat.index')); ?>">Daftar Akun Warga</a>
                
                
                
            </div>
         </div>
    </li>
 
    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

   

</ul><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>