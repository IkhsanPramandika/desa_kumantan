@extends('layouts.app')

@section('title', 'Buat Permohonan KK Baru')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Buat Permohonan Kartu Keluarga Baru</h1>

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
        <h6 class="m-0 font-weight-bold text-primary">Form Permohonan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('permohonan-kk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Custom Upload Input for file_kk --}}
            <div class="form-group">
                <label for="file_kk_input">Kartu Keluarga (KK) Asli</label>
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

            {{-- Custom Upload Input for file_ktp --}}
            <div class="form-group">
                <label for="file_ktp_input">KTP Pemohon</label>
                <div class="custom-file-upload-container @error('file_ktp') is-invalid-custom @enderror" id="drop_area_file_ktp">
                    <input type="file" class="d-none" id="file_ktp_input" name="file_ktp" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_file_ktp">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="file_ktp_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="file_ktp_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('file_ktp')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for surat_pengantar_rt_rw --}}
            <div class="form-group">
                <label for="surat_pengantar_rt_rw_input">Surat Pengantar RT/RW</label>
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

            {{-- Custom Upload Input for buku_nikah_akta_cerai --}}
            <div class="form-group">
                <label for="buku_nikah_akta_cerai_input">Buku Nikah / Akta Cerai</label>
                <div class="custom-file-upload-container @error('buku_nikah_akta_cerai') is-invalid-custom @enderror" id="drop_area_buku_nikah_akta_cerai">
                    <input type="file" class="d-none" id="buku_nikah_akta_cerai_input" name="buku_nikah_akta_cerai" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_buku_nikah_akta_cerai">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="buku_nikah_akta_cerai_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="buku_nikah_akta_cerai_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('buku_nikah_akta_cerai')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for surat_pindah_datang --}}
            <div class="form-group">
                <label for="surat_pindah_datang_input">Surat Pindah Datang</label>
                <div class="custom-file-upload-container @error('surat_pindah_datang') is-invalid-custom @enderror" id="drop_area_surat_pindah_datang">
                    <input type="file" class="d-none" id="surat_pindah_datang_input" name="surat_pindah_datang" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_surat_pindah_datang">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="surat_pindah_datang_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="surat_pindah_datang_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('surat_pindah_datang')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Custom Upload Input for ijazah_terakhir --}}
            <div class="form-group">
                <label for="ijazah_terakhir_input">Ijazah Terakhir</label>
                <div class="custom-file-upload-container @error('ijazah_terakhir') is-invalid-custom @enderror" id="drop_area_ijazah_terakhir">
                    <input type="file" class="d-none" id="ijazah_terakhir_input" name="ijazah_terakhir" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary btn-sm" id="upload_button_ijazah_terakhir">Upload</button>
                    <span class="text-muted ml-2 file-placeholder" id="ijazah_terakhir_placeholder">Drag & Drop Files</span>
                    <span class="file-name ml-2 text-muted" id="ijazah_terakhir_name"></span>
                </div>
                <small class="form-text text-muted">Jenis Ekstensi yang diizinkan adalah jpg, jpeg, png, pdf</small>
                <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                @error('ijazah_terakhir')
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

            <button type="submit" class="btn btn-primary">Submit Permohonan</button>
            <a href="{{ route('permohonan-kk.index') }}" class="btn btn-secondary">Batal</a>
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
            'file_ktp',
            'surat_pengantar_rt_rw',
            'buku_nikah_akta_cerai',
            'surat_pindah_datang',
            'ijazah_terakhir'
        ];

        fileInputs.forEach(function(field) {
            const uploadButton = document.getElementById('upload_button_' + field);
            const hiddenInput = document.getElementById(field + '_input');
            const fileNameDisplay = document.getElementById(field + '_name');
            const filePlaceholder = document.getElementById(field + '_placeholder');
            const dropArea = document.getElementById('drop_area_' + field);

            // Check if all necessary elements exist before adding listeners
            if (uploadButton && hiddenInput && fileNameDisplay && filePlaceholder && dropArea) {
                console.log(`[${field}] Initializing listeners for field: ${field}`);

                // 1. Tombol Upload: Memicu klik pada input file tersembunyi
                uploadButton.addEventListener('click', function() {
                    console.log(`[${field}] Upload button clicked. Triggering hidden input.`);
                    hiddenInput.click();
                });

                // 2. Input File Tersembunyi: Memperbarui tampilan nama file saat ada perubahan
                hiddenInput.addEventListener('change', function() {
                    console.log(`[${field}] Hidden input change event detected.`);
                    const file = this.files[0]; // Ambil file pertama dari FileList
                    console.log(`[${field}] FileList on hidden input:`, this.files); // Log FileList

                    if (file) {
                        console.log(`[${field}] File selected: ${file.name}`);
                        fileNameDisplay.textContent = file.name;
                        fileNameDisplay.style.display = 'inline-block';
                        filePlaceholder.style.display = 'none';
                    } else {
                        console.log(`[${field}] No file selected.`);
                        fileNameDisplay.textContent = '';
                        fileNameDisplay.style.display = 'none';
                        filePlaceholder.style.display = 'inline-block';
                    }
                });

                // 3. Drag & Drop Area: Menangani event drag dan drop
                dropArea.addEventListener('dragover', (e) => {
                    e.preventDefault(); // Mencegah perilaku default browser (membuka file)
                    console.log(`[${field}] Drag over.`);
                    dropArea.classList.add('drag-over'); // Tambahkan kelas untuk visual feedback
                });

                dropArea.addEventListener('dragleave', () => {
                    console.log(`[${field}] Drag leave.`);
                    dropArea.classList.remove('drag-over'); // Hapus kelas visual feedback
                });

                dropArea.addEventListener('drop', (e) => {
                    e.preventDefault(); // Mencegah perilaku default browser
                    console.log(`[${field}] Drop event detected.`);
                    dropArea.classList.remove('drag-over'); // Hapus kelas visual feedback

                    const files = e.dataTransfer.files; // Dapatkan FileList dari event drop

                    if (files.length > 0) {
                        console.log(`[${field}] Files dropped:`, files);

                        // Penting: Membuat FileList baru yang dapat ditetapkan ke input file
                        // Ini adalah cara yang lebih kompatibel lintas browser
                        const dt = new DataTransfer();
                        for (let i = 0; i < files.length; i++) {
                            dt.items.add(files[i]);
                        }
                        hiddenInput.files = dt.files; // Tetapkan FileList ke input tersembunyi

                        console.log(`[${field}] Files assigned to hidden input. Current files:`, hiddenInput.files);

                        // Secara manual memicu event 'change' pada input tersembunyi
                        // Ini penting agar browser mendaftarkan perubahan file untuk submit form
                        const event = new Event('change', { bubbles: true });
                        hiddenInput.dispatchEvent(event);
                        console.log(`[${field}] Change event dispatched.`);

                    } else {
                        console.log(`[${field}] No files dropped.`);
                    }
                });
            } else {
                // Log error jika ada elemen yang tidak ditemukan
                console.error(`[${field}] ERROR: Missing element(s). Check your HTML IDs.`);
                if (!uploadButton) console.error(`  - upload_button_${field} not found`);
                if (!hiddenInput) console.error(`  - ${field}_input not found`);
                if (!fileNameDisplay) console.error(`  - ${field}_name not found`);
                if (!filePlaceholder) console.error(`  - ${field}_placeholder not found`);
                if (!dropArea) console.error(`  - drop_area_${field} not found`);
            }
        });
    });
</script>
@endpush