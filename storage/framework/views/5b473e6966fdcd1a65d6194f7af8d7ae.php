

<?php $__env->startSection('title', 'Proses Surat Keterangan Usaha'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses & Edit Surat Keterangan Usaha</h1>
    <a href="<?php echo e(route('petugas.permohonan-sk-usaha.show', $permohonan->id)); ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Detail
    </a>
</div>

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

<form action="<?php echo e(route('petugas.permohonan-sk-usaha.selesaikan', $permohonan->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class="row">
        
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Data Referensi dari Pemohon</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Data Pemohon</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Nama</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_pemohon ?? '-'); ?></dd>
                        <dt class="col-sm-4">NIK</dt><dd class="col-sm-8"><?php echo e($permohonan->nik_pemohon ?? '-'); ?></dd>
                    </dl>
                    <hr>
                    <h5 class="font-weight-bold mt-4">Data Usaha</h5>
                    <dl>
                        <dt>Nama Usaha</dt><dd><?php echo e($permohonan->nama_usaha ?? '-'); ?></dd>
                        <dt>Alamat Usaha</dt><dd><?php echo e($permohonan->alamat_usaha ?? '-'); ?></dd>
                        <dt>Keperluan Surat</dt><dd><?php echo e($permohonan->keperluan_surat ?? '-'); ?></dd>
                    </dl>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
                <div class="card-body">
                     <ul class="list-group list-group-flush">
                        <?php
                            $lampiran = [
                                'file_kk' => 'Kartu Keluarga',
                                'file_ktp' => 'KTP Pemohon',
                            ];
                        ?>
                        <?php $__currentLoopData = $lampiran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div><i class="fas fa-file-alt text-gray-500 mr-2"></i> <?php echo e($label); ?></div>
                            <?php if($permohonan->$field): ?>
                                <a href="<?php echo e(asset('storage/' . $permohonan->$field)); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye fa-sm"></i> Lihat</a>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak Ada</span>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>

        
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i>Form Isian Surat oleh Petugas</h6></div>
                <div class="card-body">
                    <p class="text-muted small">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final. Nomor surat akan dibuat secara otomatis.</p>

                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Data Pemohon (Akan Tercantum di Surat)</h5>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Nama Pemohon</label><input type="text" class="form-control" name="nama_pemohon" value="<?php echo e(old('nama_pemohon', $permohonan->nama_pemohon)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>NIK Pemohon</label><input type="text" class="form-control" name="nik_pemohon" value="<?php echo e(old('nik_pemohon', $permohonan->nik_pemohon)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="Laki-laki" <?php if(old('jenis_kelamin', $permohonan->jenis_kelamin) == 'Laki-laki'): echo 'selected'; endif; ?>>Laki-laki</option>
                                <option value="Perempuan" <?php if(old('jenis_kelamin', $permohonan->jenis_kelamin) == 'Perempuan'): echo 'selected'; endif; ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group"><label>Tempat Lahir</label><input type="text" class="form-control" name="tempat_lahir" value="<?php echo e(old('tempat_lahir', $permohonan->tempat_lahir)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>Tanggal Lahir</label><input type="date" class="form-control" name="tanggal_lahir" value="<?php echo e(old('tanggal_lahir', $permohonan->tanggal_lahir ? \Carbon\Carbon::parse($permohonan->tanggal_lahir)->format('Y-m-d') : '')); ?>" required></div>
                        <div class="col-md-12 form-group"><label>Alamat Pemohon</label><textarea class="form-control" name="alamat_pemohon" rows="3" required><?php echo e(old('alamat_pemohon', $permohonan->alamat_pemohon)); ?></textarea></div>
                    </div>

                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Data Usaha</h5>
                    <div class="form-group"><label>Nama Usaha</label><input type="text" class="form-control" name="nama_usaha" value="<?php echo e(old('nama_usaha', $permohonan->nama_usaha)); ?>" required></div>
                    <div class="form-group"><label>Alamat Usaha</label><textarea class="form-control" name="alamat_usaha" rows="3" required><?php echo e(old('alamat_usaha', $permohonan->alamat_usaha)); ?></textarea></div>
                    <div class="form-group"><label>Keperluan Surat</label><textarea class="form-control" name="keperluan_surat" rows="2" required><?php echo e(old('keperluan_surat', $permohonan->keperluan_surat)); ?></textarea></div>
                </div>
                <div class="card-footer text-right">
                    <a href="<?php echo e(route('petugas.permohonan-sk-usaha.show', $permohonan->id)); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')"><i class="fas fa-print"></i> Buat Surat Final & Selesaikan</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_usaha/edit_surat.blade.php ENDPATH**/ ?>