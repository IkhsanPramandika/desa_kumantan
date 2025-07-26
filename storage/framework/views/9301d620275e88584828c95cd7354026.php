
 

<?php $__env->startSection('title', 'Buat Surat Permohonan Lainnya'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Buat Surat untuk: <?php echo e($permohonan->masyarakat->nama_lengkap); ?></h1>
    <a href="<?php echo e(route('petugas.permohonan-lainnya.show', $permohonan->id)); ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Detail
    </a>
</div>

<div class="row">
    
    <div class="col-lg-5">
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Permintaan Pemohon</h6>
            </div>
            <div class="card-body">
                <dl>
                    <dt>Judul Permintaan</dt>
                    <dd><?php echo e($permohonan->judul_permohonan); ?></dd>
                    <dt>Keperluan</dt>
                    <dd><?php echo e($permohonan->keperluan); ?></dd>
                    <dt>Rincian dari Pemohon</dt>
                    <dd>
                        <div class="p-3 bg-light border rounded mt-1">
                            <?php echo nl2br(e($permohonan->rincian_pemohon)); ?>

                        </div>
                    </dd>
                </dl>
            </div>
        </div>

        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6>
            </div>
            <div class="card-body">
                <?php
                    $lampiranFiles = !empty($permohonan->lampiran) ? json_decode($permohonan->lampiran, true) : [];
                ?>

                <?php if(!empty($lampiranFiles) && is_array($lampiranFiles)): ?>
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $lampiranFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                                Lampiran <?php echo e($index + 1); ?>

                            </div>
                            <a href="<?php echo e(asset('storage/' . $file)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye fa-sm"></i> Lihat
                            </a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-center my-3">Tidak ada dokumen yang dilampirkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Pembuatan Surat</h6>
            </div>
            <div class="card-body">
                
                <form action="<?php echo e(route('petugas.permohonan-lainnya.generate-surat', $permohonan->id)); ?>" method="POST" id="form-generate-surat">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Nomor Surat</label>
                        <div class="alert alert-info" role="alert">
                            Nomor surat akan dibuat secara otomatis oleh sistem.
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="judul_surat_final" class="font-weight-bold">Judul Dokumen (Akan tampil di bawah KOP)</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['judul_surat_final'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="judul_surat_final" name="judul_surat_final" value="<?php echo e(old('judul_surat_final', 'SURAT KETERANGAN ' . strtoupper($permohonan->judul_permohonan))); ?>" required>
                        <?php $__errorArgs = ['judul_surat_final'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="konten_final_html" class="font-weight-bold">Isi Surat</label>
                        <textarea class="form-control <?php $__errorArgs = ['konten_final_html'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="wysiwyg" name="konten_final_html" rows="20"><?php echo e(old('konten_final_html', '<p>Dengan ini menerangkan bahwa nama yang tersebut di atas adalah benar penduduk Desa Kumantan.</p><p>Surat keterangan ini dibuat sebagai pemenuhan atas permintaan yang bersangkutan untuk keperluan: <strong>' . e($permohonan->keperluan) . '</strong>.</p>')); ?></textarea>
                        <?php $__errorArgs = ['konten_final_html'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50"><i class="fas fa-check"></i></span>
                        <span class="text">Generate Surat & Selesaikan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script src="https://cdn.tiny.cloud/1/3fi9aqma9lmgcqhmpbu9mmo34onbhectbfhqiavjvor03d7o/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: 'textarea#wysiwyg',
            plugins: 'table lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link',
            height: 500,
            menubar: false,
            // [PERBAIKAN] Menambahkan setup untuk memastikan konten tersimpan
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });

        // Menambahkan listener ke form untuk memastikan data tersimpan sebelum submit
        const form = document.getElementById('form-generate-surat');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Perintahkan TinyMCE untuk menyimpan kontennya ke textarea asli
                tinymce.triggerSave();
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/petugas/pengajuan/permohonan_lainnya/create_surat.blade.php ENDPATH**/ ?>