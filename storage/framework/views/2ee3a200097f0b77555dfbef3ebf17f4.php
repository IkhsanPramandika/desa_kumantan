

<?php $__env->startSection('title', 'Detail Permohonan Lainnya'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan Lainnya #<?php echo e($permohonan->id); ?></h1>

<?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="alert alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="row">
    
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data Permohonan dari Masyarakat</h6></div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nama Pemohon</dt><dd class="col-sm-8"><?php echo e($permohonan->masyarakat->nama_lengkap ?? '-'); ?></dd>
                    <dt class="col-sm-4">NIK Pemohon</dt><dd class="col-sm-8"><?php echo e($permohonan->masyarakat->nik ?? '-'); ?></dd>
                </dl>
                <hr>
                <dl>
                    <dt>Judul Permohonan</dt>
                    <dd><?php echo e($permohonan->judul_permohonan ?? '-'); ?></dd>

                    <dt>Keperluan</dt>
                    <dd><?php echo e($permohonan->keperluan ?? '-'); ?></dd>

                    <dt>Rincian Lengkap dari Pemohon</dt>
                    <dd>
                        <div class="p-3 bg-light border rounded mt-1">
                            <?php echo nl2br(e($permohonan->rincian_pemohon ?? 'Tidak ada rincian.')); ?>

                        </div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                <?php if($permohonan->status == 'pending'): ?> <span class="badge badge-warning">Pending</span>
                <?php elseif($permohonan->status == 'selesai'): ?> <span class="badge badge-success">Selesai</span>
                <?php elseif($permohonan->status == 'ditolak'): ?> <span class="badge badge-danger">Ditolak</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($permohonan->status == 'pending'): ?>
                    <p>Periksa rincian permohonan. Jika data valid dan bisa diproses, klik "Buat Surat" untuk melanjutkan ke halaman penulisan surat.</p>
                    <a href="<?php echo e(route('petugas.permohonan-lainnya.create-surat', $permohonan->id)); ?>" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-pen-alt"></i> Buat Surat
                    </a>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-times"></i> Tolak Permohonan</button>
                
                <?php elseif($permohonan->status == 'selesai'): ?>
                    <p>Surat telah dibuat pada <?php echo e($permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->format('d F Y, H:i') : ''); ?>.</p>
                    <a href="<?php echo e(route('petugas.permohonan-lainnya.download-final', $permohonan->id)); ?>" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh Surat</a>

                <?php elseif($permohonan->status == 'ditolak'): ?>
                    <p>Permohonan ini telah ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                <?php endif; ?>
                
                <a href="<?php echo e(route('petugas.permohonan-lainnya.index')); ?>" class="btn btn-secondary btn-block mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Lampiran dari Pemohon
                        <?php if($permohonan->lampiran): ?>
                            <a href="<?php echo e(asset('storage/' . $permohonan->lampiran)); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                        <?php else: ?>
                            <span class="badge badge-secondary">Tidak Ada</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="tolakModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('petugas.permohonan-lainnya.tolak', $permohonan->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header"><h5 class="modal-title">Tolak Permohonan</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan">Alasan Penolakan:</label>
                        <textarea class="form-control" name="catatan_penolakan" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Ya, Tolak</button></div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/permohonan_lainnya/show.blade.php ENDPATH**/ ?>