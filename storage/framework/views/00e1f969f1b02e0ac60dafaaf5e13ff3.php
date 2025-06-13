<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Tombol Sidebar -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Form Pencarian -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search"
        action="<?php echo e(route('search')); ?>" method="GET">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Pencarian ..."
                aria-label="Search" aria-describedby="basic-addon2" name="query" value="<?php echo e(request('query')); ?>">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Bagian Kanan Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Dropdown Notifikasi (Desain Baru) -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter" id="notifikasi-counter">
                    <?php echo e(auth()->user()->unreadNotifications->count()); ?>

                </span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Pusat Notifikasi
                </h6>
                <div id="notifikasi-list">
                    <?php $__empty_1 = true; $__currentLoopData = auth()->user()->unreadNotifications->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('petugas.notifikasi.read', $notification->id)); ?>" class="dropdown-item d-flex align-items-center">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">
                                    <?php echo e(\Carbon\Carbon::parse($notification->created_at)->diffForHumans()); ?>

                                </div>
                                <span class="font-weight-bold">
                                    <?php echo e(Str::limit($notification->data['pesan'], 40)); ?>

                                </span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada notifikasi baru</a>
                    <?php endif; ?>
                </div>

                <a class="dropdown-item text-center small text-gray-500" href="<?php echo e(route('petugas.notifikasi.index')); ?>">Tampilkan Semua</a>
            </div>
        </li>

        <!-- Divider -->
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Info -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php echo e(Auth::user()->name ?? 'Guest'); ?>

                </span>
                <img class="img-profile rounded-circle" src="<?php echo e(asset('sbadmin/img/undraw_profile.svg')); ?>">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Settings
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form-modal').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
                <form id="logout-form-modal" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </li>

    </ul>

</nav>
<?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/layouts/navbar.blade.php ENDPATH**/ ?>