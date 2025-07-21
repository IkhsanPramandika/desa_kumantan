

<?php $__env->startSection('title', 'Detail & Proses Permohonan KK Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    
    <h1 class="h3 mb-0 text-gray-800">Detail Permohonan #<?php echo e($permohonan->id); ?></h1>
    <a href="<?php echo e(route('petugas.permohonan-kk-baru.index')); ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Daftar
    </a>
</div>



<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo e(session('error')); ?>

    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<?php endif; ?>

<div class="row">
    
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Pemohon</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nama Pemohon</dt>
                    <dd class="col-sm-8 font-weight-bold"><?php echo e($permohonan->masyarakat->nama_lengkap ?? 'N/A'); ?></dd>

                    <dt class="col-sm-4">NIK Pemohon</dt>
                    <dd class="col-sm-8"><?php echo e($permohonan->masyarakat->nik ?? 'N/A'); ?></dd>

                    <dt class="col-sm-4">Tanggal Pengajuan</dt>
                    <dd class="col-sm-8"><?php echo e(\Carbon\Carbon::parse($permohonan->created_at)->isoFormat('D MMMM YYYY, HH:mm')); ?></dd>
                </dl>
                <hr>
                <h6 class="font-weight-bold">Catatan dari Pemohon</h6>
                <p class="text-muted"><em><?php echo e($permohonan->catatan_pemohon ?: 'Tidak ada catatan.'); ?></em></p>
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
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                            <?php echo e($label); ?>

                        </div>
                        <?php if($permohonan->$field): ?>
                            <a href="<?php echo e(asset('storage/' . $permohonan->$field)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye fa-sm"></i> Lihat
                            </a>
                        <?php else: ?>
                            <span class="badge badge-secondary">Tidak Ada</span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                <?php echo $__env->make('layouts.partials.status_badge', ['status' => $permohonan->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div class="card-body">
                
                <?php if($permohonan->status == 'pending'): ?>
                    <p class="text-info"><i class="fas fa-info-circle fa-sm"></i> Periksa semua dokumen lampiran. Jika data sudah valid, klik "Verifikasi". Jika ada yang perlu diperbaiki, klik "Kembalikan untuk Revisi".</p>
                    <hr>
                    <form action="<?php echo e(route('petugas.permohonan-kk-baru.verifikasi', $permohonan->id)); ?>" method="POST" class="mb-2">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data dan lampiran sudah valid?')"><i class="fas fa-check-circle"></i> Verifikasi & Lanjutkan</button>
                    </form>
                    <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-undo"></i> Kembalikan untuk Revisi</button>

                <?php elseif(in_array($permohonan->status, ['diterima', 'diproses'])): ?>
                    <p>Permohonan telah diverifikasi. Unggah file KK baru (PDF) untuk menyelesaikan proses.</p>
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#selesaikanModal"><i class="fas fa-upload"></i> Unggah KK & Selesaikan</button>

                <?php elseif($permohonan->status == 'membutuhkan_revisi'): ?>
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold">Menunggu Revisi dari Pengguna</h6>
                        <p class="mb-0 small">Permohonan telah dikembalikan kepada pengguna untuk diperbaiki. Anda akan menerima notifikasi jika pengguna sudah mengirimkan revisi.</p>
                    </div>
                    <h6 class="font-weight-bold">Catatan Perbaikan:</h6>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                
                <?php elseif($permohonan->status == 'selesai'): ?>
                    <p>Proses telah selesai pada <strong><?php echo e($permohonan->tanggal_selesai_proses ? \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses)->isoFormat('D MMMM YYYY') : 'N/A'); ?></strong>.</p>
                    <a href="<?php echo e(route('petugas.permohonan-kk-baru.download-final', $permohonan->id)); ?>" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh Dokumen Final</a>

                <?php elseif($permohonan->status == 'ditolak'): ?>
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold">Permohonan Ditolak</h6>
                    </div>
                    <h6 class="font-weight-bold">Alasan Penolakan:</h6>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                <?php endif; ?>
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
                    <h5 class="modal-title" id="tolakModalLabel">Kembalikan Permohonan untuk Revisi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan"><strong>Tulis Catatan Perbaikan (Wajib):</strong></label>
                        <textarea class="form-control" name="catatan_penolakan" rows="4" required placeholder="Contoh: Scan KTP buram, mohon unggah ulang dengan jelas."></textarea>
                        <small class="form-text text-muted">Catatan ini akan ditampilkan kepada pengguna.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Ya, Kembalikan</button>
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