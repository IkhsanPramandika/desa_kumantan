

<?php $__env->startSection('title', 'Proses Surat Keterangan Ahli Waris'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Proses & Edit Surat Keterangan Ahli Waris</h1>


<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <p><strong>Harap perbaiki error berikut:</strong></p>
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?php echo e(route('petugas.permohonan-sk-ahli-waris.selesaikan', $permohonan->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class="row">
        
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Data Pewaris (Alm)</h5>
                    <dl>
                        <dt>Nama:</dt><dd><?php echo e($permohonan->nama_pewaris ?? '-'); ?></dd>
                        <dt>NIK:</dt><dd><?php echo e($permohonan->nik_pewaris ?? '-'); ?></dd>
                        <dt>Tanggal Meninggal:</dt><dd><?php echo e($permohonan->tanggal_meninggal_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_meninggal_pewaris)->isoFormat('D MMMM YYYY') : '-'); ?></dd>
                    </dl>
                    <hr>
                    <h5 class="font-weight-bold mt-4">Daftar Ahli Waris</h5>
                    <ol>
                        <?php
                            $ahliWarisListReadOnly = is_string($permohonan->daftar_ahli_waris) ? json_decode($permohonan->daftar_ahli_waris, true) : $permohonan->daftar_ahli_waris;
                        ?>
                        <?php $__empty_1 = true; $__currentLoopData = $ahliWarisListReadOnly ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ahliWaris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li><?php echo e($ahliWaris['nama'] ?? '-'); ?> (<?php echo e($ahliWaris['hubungan'] ?? '-'); ?>)</li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li>Tidak ada data ahli waris yang diajukan.</li>
                        <?php endif; ?>
                    </ol>
                </div>
            </div>
        </div>

        
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Isian Surat oleh Petugas</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final.</p>
                    <div class="alert alert-info">Nomor surat akan dibuat secara otomatis oleh sistem.</div>

                    <h5 class="font-weight-bold mt-4">Data Pewaris (Alm)</h5>
                    <div class="form-group">
                        <label>Nama Lengkap Pewaris</label>
                        <input type="text" class="form-control" name="nama_pewaris" value="<?php echo e(old('nama_pewaris', $permohonan->nama_pewaris)); ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>NIK Pewaris</label>
                            <input type="text" class="form-control" name="nik_pewaris" value="<?php echo e(old('nik_pewaris', $permohonan->nik_pewaris)); ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tempat Lahir Pewaris</label>
                            <input type="text" class="form-control" name="tempat_lahir_pewaris" value="<?php echo e(old('tempat_lahir_pewaris', $permohonan->tempat_lahir_pewaris)); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tanggal Lahir Pewaris</label>
                            <input type="date" class="form-control" name="tanggal_lahir_pewaris" value="<?php echo e(old('tanggal_lahir_pewaris', $permohonan->tanggal_lahir_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pewaris)->format('Y-m-d') : '')); ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Meninggal Pewaris</label>
                            <input type="date" class="form-control" name="tanggal_meninggal_pewaris" value="<?php echo e(old('tanggal_meninggal_pewaris', $permohonan->tanggal_meninggal_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_meninggal_pewaris)->format('Y-m-d') : '')); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat Terakhir Pewaris</label>
                        <textarea class="form-control" name="alamat_pewaris" rows="2" required><?php echo e(old('alamat_pewaris', $permohonan->alamat_pewaris)); ?></textarea>
                    </div>
                    

                    <hr>
                    <h5 class="font-weight-bold mt-4">Daftar Ahli Waris</h5>
                    <div id="ahli-waris-fields">
                        <?php
                            $ahliWarisData = old('daftar_ahli_waris', $permohonan->daftar_ahli_waris);
                            if (is_string($ahliWarisData)) {
                                $ahliWarisData = json_decode($ahliWarisData, true) ?? [];
                            }
                            if (empty($ahliWarisData)) {
                                $ahliWarisData = [['nama'=>'', 'nik'=>'', 'hubungan'=>'', 'alamat'=>'']];
                            }
                        ?>

                        <?php $__currentLoopData = $ahliWarisData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ahliWaris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="ahli-waris-item card mb-3">
                            <div class="card-body">
                                <h6 class="font-weight-bold">Ahli Waris <?php echo e($index + 1); ?></h6>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" class="form-control" name="daftar_ahli_waris[<?php echo e($index); ?>][nama]" value="<?php echo e($ahliWaris['nama'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>NIK</label>
                                    <input type="text" class="form-control" name="daftar_ahli_waris[<?php echo e($index); ?>][nik]" value="<?php echo e($ahliWaris['nik'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Hubungan Keluarga</label>
                                    <input type="text" class="form-control" name="daftar_ahli_waris[<?php echo e($index); ?>][hubungan]" value="<?php echo e($ahliWaris['hubungan'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea class="form-control" name="daftar_ahli_waris[<?php echo e($index); ?>][alamat]" rows="2" required><?php echo e($ahliWaris['alamat'] ?? ''); ?></textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-ahli-waris">Hapus</button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <button type="button" id="add-ahli-waris" class="btn btn-secondary btn-sm mt-2">Tambah Ahli Waris</button>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')">
                        <i class="fas fa-print"></i> Buat Surat Final & Selesaikan
                    </button>
                    <a href="<?php echo e(route('petugas.permohonan-sk-ahli-waris.show', $permohonan->id)); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php
    // --- PERBAIKAN KUNCI ADA DI SINI ---
    // Decode data menjadi array SEBELUM digunakan oleh fungsi count() di dalam script
    $ahliWarisForScript = old('daftar_ahli_waris', $permohonan->daftar_ahli_waris);
    if (is_string($ahliWarisForScript)) {
        $ahliWarisForScript = json_decode($ahliWarisForScript, true) ?? [];
    }
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Gunakan variabel yang sudah pasti array
    let ahliWarisIndex = <?php echo e(count($ahliWarisForScript)); ?>;
    const container = document.getElementById('ahli-waris-fields');

    document.getElementById('add-ahli-waris').addEventListener('click', function () {
        const newItem = document.createElement('div');
        newItem.classList.add('ahli-waris-item', 'card', 'mb-3');
        newItem.innerHTML = `
            <div class="card-body">
                <h6 class="font-weight-bold">Ahli Waris ${ahliWarisIndex + 1}</h6>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-control" name="daftar_ahli_waris[${ahliWarisIndex}][nama]" required>
                </div>
                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" class="form-control" name="daftar_ahli_waris[${ahliWarisIndex}][nik]" required>
                </div>
                <div class="form-group">
                    <label>Hubungan Keluarga</label>
                    <input type="text" class="form-control" name="daftar_ahli_waris[${ahliWarisIndex}][hubungan]" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea class="form-control" name="daftar_ahli_waris[${ahliWarisIndex}][alamat]" rows="2" required></textarea>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-ahli-waris">Hapus</button>
            </div>
        `;
        container.appendChild(newItem);
        ahliWarisIndex++;
    });

    container.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-ahli-waris')) {
            e.target.closest('.ahli-waris-item').remove();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_ahli_waris/edit_surat.blade.php ENDPATH**/ ?>