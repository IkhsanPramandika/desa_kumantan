@extends('layouts.app')

@section('title', 'Input Data Detail Permohonan SK Perkawinan')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Input Data Detail Permohonan SK Perkawinan</h1>

{{-- Menampilkan Notifikasi --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Terjadi kesalahan!</strong> Mohon periksa kembali input Anda.
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Input Data Detail</h6>
    </div>
    <div class="card-body">
        {{-- PERBAIKAN: Pastikan action form memanggil rute '.selesaikan' --}}
        <form action="{{ route('petugas.permohonan-sk-perkawinan.selesaikan', $permohonan->id) }}" method="POST">
            @csrf
            {{-- PERBAIKAN: Hapus @method('PUT') karena rute menggunakan POST --}}

            <div class="form-group">
                <label for="pemohon_surat">Surat Diterbitkan Untuk:</label>
                <select class="form-control @error('pemohon_surat') is-invalid @enderror" id="pemohon_surat" name="pemohon_surat" required>
                    <option value="">-- Pilih Pemohon --</option>
                    <option value="wanita" {{ old('pemohon_surat', $permohonan->pemohon_surat) == 'wanita' ? 'selected' : '' }}>Mempelai Wanita</option>
                    <option value="pria" {{ old('pemohon_surat', $permohonan->pemohon_surat) == 'pria' ? 'selected' : '' }}>Mempelai Pria</option>
                </select>
                @error('pemohon_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <h5 class="mt-4 mb-3">Data Mempelai Pria</h5>
            <div class="form-group">
                <label for="nama_pria">Nama Lengkap Pria</label>
                <input type="text" class="form-control @error('nama_pria') is-invalid @enderror" id="nama_pria" name="nama_pria" value="{{ old('nama_pria', $permohonan->nama_pria) }}" required>
                @error('nama_pria')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="nik_pria">NIK Pria</label>
                <input type="text" class="form-control @error('nik_pria') is-invalid @enderror" id="nik_pria" name="nik_pria" value="{{ old('nik_pria', $permohonan->nik_pria) }}" required>
                @error('nik_pria')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="tempat_lahir_pria">Tempat Lahir Pria</label>
                <input type="text" class="form-control @error('tempat_lahir_pria') is-invalid @enderror" id="tempat_lahir_pria" name="tempat_lahir_pria" value="{{ old('tempat_lahir_pria', $permohonan->tempat_lahir_pria) }}" required>
                @error('tempat_lahir_pria')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="tanggal_lahir_pria">Tanggal Lahir Pria</label>
                <input type="date" class="form-control @error('tanggal_lahir_pria') is-invalid @enderror" id="tanggal_lahir_pria" name="tanggal_lahir_pria" value="{{ old('tanggal_lahir_pria', $permohonan->tanggal_lahir_pria ? $permohonan->tanggal_lahir_pria->format('Y-m-d') : '') }}" required>
                @error('tanggal_lahir_pria')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="alamat_pria">Alamat Pria</label>
                <textarea class="form-control @error('alamat_pria') is-invalid @enderror" id="alamat_pria" name="alamat_pria" rows="3" required>{{ old('alamat_pria', $permohonan->alamat_pria) }}</textarea>
                @error('alamat_pria')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <h5 class="mt-4 mb-3">Data Mempelai Wanita</h5>
            <div class="form-group">
                <label for="nama_wanita">Nama Lengkap Wanita</label>
                <input type="text" class="form-control @error('nama_wanita') is-invalid @enderror" id="nama_wanita" name="nama_wanita" value="{{ old('nama_wanita', $permohonan->nama_wanita) }}" required>
                @error('nama_wanita')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="nik_wanita">NIK Wanita</label>
                <input type="text" class="form-control @error('nik_wanita') is-invalid @enderror" id="nik_wanita" name="nik_wanita" value="{{ old('nik_wanita', $permohonan->nik_wanita) }}" required>
                @error('nik_wanita')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="tempat_lahir_wanita">Tempat Lahir Wanita</label>
                <input type="text" class="form-control @error('tempat_lahir_wanita') is-invalid @enderror" id="tempat_lahir_wanita" name="tempat_lahir_wanita" value="{{ old('tempat_lahir_wanita', $permohonan->tempat_lahir_wanita) }}" required>
                @error('tempat_lahir_wanita')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="tanggal_lahir_wanita">Tanggal Lahir Wanita</label>
                <input type="date" class="form-control @error('tanggal_lahir_wanita') is-invalid @enderror" id="tanggal_lahir_wanita" name="tanggal_lahir_wanita" value="{{ old('tanggal_lahir_wanita', $permohonan->tanggal_lahir_wanita ? $permohonan->tanggal_lahir_wanita->format('Y-m-d') : '') }}" required>
                @error('tanggal_lahir_wanita')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="alamat_wanita">Alamat Wanita</label>
                <textarea class="form-control @error('alamat_wanita') is-invalid @enderror" id="alamat_wanita" name="alamat_wanita" rows="3" required>{{ old('alamat_wanita', $permohonan->alamat_wanita) }}</textarea>
                @error('alamat_wanita')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="tanggal_akad_nikah">Tanggal Akad Nikah</label>
                <input type="date" class="form-control @error('tanggal_akad_nikah') is-invalid @enderror" id="tanggal_akad_nikah" name="tanggal_akad_nikah" value="{{ old('tanggal_akad_nikah', $permohonan->tanggal_akad_nikah ? $permohonan->tanggal_akad_nikah->format('Y-m-d') : '') }}" required>
                @error('tanggal_akad_nikah')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">Simpan Data & Generate PDF</button>
            {{-- PERBAIKAN: Tambahkan prefix 'petugas.' pada rute tombol Batal --}}
            <a href="{{ route('petugas.permohonan-sk-perkawinan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
