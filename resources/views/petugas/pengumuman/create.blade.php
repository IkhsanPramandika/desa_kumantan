@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Buat Pengumuman Baru')

@push('styles')
{{-- CSS untuk CKEditor tidak selalu diperlukan jika menggunakan skin default via CDN --}}
<style>
    .custom-file-upload-container { 
        border: 1px solid #d1d3e2; padding: 8px 15px; text-align: left; cursor: pointer;
        border-radius: .35rem; display: flex; align-items: center;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; min-height: 48px;
    }
    .custom-file-upload-container:hover { border-color: #b9c2d1; }
    .custom-file-upload-container.focus-within, .custom-file-upload-container:focus {
        border-color: #80bdff; outline: 0; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .custom-file-upload-container.drag-over { border-color: #007bff !important; background-color: #e9f7ff; }
    .custom-file-upload-container.is-invalid-custom { border-color: #e74a3b; }
    .invalid-feedback.d-block { display: block !important; }
    .upload-button { flex-shrink: 0; margin-right: 10px; }
    .file-placeholder { color: #6c757d; flex-grow: 1; text-align: right; font-style: italic; }
    .file-name { font-weight: 600; color: #495057; flex-grow: 1; text-align: right; 
                 white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: none; }
    
    /* Style untuk error pada wrapper CKEditor (jika diperlukan, CKEditor biasanya menangani border sendiri) */
    /* Jika textarea.is-invalid, CKEditor mungkin tidak langsung menunjukkan border error */
    .cke_chrome.is-invalid { /* Target wrapper CKEditor jika perlu */
        border: 1px solid #e74a3b !important; 
    }
    textarea.is-invalid + .invalid-feedback { /* Pastikan pesan error di bawah textarea terlihat */
        display: block !important;
    }

    .form-title-custom {
        font-size: 0.9rem; 
        color: #007bff; 
        margin-bottom: 1.5rem;
        font-weight: 500;
    }
    .card-body-custom-padding {
        padding: 2rem; 
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Buat Pengumuman / Berita Desa Baru</h1>

    <div class="card shadow mb-4">
        <div class="card-body card-body-custom-padding">
            <h2 class="form-title-custom">Formulir Pengumuman</h2>

            <form action="{{ route('petugas.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="judul">Judul Pengumuman <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required placeholder="Masukkan judul pengumuman">
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="isi_pengumuman">Isi Pengumuman <span class="text-danger">*</span></label>
                    {{-- Textarea ini akan diinisialisasi oleh CKEditor --}}
                    <textarea class="form-control @error('isi') is-invalid @enderror" id="isi_pengumuman" name="isi" rows="15">{{ old('isi') }}</textarea>
                    @error('isi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="gambar_pengumuman_input">Gambar Pengumuman (Opsional)</label>
                    <div class="custom-file-upload-container @error('gambar_pengumuman') is-invalid-custom @enderror" id="drop_area_gambar_pengumuman">
                        <input type="file" class="d-none" id="gambar_pengumuman_input" name="gambar_pengumuman" accept="image/*">
                        <button type="button" class="btn btn-info btn-sm upload-button" id="upload_button_gambar_pengumuman">Pilih Gambar</button>
                        <span class="file-name" id="gambar_pengumuman_name"></span>
                        <span class="file-placeholder" id="gambar_pengumuman_placeholder">Drag & Drop Gambar (Max 2MB)</span>
                    </div>
                    <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF, SVG. Max 2MB.</small>
                    @error('gambar_pengumuman') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="file_pengumuman_input">File Lampiran (Opsional)</label>
                     <div class="custom-file-upload-container @error('file_pengumuman') is-invalid-custom @enderror" id="drop_area_file_pengumuman">
                        <input type="file" class="d-none" id="file_pengumuman_input" name="file_pengumuman" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                        <button type="button" class="btn btn-info btn-sm upload-button" id="upload_button_file_pengumuman">Pilih File</button>
                        <span class="file-name" id="file_pengumuman_name"></span>
                        <span class="file-placeholder" id="file_pengumuman_placeholder">Drag & Drop File (Max 5MB)</span>
                    </div>
                    <small class="form-text text-muted">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX. Max 5MB.</small>
                    @error('file_pengumuman') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tanggal_publikasi">Tanggal Publikasi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_publikasi') is-invalid @enderror" id="tanggal_publikasi" name="tanggal_publikasi" value="{{ old('tanggal_publikasi', now()->format('Y-m-d')) }}" required>
                        @error('tanggal_publikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status_publikasi">Status Publikasi <span class="text-danger">*</span></label>
                        <select class="form-control @error('status_publikasi') is-invalid @enderror" id="status_publikasi" name="status_publikasi" required>
                            <option value="draft" {{ old('status_publikasi') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="dipublikasikan" {{ old('status_publikasi') == 'dipublikasikan' ? 'selected' : '' }}>Publikasikan</option>
                        </select>
                        @error('status_publikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Pengumuman</button>
                    <a href="{{ route('petugas.pengumuman.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- CKEditor 4 Script dari CDN --}}
<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script> {{-- Anda bisa memilih paket lain seperti 'standard-all' atau 'full' --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi CKEditor pada textarea
        CKEDITOR.replace('isi_pengumuman', {
            height: 300, // Sesuaikan tinggi editor
            // Konfigurasi toolbar bisa disesuaikan di sini
            // Contoh konfigurasi toolbar sederhana:
            toolbarGroups: [
                { name: 'document', groups: [ 'mode', 'doctools', 'document' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                { name: 'forms', groups: [ 'forms' ] },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                { name: 'links', groups: [ 'links' ] },
                { name: 'insert', groups: [ 'insert' ] },
                '/',
                { name: 'styles', groups: [ 'styles' ] },
                { name: 'colors', groups: [ 'colors' ] },
                { name: 'tools', groups: [ 'tools' ] },
                { name: 'others', groups: [ 'others' ] },
                { name: 'about', groups: [ 'about' ] }
            ],
            removeButtons: 'Source,Save,NewPage,ExportPdf,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CopyFormatting,RemoveFormat,CreateDiv,Language,Anchor,Flash,Smiley,PageBreak,Iframe,ShowBlocks,About',
            // Untuk memastikan konten diupdate ke textarea asli untuk validasi Laravel
            // CKEditor secara otomatis mengupdate textarea asli saat form disubmit.
            // Namun, jika Anda melakukan validasi sisi klien atau memerlukan data terbaru secara dinamis:
            on: {
                instanceReady: function(evt) {
                    var editor = evt.editor;
                    // Menangani error validasi dari Laravel
                    const textarea = document.getElementById('isi_pengumuman');
                    if (textarea.classList.contains('is-invalid')) {
                        // Menambahkan style error ke wrapper CKEditor
                        // Wrapper CKEditor biasanya memiliki class cke_chrome
                        let editorWrapper = document.getElementById('cke_isi_pengumuman');
                        if (editorWrapper) {
                            editorWrapper.style.border = '1px solid #e74a3b'; // Warna error Bootstrap
                            editorWrapper.style.borderRadius = '.35rem';
                        }
                    }

                    editor.on('change', function() {
                        editor.updateElement(); // Update textarea asli saat konten berubah
                        // Hapus error jika sudah diisi
                        const textarea = document.getElementById('isi_pengumuman');
                        let editorWrapper = document.getElementById('cke_isi_pengumuman');
                        var errorMessage = $(this).closest('.form-group').find('.invalid-feedback');

                        if (editor.getData().trim() !== '') {
                            if (editorWrapper) {
                                editorWrapper.style.border = ''; // Hapus border error
                            }
                            textarea.classList.remove('is-invalid');
                            if(errorMessage.length > 0) {
                                errorMessage.hide();
                            }
                        }
                    });
                }
            }
        });

        // Inisialisasi custom file uploads
        setupCustomUpload('gambar_pengumuman_input', 'upload_button_gambar_pengumuman', 'gambar_pengumuman_placeholder', 'gambar_pengumuman_name', 'drop_area_gambar_pengumuman');
        setupCustomUpload('file_pengumuman_input', 'upload_button_file_pengumuman', 'file_pengumuman_placeholder', 'file_pengumuman_name', 'drop_area_file_pengumuman');
    });

    // Fungsi setupCustomUpload yang sudah ada
    function setupCustomUpload(inputId, buttonId, placeholderId, nameId, dropAreaId) {
        const fileInput = document.getElementById(inputId);
        const uploadButton = document.getElementById(buttonId);
        const placeholder = document.getElementById(placeholderId);
        const fileNameSpan = document.getElementById(nameId);
        const dropArea = document.getElementById(dropAreaId);

        if (!fileInput || !uploadButton || !placeholder || !fileNameSpan || !dropArea) {
            console.error('Satu atau lebih elemen tidak ditemukan untuk setupCustomUpload dengan inputId:', inputId);
            return; 
        }
        uploadButton.addEventListener('click', () => { fileInput.click(); });
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameSpan.textContent = fileInput.files[0].name;
                fileNameSpan.style.display = 'inline'; 
                placeholder.style.display = 'none';  
            } else {
                fileNameSpan.textContent = ''; 
                fileNameSpan.style.display = 'none';   
                placeholder.style.display = 'inline';
            }
        });
        dropArea.addEventListener('dragover', (event) => {
            event.stopPropagation(); event.preventDefault(); dropArea.classList.add('drag-over'); 
        });
        dropArea.addEventListener('dragleave', (event) => {
            event.stopPropagation(); event.preventDefault(); dropArea.classList.remove('drag-over'); 
        });
        dropArea.addEventListener('drop', (event) => {
            event.stopPropagation(); event.preventDefault(); dropArea.classList.remove('drag-over');
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files; 
                fileNameSpan.textContent = files[0].name;
                fileNameSpan.style.display = 'inline'; placeholder.style.display = 'none';
                const changeEvent = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(changeEvent);
            }
        });
        dropArea.addEventListener('click', (e) => {
            if (e.target !== uploadButton && !uploadButton.contains(e.target)) {
                 fileInput.click();
            }
        });
    }
</script>
@endpush
