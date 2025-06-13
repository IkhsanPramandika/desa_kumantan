

<?php $__env->startSection('title', 'Hasil Pencarian'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Hasil Pencarian untuk "<?php echo e($query); ?>"</h1>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Hasil Ditemukan (<?php echo e($results->count()); ?>)</h6>
    </div>
    <div class="card-body">
        <?php if($results->isEmpty()): ?>
            <p>Tidak ada hasil yang ditemukan untuk "<?php echo e($query); ?>".</p>
        <?php else: ?>
            
            
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipe Permohonan</th>
                            <th>Catatan/Nama Usaha</th>
                            <th>Status</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($result->id); ?></td>
                                <td>
                                    
                                    <?php
                                        $modelClass = class_basename($result);
                                        $type = '';
                                        switch ($modelClass) {
                                            case 'PermohonananKKBaru': $type = 'KK Baru'; break;
                                            case 'PermohonananKKHilang': $type = 'KK Hilang'; break;
                                            case 'PermohonananKKPerubahanData': $type = 'KK Perubahan Data'; break;
                                            case 'PermohonananSKDomisili': $type = 'SK Domisili'; break;
                                            case 'PermohonananSKKelahiran': $type = 'SK Kelahiran'; break;
                                            case 'PermohonananSKKematian': $type = 'SK Kematian'; break;
                                            case 'PermohonananSKPerkawinan': $type = 'SK Perkawinan'; break;
                                            case 'PermohonananSKTidakMampu': $type = 'SK Tidak Mampu'; break;
                                            case 'PermohonananSKUsaha': $type = 'SK Usaha'; break;
                                            default: $type = 'Tidak Diketahui'; break;
                                        }
                                    ?>
                                    <?php echo e($type); ?>

                                </td>
                                <td>
                                    
                                    <?php if(isset($result->catatan)): ?>
                                        <?php echo e(Str::limit($result->catatan, 50)); ?>

                                    <?php elseif(isset($result->nama_usaha)): ?>
                                        <?php echo e(Str::limit($result->nama_usaha, 50)); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($result->status == 'pending'): ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php elseif($result->status == 'diterima'): ?>
                                        <span class="badge badge-success">Diterima</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                                
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script src="<?php echo e(asset('sbadmin/vendor/datatables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('sbadmin/vendor/datatables/dataTables.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('sbadmin/js/demo/datatables-demo.js')); ?>"></script> 
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/search/results.blade.php ENDPATH**/ ?>