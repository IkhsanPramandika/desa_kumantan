<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo e(route('kepala_desa.dashboard')); ?>">
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

    <li class="nav-item <?php echo e(Request::routeIs('kepala_desa.dashboard') ? 'active' : ''); ?>">
        <a class="nav-link" href="<?php echo e(route('kepala_desa.dashboard')); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    
    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-block">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/layouts/sidebar_kepala_desa.blade.php ENDPATH**/ ?>