{{-- Lokasi: resources/views/petugas/pengajuan/permohonan_lainnya/create_surat.blade.php --}}
@extends('layouts.app') 

@section('title', 'Buat Surat Permohonan Lainnya')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Buat Surat untuk: {{ $permohonan->masyarakat->nama_lengkap }}</h1>
    <a href="{{ route('petugas.permohonan-lainnya.show', $permohonan->id) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Detail
    </a>
</div>

<div class="row">
    {{-- [PERBAIKAN] KOLOM KIRI: MENAMPILKAN SEMUA INFORMASI DARI PEMOHON --}}
    <div class="col-lg-5">
        {{-- Card Detail Permintaan --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Permintaan Pemohon</h6>
            </div>
            <div class="card-body">
                <dl>
                    <dt>Judul Permintaan</dt>
                    <dd>{{ $permohonan->judul_permohonan }}</dd>
                    <dt>Keperluan</dt>
                    <dd>{{ $permohonan->keperluan }}</dd>
                    <dt>Rincian dari Pemohon</dt>
                    <dd>
                        <div class="p-3 bg-light border rounded mt-1">
                            {!! nl2br(e($permohonan->rincian_pemohon)) !!}
                        </div>
                    </dd>
                </dl>
            </div>
        </div>

        {{-- [FITUR BARU] Card untuk menampilkan lampiran --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6>
            </div>
            <div class="card-body">
                @php
                    $lampiranFiles = !empty($permohonan->lampiran) ? json_decode($permohonan->lampiran, true) : [];
                @endphp

                @if(!empty($lampiranFiles) && is_array($lampiranFiles))
                    <ul class="list-group list-group-flush">
                        @foreach ($lampiranFiles as $index => $file)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                                Lampiran {{ $index + 1 }}
                            </div>
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye fa-sm"></i> Lihat
                            </a>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted text-center my-3">Tidak ada dokumen yang dilampirkan.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- [PERBAIKAN] KOLOM KANAN: FORM PEMBUATAN SURAT --}}
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Pembuatan Surat</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('petugas.permohonan-lainnya.generate-surat', $permohonan->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nomor_surat" class="font-weight-bold">Nomor Surat</label>
                        <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" required placeholder="Contoh: 470/123/PEM-2025">
                    </div>
                    <div class="form-group">
                        <label for="judul_surat_final" class="font-weight-bold">Judul Dokumen (Akan tampil di bawah KOP)</label>
                        <input type="text" class="form-control" id="judul_surat_final" name="judul_surat_final" value="{{ strtoupper($permohonan->judul_permohonan) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="konten_final_html" class="font-weight-bold">Isi Surat</label>
                        <textarea class="form-control" id="wysiwyg" name="konten_final_html" rows="20"></textarea>
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
@endsection

@push('scripts')
{{-- PENTING: Tambahkan Rich Text Editor seperti TinyMCE --}}
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#wysiwyg',
        plugins: 'table lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media',
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link',
        height: 500,
        menubar: false,
    });
</script>
@endpush
