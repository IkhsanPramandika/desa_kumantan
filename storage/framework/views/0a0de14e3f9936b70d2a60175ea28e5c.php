

<?php $__env->startSection('title', 'Detail & Proses Permohonan SK Kelahiran'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan #<?php echo e($permohonan->id); ?></h1>


<?php if(session('success')): ?>
<div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-danger"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="row">
    
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan oleh Masyarakat</h6>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Anak</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama Anak</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_anak ?? '-'); ?></dd>
                    <dt class="col-sm-4">Jenis Kelamin</dt><dd class="col-sm-8"><?php echo e($permohonan->jenis_kelamin_anak ?? '-'); ?></dd>
                    <dt class="col-sm-4">Tempat, Tgl Lahir</dt><dd class="col-sm-8"><?php echo e($permohonan->tempat_lahir_anak ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_anak ? $permohonan->tanggal_lahir_anak->format('d F Y') : '-'); ?></dd>
                    <dt class="col-sm-4">Agama</dt><dd class="col-sm-8"><?php echo e($permohonan->agama_anak ?? '-'); ?></dd>
                    <dt class="col-sm-4">Alamat Anak</dt><dd class="col-sm-8"><?php echo e($permohonan->alamat_anak ?? '-'); ?></dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Data Orang Tua</h5>
                <dl class="row mt-3">
                    <dt class="col-sm-4">Nama Ayah</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_ayah ?? '-'); ?></dd>
                    <dt class="col-sm-4">NIK Ayah</dt><dd class="col-sm-8"><?php echo e($permohonan->nik_ayah ?? '-'); ?></dd>
                    <dt class="col-sm-4">Nama Ibu</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_ibu ?? '-'); ?></dd>
                    <dt class="col-sm-4">NIK Ibu</dt><dd class="col-sm-8"><?php echo e($permohonan->nik_ibu ?? '-'); ?></dd>
                </dl>
                
                
                <hr>
                <h5 class="mt-4 font-weight-bold">Catatan dari Pemohon</h5>
                <p><em><?php echo e($permohonan->catatan_pemohon ?? 'Tidak ada catatan.'); ?></em></p>
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                <?php if($permohonan->status == 'pending'): ?> <span class="badge badge-warning">Pending</span>
                <?php elseif(in_array($permohonan->status, ['diterima', 'diproses'])): ?> <span class="badge badge-info"><?php echo e(ucfirst($permohonan->status)); ?></span>
                <?php elseif($permohonan->status == 'selesai'): ?> <span class="badge badge-success">Selesai</span>
                <?php elseif($permohonan->status == 'ditolak'): ?> <span class="badge badge-danger">Ditolak</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($permohonan->status == 'pending'): ?>
                    <p>Periksa dokumen lampiran. Jika valid, klik "Verifikasi" untuk melanjutkan.</p>
                    <form action="<?php echo e(route('petugas.permohonan-sk-kelahiran.verifikasi', $permohonan->id)); ?>" method="POST" class="mb-2">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data valid?')">
                            <i class="fas fa-check"></i> Verifikasi Permohonan
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                
                
                 <?php elseif($permohonan->status == 'diterima'): ?>
                <p>Permohonan telah diverifikasi. Klik tombol di bawah untuk memproses dan mengedit data sebelum membuat surat final.</p>
                <a href="<?php echo e(route('petugas.permohonan-sk-kelahiran.edit-surat', $permohonan->id)); ?>" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-edit"></i> Proses & Edit Surat
                </a>
                  
                
                <?php elseif($permohonan->status == 'selesai'): ?>
                    <p>Surat telah dibuat pada <?php echo e($permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->format('d F Y, H:i') : ''); ?>.</p>
                    <a href="<?php echo e(route('petugas.permohonan-sk-kelahiran.download-final', $permohonan->id)); ?>" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh Surat</a>

                <?php elseif($permohonan->status == 'ditolak'): ?>
                    <p>Permohonan ini telah ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                <?php endif; ?>
                
                <a href="<?php echo e(route('petugas.permohonan-sk-kelahiran.index')); ?>" class="btn btn-secondary btn-block mt-3">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                        $lampiran = [
                            'file_kk' => 'Kartu Keluarga',
                            'file_ktp' => 'KTP Orang Tua',
                            'surat_pengantar_rt_rw' => 'Surat Pengantar RT/RW',
                            'surat_nikah_orangtua' => 'Buku Nikah Orang Tua',
                            'surat_keterangan_kelahiran' => 'Surat Keterangan Kelahiran Bidan/RS'
                        ];
                    ?>
                    <?php $__currentLoopData = $lampiran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo e($label); ?>

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
</div>


<div class="modal fade" id="tolakModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('petugas.permohonan-sk-kelahiran.tolak', $permohonan->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Permohonan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan">Alasan Penolakan:</label>
                        <textarea class="form-control" name="catatan_penolakan" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_kelahiran/show.blade.php ENDPATH**/ ?>