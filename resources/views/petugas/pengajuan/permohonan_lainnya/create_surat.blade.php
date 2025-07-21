{{-- Ganti dengan layout admin Anda --}}
@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Buat Surat untuk: {{ $permohonan->masyarakat->nama_lengkap }}</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Permintaan Pemohon</h6>
        </div>
        <div class="card-body">
            <p><strong>Judul Permintaan:</strong> {{ $permohonan->judul_permohonan }}</p>
            <p><strong>Keperluan:</strong> {{ $permohonan->keperluan }}</p>
            <div><strong>Rincian dari Pemohon:</strong> 
                <div class="p-3 bg-light border rounded">
                    {!! nl2br(e($permohonan->rincian_pemohon)) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Pembuatan Surat</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('petugas.permohonan-lainnya.generate-surat', $permohonan->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nomor_surat">Nomor Surat</label>
                    <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" required>
                </div>
                <div class="form-group">
                    <label for="judul_surat_final">Judul Dokumen (Akan tampil di bawah KOP)</label>
                    <input type="text" class="form-control" id="judul_surat_final" name="judul_surat_final" value="{{ strtoupper($permohonan->judul_permohonan) }}" required>
                </div>
                <div class="form-group">
                    <label for="konten_final_html">Isi Surat</label>
                    {{-- Ini adalah tempat untuk Rich Text Editor --}}
                    <textarea class="form-control" id="wysiwyg" name="konten_final_html" rows="15"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Generate Surat & Selesaikan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- PENTING: Tambahkan Rich Text Editor seperti TinyMCE --}}
<script src="https://cdn.tiny.cloud/1/3fi9aqma9lmgcqhmpbu9mmo34onbhectbfhqiavjvor03d7o/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#wysiwyg',
        plugins: 'table lists link image',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | table',
    });
</script>
@endpush