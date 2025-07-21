@extends('layouts.app')

@section('title', 'Proses Surat Keterangan Kelahiran')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses & Edit Surat Keterangan Kelahiran</h1>
    <a href="{{ route('petugas.permohonan-sk-kelahiran.show', $permohonan->id) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Detail
    </a>
</div>

{{-- Menampilkan error validasi jika ada --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <p><strong>Harap perbaiki error berikut:</strong></p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('petugas.permohonan-sk-kelahiran.selesaikan', $permohonan->id) }}" method="POST">
    @csrf
    <div class="row">
        {{-- KOLOM KIRI: DATA REFERENSI (READ-ONLY) --}}
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Data Referensi dari Pemohon</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Data Anak</h5>
                    <dl>
                        <dt>Nama:</dt><dd>{{ $permohonan->nama_anak ?? '-' }}</dd>
                        <dt>Tempat, Tgl Lahir:</dt><dd>{{ $permohonan->tempat_lahir_anak ?? '-' }}, {{ $permohonan->tanggal_lahir_anak ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_anak)->isoFormat('D MMMM YYYY') : '-' }}</dd>
                    </dl>
                    <hr>
                    <h5 class="font-weight-bold mt-4">Data Orang Tua</h5>
                    <dl>
                        <dt>Nama Ayah:</dt><dd>{{ $permohonan->nama_ayah ?? '-' }}</dd>
                        <dt>NIK Ayah:</dt><dd>{{ $permohonan->nik_ayah ?? '-' }}</dd>
                        <dt>Nama Ibu:</dt><dd>{{ $permohonan->nama_ibu ?? '-' }}</dd>
                        <dt>NIK Ibu:</dt><dd>{{ $permohonan->nik_ibu ?? '-' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @php
                            $lampiran = [
                                'file_kk' => 'Kartu Keluarga',
                                'file_ktp' => 'KTP Orang Tua',
                                'surat_pengantar_rt_rw' => 'Surat Pengantar RT/RW',
                                'surat_nikah_orangtua' => 'Buku Nikah Orang Tua',
                                'surat_keterangan_kelahiran' => 'Surat dari Bidan/RS',
                            ];
                        @endphp
                        @foreach ($lampiran as $field => $label)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div><i class="fas fa-file-alt text-gray-500 mr-2"></i> {{ $label }}</div>
                            @if($permohonan->$field)
                                <a href="{{ asset('storage/' . $permohonan->$field) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                            @else
                                <span class="badge badge-secondary">Tidak Ada</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: FORM ISIAN PETUGAS (EDITABLE) --}}
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i>Form Isian Surat oleh Petugas</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final. Nomor surat akan dibuat secara otomatis.</p>
                    
                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Data Anak</h5>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>Nama Lengkap Anak</label>
                            <input type="text" class="form-control" name="nama_anak" value="{{ old('nama_anak', $permohonan->nama_anak) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tempat Lahir Anak</label>
                            <input type="text" class="form-control" name="tempat_lahir_anak" value="{{ old('tempat_lahir_anak', $permohonan->tempat_lahir_anak) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Lahir Anak</label>
                            <input type="date" class="form-control" name="tanggal_lahir_anak" value="{{ old('tanggal_lahir_anak', $permohonan->tanggal_lahir_anak ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_anak)->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jenis Kelamin Anak</label>
                            <select name="jenis_kelamin_anak" class="form-control" required>
                                <option value="Laki-laki" @selected(old('jenis_kelamin_anak', $permohonan->jenis_kelamin_anak) == 'Laki-laki')>Laki-laki</option>
                                <option value="Perempuan" @selected(old('jenis_kelamin_anak', $permohonan->jenis_kelamin_anak) == 'Perempuan')>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Data Orang Tua</h5>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Lengkap Ayah</label>
                            <input type="text" class="form-control" name="nama_ayah" value="{{ old('nama_ayah', $permohonan->nama_ayah) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>NIK Ayah</label>
                            <input type="text" class="form-control" name="nik_ayah" value="{{ old('nik_ayah', $permohonan->nik_ayah) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Lengkap Ibu</label>
                            <input type="text" class="form-control" name="nama_ibu" value="{{ old('nama_ibu', $permohonan->nama_ibu) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>NIK Ibu</label>
                            <input type="text" class="form-control" name="nik_ibu" value="{{ old('nik_ibu', $permohonan->nik_ibu) }}" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('petugas.permohonan-sk-kelahiran.show', $permohonan->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')">
                        <i class="fas fa-print"></i> Buat Surat Final & Selesaikan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
