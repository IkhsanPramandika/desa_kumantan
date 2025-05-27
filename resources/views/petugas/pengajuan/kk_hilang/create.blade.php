@extends('layouts.app')

@section('title', 'Buat Permohonan KK Hilang')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Buat Permohonan Kartu Keluarga Hilang Baru</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Pengajuan Permohonan KK Hilang</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('permohonan-kk-hilang.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="surat_pengantar_rt_rw">File Surat Pengantar RT/RW (PDF/JPG/PNG, Max 2MB)</label>
                <input type="file" class="form-control-file @error('surat_pengantar_rt_rw') is-invalid @enderror" id="surat_pengantar_rt_rw" name="surat_pengantar_rt_rw" accept=".pdf,.jpg,.jpeg,.png" required>
                @error('surat_pengantar_rt_rw')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="surat_keterangan_hilang_kepolisian">File Surat Keterangan Hilang dari Kepolisian (PDF/JPG/PNG, Max 2MB)</label>
                <input type="file" class="form-control-file @error('surat_keterangan_hilang_kepolisian') is-invalid @enderror" id="surat_keterangan_hilang_kepolisian" name="surat_keterangan_hilang_kepolisian" accept=".pdf,.jpg,.jpeg,.png" required>
                @error('surat_keterangan_hilang_kepolisian')
                    <div class="invalid-feedback">{{ $message }}</div>
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
            <a href="{{ route('permohonan-kk-hilang.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection