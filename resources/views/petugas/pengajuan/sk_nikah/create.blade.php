@extends('layouts.app')

@section('title', 'Ajukan Permohonan Surat Keterangan Perkawinan')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Ajukan Permohonan Surat Keterangan Perkawinan</h1>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terjadi kesalahan!</strong> Mohon periksa kembali input Anda.
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Pengajuan Dokumen Persyaratan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('permohonan-sk-perkawinan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Custom Upload Input for file_kk --}}
            <div class="form-group">
                <label for="file_kk_input">File Kartu Keluarga (KK)</label>
                <div class="custom-file-upload-container @error('file_kk') is-invalid-custom @enderror" id="drop_area_file_kk">
                    <input type="file" class="d-none" id="file_kk_input" name="file_kk" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_file_kk">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="file_kk_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="file_kk_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('file_kk')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for file_ktp_mempelai --}}
            <div class="form-group">
                <label for="file_ktp_mempelai_input">File KTP Mempelai (Pria & Wanita, gabungkan dalam 1 file PDF)</label>
                <div class="custom-file-upload-container @error('file_ktp_mempelai') is-invalid-custom @enderror" id="drop_area_file_ktp_mempelai">
                    <input type="file" class="d-none" id="file_ktp_mempelai_input" name="file_ktp_mempelai" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_file_ktp_mempelai">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="file_ktp_mempelai_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="file_ktp_mempelai_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 5MB.</small>
                @error('file_ktp_mempelai')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for surat_nikah_orang_tua --}}
            <div class="form-group">
                <label for="surat_nikah_orang_tua_input">File Surat Nikah Orang Tua (Opsional)</label>
                <div class="custom-file-upload-container @error('surat_nikah_orang_tua') is-invalid-custom @enderror" id="drop_area_surat_nikah_orang_tua">
                    <input type="file" class="d-none" id="surat_nikah_orang_tua_input" name="surat_nikah_orang_tua" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_surat_nikah_orang_tua">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="surat_nikah_orang_tua_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="surat_nikah_orang_tua_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('surat_nikah_orang_tua')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for kartu_imunisasi_catin --}}
            <div class="form-group">
                <label for="kartu_imunisasi_catin_input">File Kartu Imunisasi Calon Pengantin (Opsional)</label>
                <div class="custom-file-upload-container @error('kartu_imunisasi_catin') is-invalid-custom @enderror" id="drop_area_kartu_imunisasi_catin">
                    <input type="file" class="d-none" id="kartu_imunisasi_catin_input" name="kartu_imunisasi_catin" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_kartu_imunisasi_catin">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="kartu_imunisasi_catin_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="kartu_imunisasi_catin_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('kartu_imunisasi_catin')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for sertifikat_elsimil --}}
            <div class="form-group">
                <label for="sertifikat_elsimil_input">File Sertifikat Elsimil (Opsional)</label>
                <div class="custom-file-upload-container @error('sertifikat_elsimil') is-invalid-custom @enderror" id="drop_area_sertifikat_elsimil">
                    <input type="file" class="d-none" id="sertifikat_elsimil_input" name="sertifikat_elsimil" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_sertifikat_elsimil">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="sertifikat_elsimil_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="sertifikat_elsimil_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('sertifikat_elsimil')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for akta_penceraian --}}
            <div class="form-group">
                <label for="akta_penceraian_input">File Akta Perceraian (Jika Ada, Opsional)</label>
                <div class="custom-file-upload-container @error('akta_penceraian') is-invalid-custom @enderror" id="drop_area_akta_penceraian">
                    <input type="file" class="d-none" id="akta_penceraian_input" name="akta_penceraian" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_akta_penceraian">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="akta_penceraian_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="akta_penceraian_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('akta_penceraian')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="catatan">Catatan (Opsional)</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Ajukan Permohonan</button>
            <a href="{{ route('permohonan-sk-perkawinan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
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
        min-height: 38px;
    }
    .custom-file-upload-container:focus-within {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .custom-file-upload-container.drag-over {
        border-color: #007bff;
        background-color: #e9f7ff;
    }
    .custom-file-upload-container.is-invalid-custom {
        border-color: #dc3545;
    }
    .invalid-feedback.d-block {
        display: block !important;
    }
    .file-placeholder {
        color: #6c757d;
        flex-grow: 1;
        text-align: right;
    }
    .file-name {
        font-weight: bold;
        color: #333;
        flex-grow: 1;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: none;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = [
            'file_kk',
            'file_ktp_mempelai',
            'surat_nikah_orang_tua',
            'kartu_imunisasi_catin',
            'sertifikat_elsimil',
            'akta_penceraian',
        ];

        fileInputs.forEach(function(field) {
            const uploadButton = document.getElementById('upload_button_' + field);
            const hiddenInput = document.getElementById(field + '_input');
            const fileNameDisplay = document.getElementById(field + '_name');
            const filePlaceholder = document.getElementById(field + '_placeholder');
            const dropArea = document.getElementById('drop_area_' + field);

            if (uploadButton && hiddenInput && fileNameDisplay && filePlaceholder && dropArea) {
                // Trigger hidden input click when custom button is clicked
                uploadButton.addEventListener('click', function() {
                    hiddenInput.click();
                });

                // Update file name display when file is selected or dropped
                hiddenInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        fileNameDisplay.textContent = file.name;
                        fileNameDisplay.style.display = 'inline-block';
                        filePlaceholder.style.display = 'none';
                        dropArea.classList.remove('is-invalid-custom'); // Remove invalid state on file select
                    } else {
                        fileNameDisplay.textContent = '';
                        fileNameDisplay.style.display = 'none';
                        filePlaceholder.style.display = 'inline-block';
                    }
                });

                // Basic Drag & Drop functionality
                dropArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropArea.classList.add('drag-over');
                });

                dropArea.addEventListener('dragleave', () => {
                    dropArea.classList.remove('drag-over');
                });

                dropArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropArea.classList.remove('drag-over');
                    const files = e.dataTransfer.files;

                    if (files.length > 0) {
                        const dt = new DataTransfer();
                        for (let i = 0; i < files.length; i++) {
                            dt.items.add(files[i]); 
                        }
                        hiddenInput.files = dt.files;

                        const event = new Event('change', { bubbles: true });
                        hiddenInput.dispatchEvent(event);
                    }
                });
            }
        });
    });
</script>
@endpush
