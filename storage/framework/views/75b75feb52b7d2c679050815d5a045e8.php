

<?php $__env->startSection('title', 'Proses Surat Pengantar Nikah'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses & Edit Surat Pengantar Nikah</h1>
    <a href="<?php echo e(route('petugas.permohonan-sk-perkawinan.show', $permohonan->id)); ?>" class="btn btn-secondary btn-sm">
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

<form action="<?php echo e(route('petugas.permohonan-sk-perkawinan.selesaikan', $permohonan->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class="row">
        
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Data Referensi dari Pemohon</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Calon Mempelai Pria</h5>
                    <dl>
                        <dt>Nama:</dt><dd><?php echo e($permohonan->nama_pria ?? '-'); ?></dd>
                        <dt>NIK:</dt><dd><?php echo e($permohonan->nik_pria ?? '-'); ?></dd>
                        <dt>Tempat, Tgl Lahir:</dt><dd><?php echo e($permohonan->tempat_lahir_pria ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_pria ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pria)->isoFormat('D MMMM YYYY') : '-'); ?></dd>
                        <dt>Alamat:</dt><dd><?php echo e($permohonan->alamat_pria ?? '-'); ?></dd>
                    </dl>
                    <hr>
                    <h5 class="font-weight-bold mt-4">Calon Mempelai Wanita</h5>
                    <dl>
                        <dt>Nama:</dt><dd><?php echo e($permohonan->nama_wanita ?? '-'); ?></dd>
                        <dt>NIK:</dt><dd><?php echo e($permohonan->nik_wanita ?? '-'); ?></dd>
                        <dt>Tempat, Tgl Lahir:</dt><dd><?php echo e($permohonan->tempat_lahir_wanita ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_wanita ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_wanita)->isoFormat('D MMMM YYYY') : '-'); ?></dd>
                        <dt>Alamat:</dt><dd><?php echo e($permohonan->alamat_wanita ?? '-'); ?></dd>
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
                                'file_ktp_mempelai' => 'KTP Kedua Mempelai',
                                'surat_nikah_orang_tua' => 'Surat Nikah Orang Tua',
                                'kartu_imunisasi_catin' => 'Kartu Imunisasi Catin',
                                'sertifikat_elsimil' => 'Sertifikat Elsimil',
                                'akta_penceraian' => 'Akta Perceraian',
                            ];
                        ?>
                        <?php $__currentLoopData = $lampiran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div><i class="fas fa-file-alt text-gray-500 mr-2"></i> <?php echo e($label); ?></div>
                            <?php if($permohonan->$field): ?>
                                <a href="<?php echo e(asset('storage/' . $permohonan->$field)); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i>Form Isian Surat oleh Petugas</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final. Nomor surat akan dibuat secara otomatis.</p>
                    
                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Data Calon Mempelai Pria</h5>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Nama Lengkap Pria</label><input type="text" class="form-control" name="nama_pria" value="<?php echo e(old('nama_pria', $permohonan->nama_pria)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>NIK Pria</label><input type="text" class="form-control" name="nik_pria" value="<?php echo e(old('nik_pria', $permohonan->nik_pria)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>Tempat Lahir Pria</label><input type="text" class="form-control" name="tempat_lahir_pria" value="<?php echo e(old('tempat_lahir_pria', $permohonan->tempat_lahir_pria)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>Tanggal Lahir Pria</label><input type="date" class="form-control" name="tanggal_lahir_pria" value="<?php echo e(old('tanggal_lahir_pria', $permohonan->tanggal_lahir_pria ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pria)->format('Y-m-d') : '')); ?>" required></div>
                        <div class="col-md-12 form-group"><label>Alamat Lengkap Pria</label><textarea class="form-control" name="alamat_pria" rows="3" required><?php echo e(old('alamat_pria', $permohonan->alamat_pria)); ?></textarea></div>
                    </div>

                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Data Calon Mempelai Wanita</h5>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Nama Lengkap Wanita</label><input type="text" class="form-control" name="nama_wanita" value="<?php echo e(old('nama_wanita', $permohonan->nama_wanita)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>NIK Wanita</label><input type="text" class="form-control" name="nik_wanita" value="<?php echo e(old('nik_wanita', $permohonan->nik_wanita)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>Tempat Lahir Wanita</label><input type="text" class="form-control" name="tempat_lahir_wanita" value="<?php echo e(old('tempat_lahir_wanita', $permohonan->tempat_lahir_wanita)); ?>" required></div>
                        <div class="col-md-6 form-group"><label>Tanggal Lahir Wanita</label><input type="date" class="form-control" name="tanggal_lahir_wanita" value="<?php echo e(old('tanggal_lahir_wanita', $permohonan->tanggal_lahir_wanita ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_wanita)->format('Y-m-d') : '')); ?>" required></div>
                        <div class="col-md-12 form-group"><label>Alamat Lengkap Wanita</label><textarea class="form-control" name="alamat_wanita" rows="3" required><?php echo e(old('alamat_wanita', $permohonan->alamat_wanita)); ?></textarea></div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="<?php echo e(route('petugas.permohonan-sk-perkawinan.show', $permohonan->id)); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')">
                        <i class="fas fa-print"></i> Buat Surat Final & Selesaikan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_nikah/edit_surat.blade.php ENDPATH**/ ?>