@extends('layouts.app')
@section('title', 'Buat Permohonan SK Usaha')
@section('content')
<h1 class="h3 mb-4 text-gray-800">Form Permohonan Surat Keterangan Usaha</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Isi Data Permohonan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('permohonan-sk-usaha.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h5 class="mb-3 text-gray-800">Data Pemohon</h5>
            {{-- ... field data pemohon lainnya ... --}}
             <div class="form-group">
                 <label for="nama_pemohon">Nama Lengkap Pemohon <span class="text-danger">*</span></label>
                 <input type="text" class="form-control @error('nama_pemohon') is-invalid @enderror" id="nama_pemohon" name="nama_pemohon" value="{{ old('nama_pemohon') }}" required>
                 @error('nama_pemohon')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>
             <div class="form-group">
                 <label for="nik_pemohon">NIK Pemohon <span class="text-danger">*</span></label>
                 <input type="text" class="form-control @error('nik_pemohon') is-invalid @enderror" id="nik_pemohon" name="nik_pemohon" value="{{ old('nik_pemohon') }}" required>
                 @error('nik_pemohon')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>
             <div class="form-group">
                 <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                 <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                     <option value="">Pilih Jenis Kelamin</option>
                     <option value="LAKI-LAKI" {{ old('jenis_kelamin') == 'LAKI-LAKI' ? 'selected' : '' }}>LAKI-LAKI</option>
                     <option value="PEREMPUAN" {{ old('jenis_kelamin') == 'PEREMPUAN' ? 'selected' : '' }}>PEREMPUAN</option>
                 </select>
                 @error('jenis_kelamin')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>
             <div class="form-row">
                 <div class="form-group col-md-6">
                     <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                     <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required>
                     @error('tempat_lahir')
                         <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                 </div>
                 <div class="form-group col-md-6">
                     <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                     <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                     @error('tanggal_lahir')
                         <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                 </div>
             </div>
             <div class="form-group">
                 <label for="warganegara_agama">Warganegara / Agama <span class="text-danger">*</span></label>
                 <input type="text" class="form-control @error('warganegara_agama') is-invalid @enderror" id="warganegara_agama" name="warganegara_agama" value="{{ old('warganegara_agama') }}" required>
                 @error('warganegara_agama')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>
             <div class="form-group">
                 <label for="pekerjaan">Pekerjaan <span class="text-danger">*</span></label>
                 <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" required>
                 @error('pekerjaan')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>
             <div class="form-group">
                 <label for="alamat_pemohon">Alamat Pemohon <span class="text-danger">*</span></label>
                 <textarea class="form-control @error('alamat_pemohon') is-invalid @enderror" id="alamat_pemohon" name="alamat_pemohon" rows="3" required>{{ old('alamat_pemohon') }}</textarea>
                 @error('alamat_pemohon')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>


            <h5 class="mb-3 mt-4 text-gray-800">Detail Usaha</h5>
            <div class="form-group">
                <label for="nama_usaha">Nama Usaha <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_usaha') is-invalid @enderror" id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha') }}" required>
                @error('nama_usaha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="alamat_usaha">Alamat Usaha <span class="text-danger">*</span></label>
                <textarea class="form-control @error('alamat_usaha') is-invalid @enderror" id="alamat_usaha" name="alamat_usaha" rows="3" required>{{ old('alamat_usaha') }}</textarea>
                @error('alamat_usaha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <h5 class="mb-3 mt-4 text-gray-800">Upload Dokumen Pendukung</h5>
            {{-- ... field upload file_kk dan file_ktp ... --}}
             <div class="form-group">
                 <label for="file_kk">File Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                 <input type="file" class="form-control-file @error('file_kk') is-invalid @enderror" id="file_kk" name="file_kk" accept=".pdf,.jpg,.jpeg,.png" required>
                 <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max 2MB.</small>
                 @error('file_kk')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>
             <div class="form-group">
                 <label for="file_ktp">File KTP Pemohon <span class="text-danger">*</span></label>
                 <input type="file" class="form-control-file @error('file_ktp') is-invalid @enderror" id="file_ktp" name="file_ktp" accept=".pdf,.jpg,.jpeg,.png" required>
                 <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max 2MB.</small>
                 @error('file_ktp')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>

            <button type="submit" class="btn btn-primary">Ajukan Permohonan</button>
            <a href="{{ route('permohonan-sk-usaha.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection