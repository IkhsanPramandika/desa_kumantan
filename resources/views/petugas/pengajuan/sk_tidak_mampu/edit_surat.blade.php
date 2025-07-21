@extends('layouts.app')

@section('title', 'Proses Surat Keterangan Tidak Mampu')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Proses & Edit Surat Keterangan Tidak Mampu</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- PERBAIKAN: Menambahkan tag <form> yang benar di sini --}}
<form action="{{ route('petugas.permohonan-sk-tidak-mampu.selesaikan', $permohonan->id) }}" method="POST">
    @csrf
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
                        {{-- PERBAIKAN: Menggunakan relasi masyarakat --}}
                        <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $permohonan->masyarakat->nama_lengkap ?? '-' }}</dd>
                        <dt class="col-sm-4">NIK</dt><dd class="col-sm-8">{{ $permohonan->masyarakat->nik ?? '-' }}</dd>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Form Isian Surat oleh Petugas</h6></div>
                <div class="card-body">
                    <p class="text-muted">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final.</p>
                    <div class="alert alert-info">Nomor surat akan dibuat secara otomatis oleh sistem.</div>

                    {{-- PERBAIKAN: Menambahkan form untuk data pemohon utama --}}
                    <h5 class="font-weight-bold mt-4">Data Pemohon (Yang Akan Tercantum di Surat)</h5>
                    <div class="form-group">
                        <label for="nama_pemohon">Nama Pemohon</label>
                        {{-- PERBAIKAN: Mengubah name="nama_lengkap" menjadi name="nama_pemohon" --}}
                        <input type="text" class="form-control" name="nama_pemohon" value="{{ old('nama_pemohon', $permohonan->masyarakat->nama_lengkap) }}" required>
                    </div>
                     <div class="form-group">
                        <label for="nik_pemohon">NIK Pemohon</label>
                        {{-- PERBAIKAN: Mengubah name="nik" menjadi name="nik_pemohon" --}}
                        <input type="text" class="form-control" name="nik_pemohon" value="{{ old('nik_pemohon', $permohonan->masyarakat->nik) }}" required>
                    </div>

                    <hr>
                    <h5 class="font-weight-bold mt-4">Data Anak/Orang Tua Terkait (Jika Ada)</h5>
                    <div class="form-group">
                        <label>Nama Terkait</label>
                        <input type="text" class="form-control" name="nama_terkait" value="{{ old('nama_terkait', $permohonan->nama_terkait) }}">
                    </div>
                    <div class="form-group">
                        <label>NIK Terkait</label>
                        <input type="text" class="form-control" name="nik_terkait" value="{{ old('nik_terkait', $permohonan->nik_terkait) }}">
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir Terkait</label>
                        <input type="text" class="form-control" name="tempat_lahir_terkait" value="{{ old('tempat_lahir_terkait', $permohonan->tempat_lahir_terkait) }}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir Terkait</label>
                        <input type="date" class="form-control" name="tanggal_lahir_terkait" value="{{ old('tanggal_lahir_terkait', $permohonan->tanggal_lahir_terkait ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_terkait)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan/Sekolah Terkait</label>
                        <input type="text" class="form-control" name="pekerjaan_atau_sekolah_terkait" value="{{ old('pekerjaan_atau_sekolah_terkait', $permohonan->pekerjaan_atau_sekolah_terkait) }}">
                    </div>
                    <div class="form-group">
                        <label>Alamat Terkait</label>
                        <textarea class="form-control" name="alamat_terkait" rows="3">{{ old('alamat_terkait', $permohonan->alamat_terkait) }}</textarea>
                    </div>

                    <hr>
                    <h5 class="font-weight-bold mt-4">Keperluan Surat</h5>
                    <div class="form-group">
                        <label for="keperluan_surat">Tulis ulang atau perbaiki redaksi keperluan surat</label>
                        <textarea class="form-control" name="keperluan_surat" id="keperluan_surat" rows="3" required>{{ old('keperluan_surat', $permohonan->keperluan_surat) }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')"><i class="fas fa-print"></i> Buat Surat Final & Selesaikan</button>
                    <a href="{{ route('petugas.permohonan-sk-tidak-mampu.show', $permohonan->id) }}" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                </div>
            </div>
        </div>
    </div>
</form> {{-- PERBAIKAN: Menambahkan tag penutup </form> yang benar di sini --}}
@endsection
