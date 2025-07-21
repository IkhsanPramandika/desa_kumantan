@extends('layouts.app')

@section('title', 'Proses Surat Keterangan Domisili')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses & Edit Surat Keterangan Domisili</h1>
    <a href="{{ route('petugas.permohonan-sk-domisili.show', $permohonan->id) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Detail
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    {{-- KOLOM KIRI: DATA REFERENSI (READ-ONLY) --}}
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Data Referensi dari Pemohon</h6>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Pemohon/Lembaga</h5>
                <dl class="row">
                    <dt class="col-sm-5">Nama</dt><dd class="col-sm-7">{{ $permohonan->nama_pemohon_atau_lembaga ?? '-' }}</dd>
                    <dt class="col-sm-5">NIK</dt><dd class="col-sm-7">{{ $permohonan->nik_pemohon ?? '-' }}</dd>
                    <dt class="col-sm-5">Alamat Domisili</dt><dd class="col-sm-7">{{ $permohonan->alamat_lengkap_domisili ?? '-' }}</dd>
                    <dt class="col-sm-5">RT/RW</dt><dd class="col-sm-7">{{ $permohonan->rt_domisili ?? '-' }} / {{ $permohonan->rw_domisili ?? '-' }}</dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Keperluan Surat</h5>
                <p class="text-muted"><em>{{ $permohonan->keperluan_domisili ?? 'Tidak ada keterangan.' }}</em></p>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: FORM ISIAN PETUGAS (EDITABLE) --}}
    <div class="col-lg-7">
        <form action="{{ route('petugas.permohonan-sk-domisili.selesaikan', $permohonan->id) }}" method="POST">
            @csrf
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i>Form Isian Surat oleh Petugas</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final. Nomor surat akan dibuat secara otomatis.</p>
                    
                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Data Pemohon/Lembaga</h5>
                    <div class="form-group">
                        <label>Nama Pemohon atau Lembaga</label>
                        <input type="text" class="form-control" name="nama_pemohon_atau_lembaga" value="{{ old('nama_pemohon_atau_lembaga', $permohonan->nama_pemohon_atau_lembaga) }}" required>
                    </div>
                    <div class="form-group">
                        <label>NIK Pemohon (kosongkan jika lembaga)</label>
                        <input type="text" class="form-control" name="nik_pemohon" value="{{ old('nik_pemohon', $permohonan->nik_pemohon) }}">
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap Domisili</label>
                        <textarea class="form-control" name="alamat_lengkap_domisili" rows="3" required>{{ old('alamat_lengkap_domisili', $permohonan->alamat_lengkap_domisili) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>RT Domisili</label>
                                <input type="text" class="form-control" name="rt_domisili" value="{{ old('rt_domisili', $permohonan->rt_domisili) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label>RW Domisili</label>
                                <input type="text" class="form-control" name="rw_domisili" value="{{ old('rw_domisili', $permohonan->rw_domisili) }}" required>
                            </div>
                        </div>
                    </div>

                    <h5 class="font-weight-bold mt-4 border-bottom pb-2 mb-3">Keperluan Surat</h5>
                    <div class="form-group">
                        <label for="keperluan_domisili">Tulis ulang atau perbaiki redaksi keperluan surat</label>
                        <textarea class="form-control" name="keperluan_domisili" id="keperluan_domisili" rows="3" required>{{ old('keperluan_domisili', $permohonan->keperluan_domisili) }}</textarea>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('petugas.permohonan-sk-domisili.show', $permohonan->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')">
                        <i class="fas fa-print"></i> Buat Surat Final & Selesaikan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
