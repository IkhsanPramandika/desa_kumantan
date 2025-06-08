@extends('layouts.app')

@section('title', 'Input Data Detail SK Usaha')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Input Data Detail Surat Keterangan Usaha</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Input Data Permohonan SK Usaha #{{ $permohonan->id }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('permohonan-sk-usaha.store-data-pdf', $permohonan->id) }}" method="POST">
            @csrf

            <h5 class="mb-3 text-gray-800">Data Pemohon</h5>
            <div class="form-group">
                <label for="nama_pemohon">Nama Lengkap Pemohon <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_pemohon') is-invalid @enderror" id="nama_pemohon" name="nama_pemohon" value="{{ old('nama_pemohon', $permohonan->nama_pemohon) }}" required>
                @error('nama_pemohon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="nik_pemohon">NIK Pemohon <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nik_pemohon') is-invalid @enderror" id="nik_pemohon" name="nik_pemohon" value="{{ old('nik_pemohon', $permohonan->nik_pemohon) }}" required>
                @error('nik_pemohon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="LAKI-LAKI" {{ old('jenis_kelamin', $permohonan->jenis_kelamin) == 'LAKI-LAKI' ? 'selected' : '' }}>LAKI-LAKI</option>
                    <option value="PEREMPUAN" {{ old('jenis_kelamin', $permohonan->jenis_kelamin) == 'PEREMPUAN' ? 'selected' : '' }}>PEREMPUAN</option>
                </select>
                @error('jenis_kelamin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $permohonan->tempat_lahir) }}" required>
                    @error('tempat_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $permohonan->tanggal_lahir ? $permohonan->tanggal_lahir->format('Y-m-d') : '') }}" required>
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="warganegara_agama">Warganegara / Agama <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('warganegara_agama') is-invalid @enderror" id="warganegara_agama" name="warganegara_agama" value="{{ old('warganegara_agama', $permohonan->warganegara_agama) }}" required>
                @error('warganegara_agama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="pekerjaan">Pekerjaan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan', $permohonan->pekerjaan) }}" required>
                @error('pekerjaan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="alamat_pemohon">Alamat Pemohon <span class="text-danger">*</span></label>
                <textarea class="form-control @error('alamat_pemohon') is-invalid @enderror" id="alamat_pemohon" name="alamat_pemohon" rows="3" required>{{ old('alamat_pemohon', $permohonan->alamat_pemohon) }}</textarea>
                @error('alamat_pemohon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <h5 class="mb-3 mt-4 text-gray-800">Detail Usaha</h5>
            <div class="form-group">
                <label for="nama_usaha">Nama Usaha (dari Permohonan) <span class="text-muted">(Tidak dapat diedit)</span></label>
                <input type="text" class="form-control" value="{{ $permohonan->nama_usaha }}" disabled>
            </div>
            <div class="form-group">
                <label for="alamat_usaha">Alamat Usaha (dari Permohonan) <span class="text-muted">(Tidak dapat diedit)</span></label>
                <textarea class="form-control" rows="3" disabled>{{ $permohonan->alamat_usaha }}</textarea>
            </div>
            <div class="form-group">
                <label for="tujuan">Tujuan Surat Keterangan Usaha <span class="text-danger">*</span></label>
                <textarea class="form-control @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan" rows="3" required>{{ old('tujuan', $permohonan->tujuan) }}</textarea>
                @error('tujuan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="nomor_surat">Nomor Surat (Final) <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nomor_surat') is-invalid @enderror" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat', $permohonan->nomor_surat) }}" placeholder="Contoh: 205/0016/TR/1/2021" required>
                @error('nomor_surat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <a href="{{ route('permohonan-sk-usaha.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan & Generate Surat</button>
        </form>
    </div>
</div>
@endsection