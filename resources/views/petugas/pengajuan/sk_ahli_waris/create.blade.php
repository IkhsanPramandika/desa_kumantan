@extends('layouts.app')

@section('title', 'Ajukan Permohonan Surat Keterangan Ahli Waris')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Ajukan Permohonan Surat Keterangan Ahli Waris</h1>

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
        <form action="{{ route('permohonan-sk-ahli-waris.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Custom Upload Input for file_ktp_pemohon --}}
            <div class="form-group">
                <label for="file_ktp_pemohon_input">File KTP Pemohon</label>
                <div class="custom-file-upload-container @error('file_ktp_pemohon') is-invalid-custom @enderror" id="drop_area_file_ktp_pemohon">
                    <input type="file" class="d-none" id="file_ktp_pemohon_input" name="file_ktp_pemohon" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_file_ktp_pemohon">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="file_ktp_pemohon_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="file_ktp_pemohon_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('file_ktp_pemohon')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for file_kk_pemohon --}}
            <div class="form-group">
                <label for="file_kk_pemohon_input">File Kartu Keluarga (KK) Pemohon</label>
                <div class="custom-file-upload-container @error('file_kk_pemohon') is-invalid-custom @enderror" id="drop_area_file_kk_pemohon">
                    <input type="file" class="d-none" id="file_kk_pemohon_input" name="file_kk_pemohon" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_file_kk_pemohon">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="file_kk_pemohon_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="file_kk_pemohon_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('file_kk_pemohon')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for file_ktp_ahli_waris --}}
            <div class="form-group">
                <label for="file_ktp_ahli_waris_input">File KTP Ahli Waris (Jika lebih dari 1, gabungkan dalam 1 file PDF)</label>
                <div class="custom-file-upload-container @error('file_ktp_ahli_waris') is-invalid-custom @enderror" id="drop_area_file_ktp_ahli_waris">
                    <input type="file" class="d-none" id="file_ktp_ahli_waris_input" name="file_ktp_ahli_waris" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_file_ktp_ahli_waris">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="file_ktp_ahli_waris_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="file_ktp_ahli_waris_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 5MB.</small>
                @error('file_ktp_ahli_waris')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for file_kk_ahli_waris --}}
            <div class="form-group">
                <label for="file_kk_ahli_waris_input">File Kartu Keluarga (KK) Ahli Waris (Jika lebih dari 1, gabungkan dalam 1 file PDF)</label>
                <div class="custom-file-upload-container @error('file_kk_ahli_waris') is-invalid-custom @enderror" id="drop_area_file_kk_ahli_waris">
                    <input type="file" class="d-none" id="file_kk_ahli_waris_input" name="file_kk_ahli_waris" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_file_kk_ahli_waris">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="file_kk_ahli_waris_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="file_kk_ahli_waris_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 5MB.</small>
                @error('file_kk_ahli_waris')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for surat_keterangan_kematian --}}
            <div class="form-group">
                <label for="surat_keterangan_kematian_input">File Surat Keterangan Kematian Pewaris</label>
                <div class="custom-file-upload-container @error('surat_keterangan_kematian') is-invalid-custom @enderror" id="drop_area_surat_keterangan_kematian">
                    <input type="file" class="d-none" id="surat_keterangan_kematian_input" name="surat_keterangan_kematian" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_surat_keterangan_kematian">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="surat_keterangan_kematian_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="surat_keterangan_kematian_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('surat_keterangan_kematian')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for surat_pengantar_rt_rw --}}
            <div class="form-group">
                <label for="surat_pengantar_rt_rw_input">File Surat Pengantar RT/RW</label>
                <div class="custom-file-upload-container @error('surat_pengantar_rt_rw') is-invalid-custom @enderror" id="drop_area_surat_pengantar_rt_rw">
                    <input type="file" class="d-none" id="surat_pengantar_rt_rw_input" name="surat_pengantar_rt_rw" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_surat_pengantar_rt_rw">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="surat_pengantar_rt_rw_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="surat_pengantar_rt_rw_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('surat_pengantar_rt_rw')
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
            <a href="{{ route('permohonan-sk-ahli-waris.index') }}" class="btn btn-secondary">Batal</a>
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
            'file_ktp_pemohon',
            'file_kk_pemohon',
            'file_ktp_ahli_waris',
            'file_kk_ahli_waris',
            'surat_keterangan_kematian',
            // 'surat_nikah_pewaris', // Hapus dari daftar field JavaScript
            'surat_pengantar_rt_rw',
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