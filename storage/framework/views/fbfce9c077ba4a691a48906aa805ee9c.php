

<?php $__env->startSection('title', 'Dashboard Petugas'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card-link:hover {
        transform: translateY(-5px);
        text-decoration: none;
        color: inherit;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .welcome-card {
        background: linear-gradient(45deg, #4e73df, #224abe);
        color: white;
    }
    .welcome-card .display-4 {
        font-weight: 300;
    }
    .welcome-card p {
        font-size: 1.1rem;
    }
    .stat-card .h5 {
        font-size: 2rem;
    }
    .border-left-revisi {
        border-left: 0.25rem solid #fd7e14 !important;
    }
    .text-revisi {
        color: #fd7e14 !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="card shadow welcome-card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4">Selamat Datang, <?php echo e(Auth::user()->name); ?>!</h1>
                    <p class="lead mb-0">Anda memiliki beberapa permohonan yang menunggu untuk diproses. Mari kita mulai.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <i class="fas fa-server fa-5x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($overallTotalPending ?? 0); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-revisi shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-revisi text-uppercase mb-1">Perlu Revisi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($overallTotalRevisi ?? 0); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-exclamation-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Diproses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($overallTotalInProcess ?? 0); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-sync-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($overallTotalAccepted ?? 0); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Akses Cepat Layanan Surat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php $__currentLoopData = $permohonanDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-lg-6 col-md-6 mb-4">
                            <a href="<?php echo e(route($details['route'])); ?>" class="card-link">
                                <div class="card border-left-primary shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="mr-3">
                                                <div class="text-primary font-weight-bold text-uppercase mb-1"><?php echo e($details['title']); ?></div>
                                                <div class="small text-gray-600"><?php echo e($stats[$key]['total'] ?? 0); ?> Total Permohonan</div>
                                            </div>
                                            <?php if(($stats[$key]['pending'] ?? 0) > 0): ?>
                                            <span class="badge badge-danger badge-pill" style="font-size: 1rem;"><?php echo e($stats[$key]['pending']); ?></span>
                                            <?php else: ?>
                                            <i class="fas <?php echo e($details['icon']); ?> fa-2x text-gray-300"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentPermohonan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permohonan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold"><?php echo e($permohonan->getJudulNotifikasi()); ?></div>
                                        <small class="text-muted">Diajukan oleh: <?php echo e($permohonan->masyarakat->nama_lengkap ?? 'N/A'); ?></small>
                                    </td>
                                    <td class="text-right">
                                        <small><?php echo e($permohonan->created_at->diffForHumans()); ?></small>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo e($permohonan->getRouteTujuan()); ?>" class="btn btn-sm btn-outline-primary">Lihat</a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td class="text-center text-muted py-4">
                                        <i class="fas fa-history fa-2x mb-2 d-block"></i>
                                        Belum ada aktivitas terbaru.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Komposisi Status Permohonan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> Pending</span>
                        <span class="mr-2"><i class="fas fa-circle text-revisi"></i> Revisi</span>
                        <span class="mr-2"><i class="fas fa-circle text-info"></i> Diproses</span>
                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Selesai</span>
                    </div>
                </div>
            </div>
             <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Warga Terdaftar</h6>
                    <div class="h4 mb-0 font-weight-bold text-gray-800"><?php echo e($totalUsers ?? 0); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('sbadmin/vendor/chart.js/Chart.min.js')); ?>"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById("statusPieChart").getContext('2d');
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Pending", "Perlu Revisi", "Diproses", "Selesai"],
            datasets: [{
                data: [
                    <?php echo e($overallTotalPending ?? 0); ?>, 
                    <?php echo e($overallTotalRevisi ?? 0); ?>, 
                    <?php echo e($overallTotalInProcess ?? 0); ?>, 
                    <?php echo e($overallTotalAccepted ?? 0); ?>

                ],
                backgroundColor: ['#f6c23e', '#fd7e14', '#36b9cc', '#1cc88a'],
                hoverBackgroundColor: ['#f4b619', '#e86a04', '#2c9faf', '#17a673'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: { display: false },
            cutoutPercentage: 80,
        },
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/dashboard.blade.php ENDPATH**/ ?>