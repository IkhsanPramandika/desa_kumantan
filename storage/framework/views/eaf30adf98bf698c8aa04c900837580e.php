

<?php $__env->startSection('title', 'Detail Permohonan SK Ahli Waris'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan Surat Keterangan Ahli Waris #<?php echo e($permohonan->id); ?></h1>

<?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="alert alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="row">
    
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan Masyarakat</h6></div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Pewaris</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8"><?php echo e($permohonan->nama_pewaris ?? '-'); ?></dd>
                    <dt class="col-sm-4">NIK</dt><dd class="col-sm-8"><?php echo e($permohonan->nik_pewaris ?? '-'); ?></dd>
                    <dt class="col-sm-4">Tempat / Tanggal Lahir</dt><dd class="col-sm-8"><?php echo e($permohonan->tempat_lahir_pewaris ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pewaris)->isoFormat('D MMMM YYYY') : '-'); ?></dd>
                    <dt class="col-sm-4">Tanggal Meninggal</dt><dd class="col-sm-8"><?php echo e($permohonan->tanggal_meninggal_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_meninggal_pewaris)->isoFormat('D MMMM YYYY') : '-'); ?></dd>
                    <dt class="col-sm-4">Alamat Pewaris</dt><dd class="col-sm-8"><?php echo e($permohonan->alamat_pewaris ?? '-'); ?></dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Daftar Ahli Waris</h5>
                
                <?php
                    // Secara manual mengubah JSON string menjadi array PHP
                    $ahliWarisList = is_string($permohonan->daftar_ahli_waris) ? json_decode($permohonan->daftar_ahli_waris, true) : $permohonan->daftar_ahli_waris;
                ?>

                <?php if(!empty($ahliWarisList)): ?>
                    <table class="table table-bordered table-sm mt-3">
                        <thead><tr><th>Nama</th><th>NIK</th><th>Hubungan</th><th>Alamat</th></tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $ahliWarisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ahli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($ahli['nama'] ?? '-'); ?></td>
                                <td><?php echo e($ahli['nik'] ?? '-'); ?></td>
                                <td><?php echo e($ahli['hubungan'] ?? '-'); ?></td>
                                <td><?php echo e($ahli['alamat'] ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p><em>Tidak ada data ahli waris yang diinput.</em></p>
                <?php endif; ?>

                <hr>
                <h5 class="mt-4 font-weight-bold">Catatan dari Pemohon</h5>
                <p><em><?php echo e($permohonan->catatan_pemohon ?? 'Tidak ada catatan.'); ?></em></p>

            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                <?php if($permohonan->status == 'pending'): ?> <span class="badge badge-warning">Pending</span>
                <?php elseif($permohonan->status == 'diterima'): ?> <span class="badge badge-info">Diterima</span>
                <?php elseif($permohonan->status == 'selesai'): ?> <span class="badge badge-success">Selesai</span>
                <?php elseif($permohonan->status == 'ditolak'): ?> <span class="badge badge-danger">Ditolak</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                
                <div class="mb-3">
                    <small class="text-muted d-block">Diajukan Oleh:</small>
                    <strong><?php echo e($permohonan->masyarakat->nama_lengkap ?? 'N/A'); ?></strong><br>
                    <small class="text-muted d-block mt-1">NIK Pemohon:</small>
                    <strong><?php echo e($permohonan->masyarakat->nik ?? 'N/A'); ?></strong>
                </div>
                <hr>

                <?php if($permohonan->status == 'pending'): ?>
                    <p>Periksa lampiran. Jika data yang diajukan sudah benar, klik "Verifikasi".</p>
                    <form action="<?php echo e(route('petugas.permohonan-sk-ahli-waris.verifikasi', $permohonan->id)); ?>" method="POST" class="mb-2">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-block"><i class="fas fa-check"></i> Verifikasi</button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-times"></i> Tolak</button>
                
                <?php elseif($permohonan->status == 'diterima'): ?>
                    <p>Permohonan telah diverifikasi. Klik tombol di bawah untuk memproses dan mengedit data sebelum membuat surat final.</p>
                    <a href="<?php echo e(route('petugas.permohonan-sk-ahli-waris.edit-surat', $permohonan->id)); ?>" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit"></i> Proses & Edit Surat
                    </a>

                <?php elseif($permohonan->status == 'selesai'): ?>
                    <p>Surat telah dibuat. Anda bisa mengunduhnya di bawah ini.</p>
                    <a href="<?php echo e(route('petugas.permohonan-sk-ahli-waris.download-final', $permohonan->id)); ?>" class="btn btn-success btn-block mb-2"><i class="fas fa-download"></i> Unduh Surat</a>
                
                <?php elseif($permohonan->status == 'ditolak'): ?>
                    <p>Permohonan ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"<?php echo e($permohonan->catatan_penolakan); ?>"</em></blockquote>
                <?php endif; ?>
                <a href="<?php echo e(route('petugas.permohonan-sk-ahli-waris.index')); ?>" class="btn btn-secondary btn-block mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                        $lampiran = [
                            'file_ktp_pemohon' => 'KTP Pemohon',
                            'file_kk_pemohon' => 'Kartu Keluarga Pemohon',
                            'file_ktp_ahli_waris' => 'KTP Ahli Waris',
                            'file_kk_ahli_waris' => 'Kartu Keluarga Ahli Waris',
                            'surat_keterangan_kematian' => 'Surat Kematian',
                            'surat_pengantar_rt_rw' => 'Surat Pengantar RT/RW',
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
            <form action="<?php echo e(route('petugas.permohonan-sk-ahli-waris.tolak', $permohonan->id)); ?>" method="POST">
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/sk_ahli_waris/show.blade.php ENDPATH**/ ?>