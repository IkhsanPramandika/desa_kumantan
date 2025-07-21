

<?php $__env->startSection('title', 'Proses Surat Keterangan Tidak Mampu'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Proses & Edit Surat Keterangan Tidak Mampu</h1>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>


<form action="<?php echo e(route('petugas.permohonan-sk-tidak-mampu.selesaikan', $permohonan->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class="row">
        
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan Masyarakat</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Data Pemohon</h5>
                    <dl class="row">
                        
                        <dt class="col-sm-4">Nama</dt><dd class="col-sm-8"><?php echo e($permohonan->masyarakat->nama_lengkap ?? '-'); ?></dd>
                        <dt class="col-sm-4">NIK</dt><dd class="col-sm-8"><?php echo e($permohonan->masyarakat->nik ?? '-'); ?></dd>
                    </dl>
                    <hr>
                    <h5 class="font-weight-bold mt-4">Keperluan Surat</h5>
                    <p><?php echo e($permohonan->keperluan_surat ?? 'Tidak ada keterangan.'); ?></p>
                    <hr>
                    <h5 class="font-weight-bold mt-4">Lampiran</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Kartu Keluarga
                            <?php if($permohonan->file_kk): ?>
                                <a href="<?php echo e(asset('storage/' . $permohonan->file_kk)); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak Ada</span>
                            <?php endif; ?>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            KTP
                            <?php if($permohonan->file_ktp): ?>
                                <a href="<?php echo e(asset('storage/' . $permohonan->file_ktp)); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak Ada</span>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Form Isian Surat oleh Petugas</h6></div>
                <div class="card-body">
                    <p class="text-muted">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final.</p>
                    <div class="alert alert-info">Nomor surat akan dibuat secara otomatis oleh sistem.</div>

                    
                    <h5 class="font-weight-bold mt-4">Data Pemohon (Yang Akan Tercantum di Surat)</h5>
                    <div class="form-group">
                        <label for="nama_pemohon">Nama Pemohon</label>
                        
                        <input type="text" class="form-control" name="nama_pemohon" value="<?php echo e(old('nama_pemohon', $permohonan->masyarakat->nama_lengkap)); ?>" required>
                    </div>
                     <div class="form-group">
                        <label for="nik_pemohon">NIK Pemohon</label>
                        
                        <input type="text" class="form-control" name="nik_pemohon" value="<?php echo e(old('nik_pemohon', $permohonan->masyarakat->nik)); ?>" required>
                    </div>

                    <hr>
                    <h5 class="font-weight-bold mt-4">Data Anak/Orang Tua Terkait (Jika Ada)</h5>
                    <div class="form-group">
                        <label>Nama Terkait</label>
                        <input type="text" class="form-control" name="nama_terkait" value="<?php echo e(old('nama_terkait', $permohonan->nama_terkait)); ?>">
                    </div>
                    <div class="form-group">
                        <label>NIK Terkait</label>
                        <input type="text" class="form-control" name="nik_terkait" value="<?php echo e(old('nik_terkait', $permohonan->nik_terkait)); ?>">
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir Terkait</label>
                        <input type="text" class="form-control" name="tempat_lahir_terkait" value="<?php echo e(old('tempat_lahir_terkait', $permohonan->tempat_lahir_terkait)); ?>">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir Terkait</label>
                        <input type="date" class="form-control" name="tanggal_lahir_terkait" value="<?php echo e(old('tanggal_lahir_terkait', $permohonan->tanggal_lahir_terkait ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_terkait)->format('Y-m-d') : '')); ?>">
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan/Sekolah Terkait</label>
                        <input type="text" class="form-control" name="pekerjaan_atau_sekolah_terkait" value="<?php echo e(old('pekerjaan_atau_sekolah_terkait', $permohonan->pekerjaan_atau_sekolah_terkait)); ?>">
                    </div>
                    <div class="form-group">
                        <label>Alamat Terkait</label>
                        <textarea class="form-control" name="alamat_terkait" rows="3"><?php echo e(old('alamat_terkait', $permohonan->alamat_terkait)); ?></textarea>
                    </div>

                    <hr>
                    <h5 class="font-weight-bold mt-4">Keperluan Surat</h5>
                    <div class="form-group">
                        <label for="keperluan_surat">Tulis ulang atau perbaiki redaksi keperluan surat</label>
                        <textarea class="form-control" name="keperluan_surat" id="keperluan_surat" rows="3" required><?php echo e(old('keperluan_surat', $permohonan->keperluan_surat)); ?></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')"><i class="fas fa-print"></i> Buat Surat Final & Selesaikan</button>
                    <a href="<?php echo e(route('petugas.permohonan-sk-tidak-mampu.show', $permohonan->id)); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                </div>
            </div>
        </div>
    </div>
</form> 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_tidak_mampu/edit_surat.blade.php ENDPATH**/ ?>