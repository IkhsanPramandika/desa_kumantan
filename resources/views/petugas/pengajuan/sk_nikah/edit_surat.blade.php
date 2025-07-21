@extends('layouts.app')

@section('title', 'Proses Surat Pengantar Nikah')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Proses & Edit Surat Pengantar Nikah</h1>

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

<form action="{{ route('petugas.permohonan-sk-perkawinan.selesaikan', $permohonan->id) }}" method="POST">
    @csrf
    <div class="row">
        {{-- KOLOM KIRI: DATA DARI MASYARAKAT (READ-ONLY) --}}
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Calon Mempelai Pria</h5>
                    <dl>
                        <dt>Nama:</dt><dd>{{ $permohonan->nama_pria ?? '-' }}</dd>
                        <dt>NIK:</dt><dd>{{ $permohonan->nik_pria ?? '-' }}</dd>
                        <dt>Alamat:</dt><dd>{{ $permohonan->alamat_pria ?? '-' }}</dd>
                    </dl>
                    <hr>
                    <h5 class="font-weight-bold mt-4">Calon Mempelai Wanita</h5>
                    <dl>
                        <dt>Nama:</dt><dd>{{ $permohonan->nama_wanita ?? '-' }}</dd>
                        <dt>NIK:</dt><dd>{{ $permohonan->nik_wanita ?? '-' }}</dd>
                        <dt>Alamat:</dt><dd>{{ $permohonan->alamat_wanita ?? '-' }}</dd>
                    </dl>
                    <hr>
                </div>
            </div>

            {{-- BAGIAN BARU: MENAMPILKAN LAMPIRAN --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @php
                            $lampiran = [
                                'file_kk' => 'Kartu Keluarga',
                                'file_ktp_mempelai' => 'KTP Kedua Mempelai',
                                'surat_nikah_orang_tua' => 'Surat Nikah Orang Tua',
                                'kartu_imunisasi_catin' => 'Kartu Imunisasi Catin',
                                'sertifikat_elsimil' => 'Sertifikat Elsimil',
                                'akta_penceraian' => 'Akta Perceraian',
                            ];
                        @endphp
                        @foreach ($lampiran as $field => $label)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            {{ $label }}
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
                    <h6 class="m-0 font-weight-bold text-primary">Form Isian Surat oleh Petugas</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final.</p>
                    
                    <div class="alert alert-info">Nomor surat akan dibuat secara otomatis oleh sistem.</div>

                    <h5 class="font-weight-bold mt-4">Data Calon Mempelai Pria</h5>
                    <div class="form-group">
                        <label>Nama Lengkap Pria</label>
                        <input type="text" class="form-control" name="nama_pria" value="{{ old('nama_pria', $permohonan->nama_pria) }}" required>
                    </div>
                    <div class="form-group">
                        <label>NIK Pria</label>
                        <input type="text" class="form-control" name="nik_pria" value="{{ old('nik_pria', $permohonan->nik_pria) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir Pria</label>
                        <input type="text" class="form-control" name="tempat_lahir_pria" value="{{ old('tempat_lahir_pria', $permohonan->tempat_lahir_pria) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir Pria</label>
                        <input type="date" class="form-control" name="tanggal_lahir_pria" value="{{ old('tanggal_lahir_pria', $permohonan->tanggal_lahir_pria ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pria)->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap Pria</label>
                        <textarea class="form-control" name="alamat_pria" rows="3" required>{{ old('alamat_pria', $permohonan->alamat_pria) }}</textarea>
                    </div>

                    <hr>
                    <h5 class="font-weight-bold mt-4">Data Calon Mempelai Wanita</h5>
                     <div class="form-group">
                        <label>Nama Lengkap Wanita</label>
                        <input type="text" class="form-control" name="nama_wanita" value="{{ old('nama_wanita', $permohonan->nama_wanita) }}" required>
                    </div>
                    <div class="form-group">
                        <label>NIK Wanita</label>
                        <input type="text" class="form-control" name="nik_wanita" value="{{ old('nik_wanita', $permohonan->nik_wanita) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir Wanita</label>
                        <input type="text" class="form-control" name="tempat_lahir_wanita" value="{{ old('tempat_lahir_wanita', $permohonan->tempat_lahir_wanita) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir Wanita</label>
                        <input type="date" class="form-control" name="tanggal_lahir_wanita" value="{{ old('tanggal_lahir_wanita', $permohonan->tanggal_lahir_wanita ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_wanita)->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap Wanita</label>
                        <textarea class="form-control" name="alamat_wanita" rows="3" required>{{ old('alamat_wanita', $permohonan->alamat_wanita) }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')">
                        <i class="fas fa-print"></i> Buat Surat Final & Selesaikan
                    </button>
                    <a href="{{ route('petugas.permohonan-sk-perkawinan.show', $permohonan->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
