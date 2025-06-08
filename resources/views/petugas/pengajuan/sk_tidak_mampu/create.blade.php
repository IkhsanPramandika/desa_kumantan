@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Buat Permohonan SK Tidak Mampu')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Permohonan Surat Keterangan Tidak Mampu</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Isi Data Permohonan SK Tidak Mampu</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('permohonan-sk-tidak-mampu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h5 class="mb-3 text-gray-800">Data Pemohon Utama (Kepala Keluarga/Penanggung Jawab)</h5>
                <div class="form-group">
                    <label for="nama_pemohon">Nama Lengkap Pemohon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_pemohon') is-invalid @enderror" id="nama_pemohon" name="nama_pemohon" value="{{ old('nama_pemohon') }}" required>
                    @error('nama_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="nik_pemohon">NIK Pemohon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nik_pemohon') is-invalid @enderror" id="nik_pemohon" name="nik_pemohon" value="{{ old('nik_pemohon') }}" required>
                    @error('nik_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tempat_lahir_pemohon">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tempat_lahir_pemohon') is-invalid @enderror" id="tempat_lahir_pemohon" name="tempat_lahir_pemohon" value="{{ old('tempat_lahir_pemohon') }}" required>
                        @error('tempat_lahir_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tanggal_lahir_pemohon">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_lahir_pemohon') is-invalid @enderror" id="tanggal_lahir_pemohon" name="tanggal_lahir_pemohon" value="{{ old('tanggal_lahir_pemohon') }}" required>
                        @error('tanggal_lahir_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="jenis_kelamin_pemohon">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-control @error('jenis_kelamin_pemohon') is-invalid @enderror" id="jenis_kelamin_pemohon" name="jenis_kelamin_pemohon" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="LAKI-LAKI" {{ old('jenis_kelamin_pemohon') == 'LAKI-LAKI' ? 'selected' : '' }}>LAKI-LAKI</option>
                        <option value="PEREMPUAN" {{ old('jenis_kelamin_pemohon') == 'PEREMPUAN' ? 'selected' : '' }}>PEREMPUAN</option>
                    </select>
                    @error('jenis_kelamin_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="agama_pemohon">Agama</label>
                    <input type="text" class="form-control @error('agama_pemohon') is-invalid @enderror" id="agama_pemohon" name="agama_pemohon" value="{{ old('agama_pemohon') }}">
                    @error('agama_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="kewarganegaraan_pemohon">Kewarganegaraan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('kewarganegaraan_pemohon') is-invalid @enderror" id="kewarganegaraan_pemohon" name="kewarganegaraan_pemohon" value="{{ old('kewarganegaraan_pemohon', 'Indonesia') }}" required>
                    @error('kewarganegaraan_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="pekerjaan_pemohon">Pekerjaan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('pekerjaan_pemohon') is-invalid @enderror" id="pekerjaan_pemohon" name="pekerjaan_pemohon" value="{{ old('pekerjaan_pemohon') }}" required>
                    @error('pekerjaan_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_pemohon">Alamat Pemohon <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('alamat_pemohon') is-invalid @enderror" id="alamat_pemohon" name="alamat_pemohon" rows="3" required>{{ old('alamat_pemohon') }}</textarea>
                    @error('alamat_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr>
                <h5 class="mb-3 mt-4 text-gray-800">Data Anak/Anggota Keluarga yang Berkepentingan (Jika Ada)</h5>
                <p class="text-muted small">Isi bagian ini jika surat keterangan tidak mampu ini untuk anak atau anggota keluarga lain dari pemohon utama.</p>

                <div class="form-group">
                    <label for="nama_terkait">Nama Lengkap Anak/Terkait</label>
                    <input type="text" class="form-control @error('nama_terkait') is-invalid @enderror" id="nama_terkait" name="nama_terkait" value="{{ old('nama_terkait') }}">
                    @error('nama_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="nik_terkait">NIK Anak/Terkait</label>
                    <input type="text" class="form-control @error('nik_terkait') is-invalid @enderror" id="nik_terkait" name="nik_terkait" value="{{ old('nik_terkait') }}">
                    @error('nik_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tempat_lahir_terkait">Tempat Lahir Anak/Terkait</label>
                        <input type="text" class="form-control @error('tempat_lahir_terkait') is-invalid @enderror" id="tempat_lahir_terkait" name="tempat_lahir_terkait" value="{{ old('tempat_lahir_terkait') }}">
                        @error('tempat_lahir_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tanggal_lahir_terkait">Tanggal Lahir Anak/Terkait</label>
                        <input type="date" class="form-control @error('tanggal_lahir_terkait') is-invalid @enderror" id="tanggal_lahir_terkait" name="tanggal_lahir_terkait" value="{{ old('tanggal_lahir_terkait') }}">
                        @error('tanggal_lahir_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="jenis_kelamin_terkait">Jenis Kelamin Anak/Terkait</label>
                    <select class="form-control @error('jenis_kelamin_terkait') is-invalid @enderror" id="jenis_kelamin_terkait" name="jenis_kelamin_terkait">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="LAKI-LAKI" {{ old('jenis_kelamin_terkait') == 'LAKI-LAKI' ? 'selected' : '' }}>LAKI-LAKI</option>
                        <option value="PEREMPUAN" {{ old('jenis_kelamin_terkait') == 'PEREMPUAN' ? 'selected' : '' }}>PEREMPUAN</option>
                    </select>
                    @error('jenis_kelamin_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="agama_terkait">Agama Anak/Terkait</label>
                    <input type="text" class="form-control @error('agama_terkait') is-invalid @enderror" id="agama_terkait" name="agama_terkait" value="{{ old('agama_terkait') }}">
                    @error('agama_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group">
                    <label for="kewarganegaraan_terkait">Kewarganegaraan Anak/Terkait</label>
                    <input type="text" class="form-control @error('kewarganegaraan_terkait') is-invalid @enderror" id="kewarganegaraan_terkait" name="kewarganegaraan_terkait" value="{{ old('kewarganegaraan_terkait', 'Indonesia') }}">
                    @error('kewarganegaraan_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="pekerjaan_atau_sekolah_terkait">Pekerjaan/Sekolah Anak/Terkait</label>
                    <input type="text" class="form-control @error('pekerjaan_atau_sekolah_terkait') is-invalid @enderror" id="pekerjaan_atau_sekolah_terkait" name="pekerjaan_atau_sekolah_terkait" value="{{ old('pekerjaan_atau_sekolah_terkait') }}">
                    @error('pekerjaan_atau_sekolah_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_terkait">Alamat Anak/Terkait (Jika berbeda dari pemohon)</label>
                    <textarea class="form-control @error('alamat_terkait') is-invalid @enderror" id="alamat_terkait" name="alamat_terkait" rows="3">{{ old('alamat_terkait') }}</textarea>
                    @error('alamat_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr>
                <h5 class="mb-3 mt-4 text-gray-800">Informasi Tambahan & Dokumen</h5>
                <div class="form-group">
                    <label for="keperluan_surat">Keperluan Pembuatan Surat <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('keperluan_surat') is-invalid @enderror" id="keperluan_surat" name="keperluan_surat" rows="3" required>{{ old('keperluan_surat') }}</textarea>
                    @error('keperluan_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="file_kk">File Kartu Keluarga (KK)</label>
                    <input type="file" class="form-control-file @error('file_kk') is-invalid @enderror" id="file_kk" name="file_kk" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max 2MB.</small>
                    @error('file_kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="file_ktp">File KTP Pemohon</label>
                    <input type="file" class="form-control-file @error('file_ktp') is-invalid @enderror" id="file_ktp" name="file_ktp" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max 2MB.</small>
                    @error('file_ktp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group">
                    <label for="file_pendukung_lain">File Pendukung Lainnya (Misal: Surat RT/RW, Foto Rumah, dll)</label>
                    <input type="file" class="form-control-file @error('file_pendukung_lain') is-invalid @enderror" id="file_pendukung_lain" name="file_pendukung_lain" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Max 2MB.</small>
                    @error('file_pendukung_lain') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="catatan_pemohon">Catatan Tambahan dari Pemohon</label>
                    <textarea class="form-control @error('catatan_pemohon') is-invalid @enderror" id="catatan_pemohon" name="catatan_pemohon" rows="3">{{ old('catatan_pemohon') }}</textarea>
                    @error('catatan_pemohon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Ajukan Permohonan</button>
                <a href="{{ route('permohonan-sk-tidak-mampu.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
