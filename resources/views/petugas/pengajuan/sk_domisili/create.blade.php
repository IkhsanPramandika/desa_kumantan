@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Buat Permohonan SK Domisili')

@push('styles')
<style>
    .custom-file-upload-container {
        border: 1px solid #d1d3e2;
        padding: 8px 15px;
        text-align: left;
        cursor: pointer;
        border-radius: .35rem;
        display: flex;
        align-items: center;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        min-height: 48px; /* Cukup untuk tombol dan teks */
    }
    .custom-file-upload-container:hover {
        border-color: #b9c2d1; /* Warna hover standar Bootstrap */
    }
    .custom-file-upload-container.focus-within,
    .custom-file-upload-container:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .custom-file-upload-container.drag-over {
        border-color: #007bff !important;
        background-color: #e9f7ff;
    }
    .custom-file-upload-container.is-invalid-custom {
        border-color: #e74a3b; /* Warna error Bootstrap */
    }
    .invalid-feedback.d-block {
        display: block !important;
    }
    .upload-button { /* Styling untuk tombol di dalam container */
        flex-shrink: 0; /* Tombol tidak mengecil */
        margin-right: 10px; /* Jarak antara tombol dan teks placeholder/nama file */
    }
    .file-placeholder {
        color: #6c757d; /* Warna placeholder Bootstrap */
        flex-grow: 1;   /* Mengisi sisa ruang */
        text-align: right; /* Teks placeholder rata kanan */
        font-style: italic; /* Membuat placeholder sedikit berbeda */
    }
    .file-name {
        font-weight: 600; /* Nama file sedikit tebal */
        color: #495057; /* Warna teks Bootstrap standar */
        flex-grow: 1;   /* Mengisi sisa ruang */
        text-align: right; /* Nama file juga rata kanan menggantikan placeholder */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: none; /* Sembunyikan nama file secara default */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Permohonan Surat Keterangan Domisili</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Isi Data Permohonan SK Domisili</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('permohonan-sk-domisili.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Tipe Pemohon dan Data Pemohon/Lembaga --}}
                <div class="form-group">
                    <label for="tipe_pemohon">Tipe Pemohon <span class="text-danger">*</span></label>
                    <select class="form-control @error('tipe_pemohon') is-invalid @enderror" id="tipe_pemohon" name="tipe_pemohon" required onchange="togglePemohonFields()">
                        <option value="">Pilih Tipe Pemohon</option>
                        <option value="perorangan" {{ old('tipe_pemohon') == 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                        <option value="lembaga" {{ old('tipe_pemohon') == 'lembaga' ? 'selected' : '' }}>Lembaga/Organisasi/Usaha</option>
                    </select>
                    @error('tipe_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="nama_pemohon_atau_lembaga">Nama Lengkap Pemohon / Nama Lembaga <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_pemohon_atau_lembaga') is-invalid @enderror" id="nama_pemohon_atau_lembaga" name="nama_pemohon_atau_lembaga" value="{{ old('nama_pemohon_atau_lembaga') }}" required>
                    @error('nama_pemohon_atau_lembaga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div id="perorangan_fields" style="display: {{ old('tipe_pemohon') == 'perorangan' ? 'block' : 'none' }};">
                    <div class="form-group">
                        <label for="nik_pemohon">NIK Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nik_pemohon') is-invalid @enderror" id="nik_pemohon" name="nik_pemohon" value="{{ old('nik_pemohon') }}">
                        @error('nik_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin_pemohon">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-control @error('jenis_kelamin_pemohon') is-invalid @enderror" id="jenis_kelamin_pemohon" name="jenis_kelamin_pemohon">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="LAKI-LAKI" {{ old('jenis_kelamin_pemohon') == 'LAKI-LAKI' ? 'selected' : '' }}>LAKI-LAKI</option>
                            <option value="PEREMPUAN" {{ old('jenis_kelamin_pemohon') == 'PEREMPUAN' ? 'selected' : '' }}>PEREMPUAN</option>
                        </select>
                        @error('jenis_kelamin_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tempat_lahir_pemohon">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('tempat_lahir_pemohon') is-invalid @enderror" id="tempat_lahir_pemohon" name="tempat_lahir_pemohon" value="{{ old('tempat_lahir_pemohon') }}">
                            @error('tempat_lahir_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal_lahir_pemohon">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_lahir_pemohon') is-invalid @enderror" id="tanggal_lahir_pemohon" name="tanggal_lahir_pemohon" value="{{ old('tanggal_lahir_pemohon') }}">
                            @error('tanggal_lahir_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pekerjaan_pemohon">Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('pekerjaan_pemohon') is-invalid @enderror" id="pekerjaan_pemohon" name="pekerjaan_pemohon" value="{{ old('pekerjaan_pemohon') }}">
                        @error('pekerjaan_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr>
                <h5 class="mb-3 mt-4 text-gray-800">Detail Domisili</h5>
                <div class="form-group">
                    <label for="alamat_lengkap_domisili">Alamat Lengkap Domisili (Sesuai KTP/Surat Lainnya) <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('alamat_lengkap_domisili') is-invalid @enderror" id="alamat_lengkap_domisili" name="alamat_lengkap_domisili" rows="3" required>{{ old('alamat_lengkap_domisili') }}</textarea>
                    @error('alamat_lengkap_domisili') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="rt_domisili">RT</label>
                        <input type="text" class="form-control @error('rt_domisili') is-invalid @enderror" id="rt_domisili" name="rt_domisili" value="{{ old('rt_domisili') }}" placeholder="Contoh: 001">
                        @error('rt_domisili') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="rw_domisili">RW</label>
                        <input type="text" class="form-control @error('rw_domisili') is-invalid @enderror" id="rw_domisili" name="rw_domisili" value="{{ old('rw_domisili') }}" placeholder="Contoh: 001">
                        @error('rw_domisili') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dusun_domisili">Dusun/Lingkungan</label>
                        <input type="text" class="form-control @error('dusun_domisili') is-invalid @enderror" id="dusun_domisili" name="dusun_domisili" value="{{ old('dusun_domisili') }}">
                        @error('dusun_domisili') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="keperluan_domisili">Keperluan Pembuatan Surat <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('keperluan_domisili') is-invalid @enderror" id="keperluan_domisili" name="keperluan_domisili" rows="3" required>{{ old('keperluan_domisili') }}</textarea>
                    @error('keperluan_domisili') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr>
                <h5 class="mb-3 mt-4 text-gray-800">Dokumen Pendukung</h5>
                
                {{-- Surat Pengantar RT/RW --}}
                <div class="form-group">
                    <label for="file_surat_pengantar_rt_rw_input">File Surat Pengantar RT/RW <span class="text-danger">*</span></label>
                    <div class="custom-file-upload-container @error('file_surat_pengantar_rt_rw') is-invalid-custom @enderror" id="drop_area_surat_pengantar_rt_rw">
                        <input type="file" class="d-none" id="file_surat_pengantar_rt_rw_input" name="file_surat_pengantar_rt_rw" accept=".pdf,.jpg,.jpeg,.png" required>
                        <button type="button" class="btn btn-primary btn-sm upload-button" id="upload_button_surat_pengantar_rt_rw">Upload</button>
                        <span class="file-name" id="file_surat_pengantar_rt_rw_name"></span>
                        <span class="file-placeholder" id="file_surat_pengantar_rt_rw_placeholder">Drag & Drop Files</span>
                    </div>
                    <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max 2MB.</small>
                    @error('file_surat_pengantar_rt_rw')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- File KK --}}
                <div class="form-group">
                    <label for="file_kk_input">File Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                    <div class="custom-file-upload-container @error('file_kk') is-invalid-custom @enderror" id="drop_area_kk">
                        <input type="file" class="d-none" id="file_kk_input" name="file_kk" accept=".pdf,.jpg,.jpeg,.png" required>
                        <button type="button" class="btn btn-primary btn-sm upload-button" id="upload_button_kk">Upload</button>
                        <span class="file-name" id="file_kk_name"></span>
                        <span class="file-placeholder" id="file_kk_placeholder">Drag & Drop Files</span>
                    </div>
                    <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max 2MB.</small>
                    @error('file_kk')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- File KTP --}}
                <div class="form-group">
                    <label for="file_ktp_input">File KTP Pemohon <span class="text-danger">*</span></label>
                    <div class="custom-file-upload-container @error('file_ktp') is-invalid-custom @enderror" id="drop_area_ktp">
                        <input type="file" class="d-none" id="file_ktp_input" name="file_ktp" accept=".pdf,.jpg,.jpeg,.png" required>
                        <button type="button" class="btn btn-primary btn-sm upload-button" id="upload_button_ktp">Upload</button>
                        <span class="file-name" id="file_ktp_name"></span>
                        <span class="file-placeholder" id="file_ktp_placeholder">Drag & Drop Files</span>
                    </div>
                    <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max 2MB.</small>
                    @error('file_ktp')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="catatan_internal">Catatan Tambahan Pemohon (Opsional)</label>
                    <textarea class="form-control @error('catatan_internal') is-invalid @enderror" id="catatan_internal" name="catatan_internal" rows="3">{{ old('catatan_internal') }}</textarea>
                    @error('catatan_internal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Ajukan Permohonan</button>
                <a href="{{ route('permohonan-sk-domisili.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setupCustomUpload(inputId, buttonId, placeholderId, nameId, dropAreaId) {
        const fileInput = document.getElementById(inputId);
        const uploadButton = document.getElementById(buttonId);
        const placeholder = document.getElementById(placeholderId);
        const fileNameSpan = document.getElementById(nameId);
        const dropArea = document.getElementById(dropAreaId);

        if (!fileInput || !uploadButton || !placeholder || !fileNameSpan || !dropArea) {
            console.error('One or more elements not found for setupCustomUpload with inputId:', inputId);
            return; // Keluar dari fungsi jika ada elemen yang hilang
        }

        uploadButton.addEventListener('click', () => {
            fileInput.click(); 
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameSpan.textContent = fileInput.files[0].name;
                fileNameSpan.style.display = 'inline'; 
                placeholder.style.display = 'none';  
            } else {
                fileNameSpan.textContent = ''; // Kosongkan nama file jika tidak ada file
                fileNameSpan.style.display = 'none';   
                placeholder.style.display = 'inline';
            }
        });

        dropArea.addEventListener('dragover', (event) => {
            event.stopPropagation();
            event.preventDefault();
            dropArea.classList.add('drag-over'); 
        });

        dropArea.addEventListener('dragleave', (event) => {
            event.stopPropagation();
            event.preventDefault();
            dropArea.classList.remove('drag-over'); 
        });

        dropArea.addEventListener('drop', (event) => {
            event.stopPropagation();
            event.preventDefault();
            dropArea.classList.remove('drag-over');
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files; 
                fileNameSpan.textContent = files[0].name;
                fileNameSpan.style.display = 'inline';
                placeholder.style.display = 'none';
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

    document.addEventListener('DOMContentLoaded', () => {
        togglePemohonFields(); 

        setupCustomUpload('file_surat_pengantar_rt_rw_input', 'upload_button_surat_pengantar_rt_rw', 'file_surat_pengantar_rt_rw_placeholder', 'file_surat_pengantar_rt_rw_name', 'drop_area_surat_pengantar_rt_rw');
        setupCustomUpload('file_kk_input', 'upload_button_kk', 'file_kk_placeholder', 'file_kk_name', 'drop_area_kk');
        setupCustomUpload('file_ktp_input', 'upload_button_ktp', 'file_ktp_placeholder', 'file_ktp_name', 'drop_area_ktp');
    });

    function togglePemohonFields() {
        var tipePemohon = document.getElementById('tipe_pemohon').value;
        var peroranganFields = document.getElementById('perorangan_fields');
        var nikInput = document.getElementById('nik_pemohon');
        var jkInput = document.getElementById('jenis_kelamin_pemohon');
        var tempatLahirInput = document.getElementById('tempat_lahir_pemohon');
        var tanggalLahirInput = document.getElementById('tanggal_lahir_pemohon');
        var pekerjaanInput = document.getElementById('pekerjaan_pemohon');

        // Pastikan semua elemen ada sebelum mencoba mengakses propertinya
        if (!peroranganFields || !nikInput || !jkInput || !tempatLahirInput || !tanggalLahirInput || !pekerjaanInput) {
            console.error("Satu atau lebih field perorangan tidak ditemukan. Periksa ID elemen.");
            return;
        }

        if (tipePemohon === 'perorangan') {
            peroranganFields.style.display = 'block';
            nikInput.required = true;
            jkInput.required = true;
            tempatLahirInput.required = true;
            tanggalLahirInput.required = true;
            pekerjaanInput.required = true;
        } else {
            peroranganFields.style.display = 'none';
            nikInput.required = false;
            jkInput.required = false;
            tempatLahirInput.required = false;
            tanggalLahirInput.required = false;
            pekerjaanInput.required = false;

            nikInput.value = '';
            jkInput.value = ''; 
            tempatLahirInput.value = '';
            tanggalLahirInput.value = '';
            pekerjaanInput.value = '';
        }
    }
</script>
@endpush
