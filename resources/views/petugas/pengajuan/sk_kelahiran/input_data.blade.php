@extends('layouts.app')

@section('title', 'Input Data Surat Keterangan Kelahiran')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Input Data dan Buat Surat Keterangan Kelahiran</h1>

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
        <h6 class="m-0 font-weight-bold text-primary">Form Data Anak dan Orang Tua (untuk Surat Keterangan Kelahiran)</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('permohonan-sk-kelahiran.store-data-and-generate-pdf', $permohonan->id) }}" method="POST">
            @csrf

            <h5>Data Anak</h5>
            <hr>
            <div class="form-group">
                <label for="nama_anak">Nama Lengkap Anak</label>
                <input type="text" class="form-control @error('nama_anak') is-invalid @enderror" id="nama_anak" name="nama_anak" value="{{ old('nama_anak', $permohonan->nama_anak) }}" required>
                @error('nama_anak')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tempat_lahir_anak">Tempat Lahir Anak (contoh: Desa Semundam)</label>
                <input type="text" class="form-control @error('tempat_lahir_anak') is-invalid @enderror" id="tempat_lahir_anak" name="tempat_lahir_anak" value="{{ old('tempat_lahir_anak', $permohonan->tempat_lahir_anak) }}" required>
                @error('tempat_lahir_anak')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_lahir_anak">Tanggal Lahir Anak</label>
                <input type="date" class="form-control @error('tanggal_lahir_anak') is-invalid @enderror" id="tanggal_lahir_anak" name="tanggal_lahir_anak" value="{{ old('tanggal_lahir_anak', $permohonan->tanggal_lahir_anak ? $permohonan->tanggal_lahir_anak->format('Y-m-d') : '') }}" required>
                @error('tanggal_lahir_anak')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="jenis_kelamin_anak">Jenis Kelamin Anak</label>
                <select class="form-control @error('jenis_kelamin_anak') is-invalid @enderror" id="jenis_kelamin_anak" name="jenis_kelamin_anak" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki" {{ old('jenis_kelamin_anak', $permohonan->jenis_kelamin_anak) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin_anak', $permohonan->jenis_kelamin_anak) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin_anak')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

           <div class="form-group">
                <label for="agama_anak">Agama Anak</label>
                <select class="form-control @error('agama_anak') is-invalid @enderror" id="agama_anak" name="agama_anak" required>
                    <option value="">Pilih Agama</option>
                    <option value="Islam" {{ old('agama_anak', $permohonan->agama_anak) == 'Islam' ? 'selected' : '' }}>Islam</option>
                    <option value="Kristen Protestan" {{ old('agama_anak', $permohonan->agama_anak) == 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                    <option value="Katolik" {{ old('agama_anak', $permohonan->agama_anak) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                    <option value="Hindu" {{ old('agama_anak', $permohonan->agama_anak) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                    <option value="Buddha" {{ old('agama_anak', $permohonan->agama_anak) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                    <option value="Konghucu" {{ old('agama_anak', $permohonan->agama_anak) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                </select>
                @error('agama_anak')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="alamat_anak">Alamat Anak (sesuai orang tua)</label>
                <textarea class="form-control @error('alamat_anak') is-invalid @enderror" id="alamat_anak" name="alamat_anak" rows="3" required>{{ old('alamat_anak', $permohonan->alamat_anak) }}</textarea>
                @error('alamat_anak')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <h5>Data Orang Tua</h5>
            <hr>
            <div class="form-group">
                <label for="nama_ayah">Nama Ayah</label>
                <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $permohonan->nama_ayah) }}" required>
                @error('nama_ayah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nik_ayah">NIK Ayah (Opsional)</label>
                <input type="text" class="form-control @error('nik_ayah') is-invalid @enderror" id="nik_ayah" name="nik_ayah" value="{{ old('nik_ayah', $permohonan->nik_ayah) }}">
                @error('nik_ayah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama_ibu">Nama Ibu</label>
                <input type="text" class="form-control @error('nama_ibu') is-invalid @enderror" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu', $permohonan->nama_ibu) }}" required>
                @error('nama_ibu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nik_ibu">NIK Ibu (Opsional)</label>
                <input type="text" class="form-control @error('nik_ibu') is-invalid @enderror" id="nik_ibu" name="nik_ibu" value="{{ old('nik_ibu', $permohonan->nik_ibu) }}">
                @error('nik_ibu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="no_buku_nikah">Nomor Buku Nikah (Opsional)</label>
                <input type="text" class="form-control @error('no_buku_nikah') is-invalid @enderror" id="no_buku_nikah" name="no_buku_nikah" value="{{ old('no_buku_nikah', $permohonan->no_buku_nikah) }}">
                @error('no_buku_nikah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Simpan Data & Buat Surat</button>
            <a href="{{ route('permohonan-sk-kelahiran.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
