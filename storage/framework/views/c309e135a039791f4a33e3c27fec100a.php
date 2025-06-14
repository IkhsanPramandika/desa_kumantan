

<?php $__env->startSection('title', 'Detail & Proses Permohonan KK Baru'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan KK Baru #<?php echo e($permohonan->id); ?></h1>


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
                <p class="text-info"><i class="fas fa-info-circle"></i> Verifikasi data di bawah ini berdasarkan dokumen lampiran yang tersedia di panel kanan.</p>
                <hr>

                
                <dl class="row">
                    <dt class="col-sm-4">Nama Pemohon</dt>
                    <dd class="col-sm-8"><?php echo e($permohonan->masyarakat->nama_lengkap ?? 'N/A'); ?></dd>

                    <dt class="col-sm-4">NIK Pemohon</dt>
                    <dd class="col-sm-8"><?php echo e($permohonan->masyarakat->nik ?? 'N/A'); ?></dd>

                    <dt class="col-sm-4">Tanggal Pengajuan</dt>
                    <dd class="col-sm-8"><?php echo e($permohonan->created_at->format('d F Y, H:i')); ?></dd>
                </dl>
                
                <hr>
                <h5 class="mt-4">Catatan dari Pemohon</h5>
                <p><em><?php echo e($permohonan->catatan_pemohon ?? 'Tidak ada catatan.'); ?></em></p>
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                <?php if($permohonan->status == 'pending'): ?> <span class="badge badge-warning">Pending</span>
                <?php elseif($permohonan->status == 'diterima' || $permohonan->status == 'diproses'): ?> <span class="badge badge-info"><?php echo e(ucfirst($permohonan->status)); ?></span>
                <?php elseif($permohonan->status == 'selesai'): ?> <span class="badge badge-success">Selesai</span>
                <?php elseif($permohonan->status == 'ditolak'): ?> <span class="badge badge-danger">Ditolak</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($permohonan->status == 'pending'): ?>
                    <p>Periksa lampiran. Jika valid, klik "Verifikasi". Jika tidak, klik "Tolak".</p>
                    <form action="<?php echo e(route('petugas.permohonan-kk-baru.verifikasi', $permohonan->id)); ?>" method="POST" class="mb-2">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data valid?')"><i class="fas fa-check"></i> Verifikasi</button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-times"></i> Tolak</button>

                <?php elseif(in_array($permohonan->status, ['diterima', 'diproses'])): ?>
                    <p>Permohonan telah diverifikasi. Unggah file KK final untuk menyelesaikan proses.</p>
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#selesaikanModal"><i class="fas fa-upload"></i> Unggah KK Final & Selesaikan</button>

                <?php elseif($permohonan->status == 'selesai'): ?>
                    <p>Proses telah selesai pada <?php echo e($permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->format('d F Y') : 'N/A'); ?>.</p>
                    <a href="<?php echo e(route('petugas.permohonan-kk-baru.download-final', $permohonan->id)); ?>" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh KK Final</a>

                <?php elseif($permohonan->status == 'ditolak'): ?>
                    <p>Permohonan ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                <?php endif; ?>
                
                <a href="<?php echo e(route('petugas.permohonan-kk-baru.index')); ?>" class="btn btn-secondary btn-block mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
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
                            'file_kk' => 'File Kartu Keluarga',
                            'file_ktp' => 'File KTP',
                            'surat_pengantar_rt_rw' => 'Surat Pengantar RT/RW',
                            'buku_nikah_akta_cerai' => 'Buku Nikah / Akta Cerai',
                            'surat_pindah_datang' => 'Surat Pindah Datang',
                            'ijazah_terakhir' => 'Ijazah Terakhir',
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


<div class="modal fade" id="tolakModal" tabindex="-1" role="dialog" aria-labelledby="tolakModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('petugas.permohonan-kk-baru.tolak', $permohonan->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="tolakModalLabel">Tolak Permohonan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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


<div class="modal fade" id="selesaikanModal" tabindex="-1" role="dialog" aria-labelledby="selesaikanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('petugas.permohonan-kk-baru.selesaikan', $permohonan->id)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="selesaikanModalLabel">Unggah KK Final</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file_hasil_akhir">Pilih File PDF KK Final:</label>
                        <input type="file" class="form-control-file" name="file_hasil_akhir" accept="application/pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Unggah & Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/kk_baru/show.blade.php ENDPATH**/ ?>