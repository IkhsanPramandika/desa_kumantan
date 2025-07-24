{{-- 
    File ini bisa disimpan sebagai partial, contoh: 
    /resources/views/petugas/pengumuman/partials/_form.blade.php 
--}}

@php
    // [PERBAIKAN] Logika ini lebih andal untuk menentukan mode 'edit'.
    // Ini hanya akan true jika variabel $pengumuman ada DAN sudah tersimpan di database.
    $isEdit = isset($pengumuman) && $pengumuman->exists;
@endphp

{{-- Form action dan method akan berubah secara dinamis --}}
<form action="{{ $isEdit ? route('petugas.pengumuman.update', $pengumuman->id) : route('petugas.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- Tambahkan method PUT hanya jika sedang dalam mode edit --}}
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- Bagian untuk menampilkan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <p><strong>Harap perbaiki error berikut:</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            {{-- Field Judul --}}
            <div class="form-group">
                <label for="judul" class="font-weight-bold">Judul Pengumuman <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $pengumuman->judul ?? '') }}" required placeholder="Masukkan judul pengumuman">
                @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Field Isi Pengumuman --}}
            <div class="form-group">
                <label for="isi" class="font-weight-bold">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea class="form-control @error('isi') is-invalid @enderror" id="wysiwyg" name="isi" rows="15">{{ old('isi', $pengumuman->isi ?? '') }}</textarea>
                @error('isi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    {{-- Field Tanggal Publikasi --}}
                    <div class="form-group">
                        <label for="tanggal_publikasi" class="font-weight-bold">Tanggal Publikasi <span class="text-danger">*</span></label>
                        {{-- [PERBAIKAN] Menggunakan $isEdit untuk konsistensi --}}
                        <input type="date" class="form-control @error('tanggal_publikasi') is-invalid @enderror" id="tanggal_publikasi" name="tanggal_publikasi" value="{{ old('tanggal_publikasi', $isEdit ? $pengumuman->tanggal_publikasi->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                        @error('tanggal_publikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Field Status Publikasi --}}
                    <div class="form-group">
                        <label for="status_publikasi" class="font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select class="form-control @error('status_publikasi') is-invalid @enderror" id="status_publikasi" name="status_publikasi" required>
                            <option value="dipublikasikan" {{ old('status_publikasi', $pengumuman->status_publikasi ?? '') == 'dipublikasikan' ? 'selected' : '' }}>Dipublikasikan</option>
                            <option value="draft" {{ old('status_publikasi', $pengumuman->status_publikasi ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                        @error('status_publikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Field Upload Gambar --}}
                    <div class="form-group">
                        <label for="gambar_pengumuman" class="font-weight-bold">Gambar Unggulan</label>
                        <input type="file" class="form-control-file @error('gambar_pengumuman') is-invalid @enderror" id="gambar_pengumuman" name="gambar_pengumuman">
                        <small class="form-text text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                        @error('gambar_pengumuman') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        
                        @if ($isEdit && $pengumuman->gambar_pengumuman)
                            <div class="mt-2">
                                <p>Gambar saat ini:</p>
                                <img src="{{ Storage::url($pengumuman->gambar_pengumuman) }}" alt="Gambar Pengumuman" class="img-fluid rounded">
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="hapus_gambar_pengumuman" name="hapus_gambar_pengumuman" value="1">
                                    <label class="custom-control-label" for="hapus_gambar_pengumuman">Hapus gambar saat ini</label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer text-right">
                    {{-- Tombol Submit --}}
                    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Perbarui' : 'Simpan' }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Script untuk editor teks TinyMCE --}}
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
