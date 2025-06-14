

<?php $__env->startSection('title', 'Detail Permohonan SK Tidak Mampu'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan Surat Keterangan Tidak Mampu #<?php echo e($permohonan->id); ?></h1>

<?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="alert alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="row">
    
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan Masyarakat</h6></div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Pemohon</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_pemohon ?? '-'); ?></dd>
                    <dt class="col-sm-4">NIK</dt><dd class="col-sm-8"><?php echo e($permohonan->nik_pemohon ?? '-'); ?></dd>
                    <dt class="col-sm-4">Tempat, Tgl Lahir</dt><dd class="col-sm-8"><?php echo e($permohonan->tempat_lahir_pemohon ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_pemohon ? $permohonan->tanggal_lahir_pemohon->format('d F Y') : '-'); ?></dd>
                    <dt class="col-sm-4">Pekerjaan</dt><dd class="col-sm-8"><?php echo e($permohonan->pekerjaan_pemohon ?? '-'); ?></dd>
                    <dt class="col-sm-4">Alamat</dt><dd class="col-sm-8"><?php echo e($permohonan->alamat_pemohon ?? '-'); ?></dd>
                </dl>
                
                <?php if($permohonan->nama_terkait): ?>
                <hr>
                <h5 class="font-weight-bold mt-4">Data Anak/Orang Tua Terkait</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_terkait ?? '-'); ?></dd>
                    <dt class="col-sm-4">Pekerjaan/Sekolah</dt><dd class="col-sm-8"><?php echo e($permohonan->pekerjaan_atau_sekolah_terkait ?? '-'); ?></dd>
                    <dt class="col-sm-4">Alamat</dt><dd class="col-sm-8"><?php echo e($permohonan->alamat_terkait ?? '-'); ?></dd>
                </dl>
                <?php endif; ?>
                
                <hr>
                <h5 class="font-weight-bold mt-4">Keperluan & Catatan</h5>
                <dl class="row">
                    <dt class="col-sm-4">Keperluan Surat</dt><dd class="col-sm-8"><?php echo e($permohonan->keperluan_surat ?? '-'); ?></dd>
                    <dt class="col-sm-4">Catatan Pemohon</dt><dd class="col-sm-8"><em><?php echo e($permohonan->catatan_pemohon ?? 'Tidak ada catatan.'); ?></em></dd>
                </dl>
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                <?php if($permohonan->status == 'pending'): ?> <span class="badge badge-warning">Pending</span>
                <?php elseif(in_array($permohonan->status, ['diterima', 'diproses'])): ?> <span class="badge badge-info"><?php echo e(ucfirst($permohonan->status)); ?></span>
                <?php elseif($permohonan->status == 'selesai'): ?> <span class="badge badge-success">Selesai</span>
                <?php elseif($permohonan->status == 'ditolak'): ?> <span class="badge badge-danger">Ditolak</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($permohonan->status == 'pending'): ?>
                    <p>Periksa lampiran. Jika data valid, klik tombol di bawah untuk memverifikasi dan membuat surat.</p>
                    <form action="<?php echo e(route('petugas.permohonan-sk-tidak-mampu.verifikasi', $permohonan->id)); ?>" method="POST" class="mb-2">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data valid dan ingin langsung membuat surat?')"><i class="fas fa-check"></i> Verifikasi & Buat Surat</button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-times"></i> Tolak</button>
                
                <?php elseif($permohonan->status == 'selesai'): ?>
                    <p>Surat telah dibuat. Anda bisa mengunduhnya atau membagikan link publik.</p>
                    <a href="<?php echo e(route('petugas.permohonan-sk-tidak-mampu.download-final', $permohonan->id)); ?>" class="btn btn-success btn-block mb-2"><i class="fas fa-download"></i> Unduh Surat (Petugas)</a>
                    <div class="form-group">
                        <label>Link Download Publik:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?php echo e(route('public.download.sk-tidak-mampu', $permohonan->id)); ?>" readonly id="publicLink">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">Salin</button>
                            </div>
                        </div>
                    </div>
                
                <?php elseif($permohonan->status == 'ditolak'): ?>
                    <p>Permohonan ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                <?php endif; ?>
                
                <a href="<?php echo e(route('petugas.permohonan-sk-tidak-mampu.index')); ?>" class="btn btn-secondary btn-block mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
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
                            'file_pendukung_lain' => 'File Pendukung Lainnya'
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
            <form action="<?php echo e(route('petugas.permohonan-sk-tidak-mampu.tolak', $permohonan->id)); ?>" method="POST">
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

<?php $__env->startPush('scripts'); ?>
<script>
function copyLink() {
  var copyText = document.getElementById("publicLink");
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */
  document.execCommand("copy");
  alert("Link berhasil disalin!");
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_tidak_mampu/show.blade.php ENDPATH**/ ?>