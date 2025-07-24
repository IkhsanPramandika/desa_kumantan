

<?php
    // [PERBAIKAN] Logika ini lebih andal untuk menentukan mode 'edit'.
    // Ini hanya akan true jika variabel $pengumuman ada DAN sudah tersimpan di database.
    $isEdit = isset($pengumuman) && $pengumuman->exists;
?>


<form action="<?php echo e($isEdit ? route('petugas.pengumuman.update', $pengumuman->id) : route('petugas.pengumuman.store')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    
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

    <div class="row">
        <div class="col-md-8">
            
            <div class="form-group">
                <label for="judul" class="font-weight-bold">Judul Pengumuman <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?php $__errorArgs = ['judul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="judul" name="judul" value="<?php echo e(old('judul', $pengumuman->judul ?? '')); ?>" required placeholder="Masukkan judul pengumuman">
                <?php $__errorArgs = ['judul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="form-group">
                <label for="isi" class="font-weight-bold">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea class="form-control <?php $__errorArgs = ['isi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="wysiwyg" name="isi" rows="15"><?php echo e(old('isi', $pengumuman->isi ?? '')); ?></textarea>
                <?php $__errorArgs = ['isi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    
                    <div class="form-group">
                        <label for="tanggal_publikasi" class="font-weight-bold">Tanggal Publikasi <span class="text-danger">*</span></label>
                        
                        <input type="date" class="form-control <?php $__errorArgs = ['tanggal_publikasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="tanggal_publikasi" name="tanggal_publikasi" value="<?php echo e(old('tanggal_publikasi', $isEdit ? $pengumuman->tanggal_publikasi->format('Y-m-d') : now()->format('Y-m-d'))); ?>" required>
                        <?php $__errorArgs = ['tanggal_publikasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="form-group">
                        <label for="status_publikasi" class="font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select class="form-control <?php $__errorArgs = ['status_publikasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status_publikasi" name="status_publikasi" required>
                            <option value="dipublikasikan" <?php echo e(old('status_publikasi', $pengumuman->status_publikasi ?? '') == 'dipublikasikan' ? 'selected' : ''); ?>>Dipublikasikan</option>
                            <option value="draft" <?php echo e(old('status_publikasi', $pengumuman->status_publikasi ?? '') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                        </select>
                        <?php $__errorArgs = ['status_publikasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="form-group">
                        <label for="gambar_pengumuman" class="font-weight-bold">Gambar Unggulan</label>
                        <input type="file" class="form-control-file <?php $__errorArgs = ['gambar_pengumuman'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="gambar_pengumuman" name="gambar_pengumuman">
                        <small class="form-text text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                        <?php $__errorArgs = ['gambar_pengumuman'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <?php if($isEdit && $pengumuman->gambar_pengumuman): ?>
                            <div class="mt-2">
                                <p>Gambar saat ini:</p>
                                <img src="<?php echo e(Storage::url($pengumuman->gambar_pengumuman)); ?>" alt="Gambar Pengumuman" class="img-fluid rounded">
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="hapus_gambar_pengumuman" name="hapus_gambar_pengumuman" value="1">
                                    <label class="custom-control-label" for="hapus_gambar_pengumuman">Hapus gambar saat ini</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer text-right">
                    
                    <button type="submit" class="btn btn-primary"><?php echo e($isEdit ? 'Perbarui' : 'Simpan'); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>


<script src="https://cdn.tiny.cloud/1/3fi9aqma9lmgcqhmpbu9mmo34onbhectbfhqiavjvor03d7o/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: 'textarea#wysiwyg',
            plugins: 'table lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link',
            height: 500,
            menubar: false,
        });
    });
</script>
<?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/layouts/partials/form-fields.blade.php ENDPATH**/ ?>