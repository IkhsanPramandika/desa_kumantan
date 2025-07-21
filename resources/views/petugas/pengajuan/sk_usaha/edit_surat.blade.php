@extends('layouts.app')

@section('title', 'Proses Surat Keterangan Usaha')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Proses & Edit Surat Keterangan Usaha</h1>

<div class="row">
    {{-- KOLOM KIRI: DATA DARI MASYARAKAT (READ-ONLY) --}}
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan Masyarakat</h6>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Pemohon</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $permohonan->nama_pemohon ?? '-' }}</dd>
                    <dt class="col-sm-4">NIK</dt><dd class="col-sm-8">{{ $permohonan->nik_pemohon ?? '-' }}</dd>
                    {{-- Tambahkan data pemohon lain jika perlu --}}
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Data Usaha</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama Usaha</dt><dd class="col-sm-8">{{ $permohonan->nama_usaha ?? '-' }}</dd>
                    <dt class="col-sm-4">Alamat Usaha</dt><dd class="col-sm-8">{{ $permohonan->alamat_usaha ?? '-' }}</dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Keperluan Surat</h5>
                <p>{{ $permohonan->keperluan_surat ?? 'Tidak ada keterangan.' }}</p>
                <hr>
                <h5 class="font-weight-bold mt-4">Lampiran</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Kartu Keluarga
                        @if($permohonan->file_kk)
                            <a href="{{ asset('storage/' . $permohonan->file_kk) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                        @else
                            <span class="badge badge-secondary">Tidak Ada</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        KTP
                        @if($permohonan->file_ktp)
                            <a href="{{ asset('storage/' . $permohonan->file_ktp) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                        @else
                            <span class="badge badge-secondary">Tidak Ada</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: FORM ISIAN PETUGAS (EDITABLE) --}}
    <div class="col-lg-7">
        <form action="{{ route('petugas.permohonan-sk-usaha.selesaikan', $permohonan->id) }}" method="POST">
            @csrf
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Isian Surat oleh Petugas</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final.</p>
                    
                    {{-- Anda bisa menambahkan field lain yang perlu ada di surat final --}}
                    {{-- Contoh: Nomor Surat --}}
                    <div class="form-group">
                        <label for="nomor_surat">Nomor Surat</label>
                        <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat') }}" placeholder="Contoh: 503/123/PEM" required>
                    </div>
                    <hr>

                    <h5 class="font-weight-bold">Data Pemohon</h5>
                    <div class="form-group">
                        <label for="nama_pemohon">Nama Pemohon</label>
                        <input type="text" class="form-control" id="nama_pemohon" name="nama_pemohon" value="{{ old('nama_pemohon', $permohonan->nama_pemohon) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nik_pemohon">NIK Pemohon</label>
                        <input type="text" class="form-control" id="nik_pemohon" name="nik_pemohon" value="{{ old('nik_pemohon', $permohonan->nik_pemohon) }}" required>
                    </div>
                    
                    <hr>
                    <h5 class="font-weight-bold mt-4">Data Usaha</h5>
                    <div class="form-group">
                        <label for="nama_usaha">Nama Usaha</label>
                        <input type="text" class="form-control" id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha', $permohonan->nama_usaha) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat_usaha">Alamat Usaha</label>
                        <textarea class="form-control" id="alamat_usaha" name="alamat_usaha" rows="3" required>{{ old('alamat_usaha', $permohonan->alamat_usaha) }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')">
                        <i class="fas fa-print"></i> Buat Surat Final & Selesaikan
                    </button>
                    <a href="{{ route('petugas.permohonan-sk-usaha.show', $permohonan->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
