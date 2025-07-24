{{-- ================================================================= --}}
{{-- LOKASI: resources/views/petugas/masyarakat/show.blade.php --}}
{{-- ================================================================= --}}
@extends('layouts.app')

@section('title', 'Detail Akun: ' . $masyarakat->nama_lengkap)

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Akun: {{ $masyarakat->nama_lengkap }}</h1>
    <a href="{{ route('petugas.masyarakat.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar
    </a>
</div>

{{-- Notifikasi --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

<div class="row">
    {{-- KOLOM KIRI: INFO UTAMA & AKSI --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img class="img-profile rounded-circle mb-3" src="{{ asset('sbadmin/img/undraw_profile.svg') }}" style="max-width: 150px;">
                <h5 class="font-weight-bold">{{ $masyarakat->nama_lengkap }}</h5>
                <p class="text-muted mb-1">NIK: {{ $masyarakat->nik }}</p>
                <p class="text-muted">{{ $masyarakat->email ?? $masyarakat->nomor_hp }}</p>
                @include('layouts.partials.akun_status_badge', ['status' => $masyarakat->status_akun])
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Aksi Petugas</h6></div>
            <div class="card-body">
                @if($masyarakat->status_akun == 'pending_verification')
                    <p class="text-info small"><i class="fas fa-info-circle"></i> Periksa data dan KTP sebelum memverifikasi akun.</p>
                    <button class="btn btn-success btn-block" data-toggle="modal" data-target="#verifikasiModal"><i class="fas fa-user-check"></i> Verifikasi & Aktifkan Akun</button>
                    <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-user-times"></i> Tolak Pendaftaran</button>
                @elseif($masyarakat->status_akun == 'active')
                     <button class="btn btn-warning btn-block" data-toggle="modal" data-target="#nonaktifkanModal"><i class="fas fa-user-slash"></i> Nonaktifkan Akun</button>
                @elseif(in_array($masyarakat->status_akun, ['inactive', 'rejected']))
                     <button class="btn btn-success btn-block" data-toggle="modal" data-target="#verifikasiModal"><i class="fas fa-user-check"></i> Aktifkan Kembali Akun</button>
                @endif
                <hr>
                <a href="{{ route('petugas.masyarakat.showResetPasswordFormByPetugas', $masyarakat->id) }}" class="btn btn-outline-primary btn-block"><i class="fas fa-key"></i> Reset Password</a>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: DETAIL LENGKAP --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="data-diri-tab" data-toggle="tab" href="#data-diri" role="tab">Data Diri</a></li>
                    <li class="nav-item"><a class="nav-link" id="ktp-tab" data-toggle="tab" href="#ktp" role="tab">Dokumen KTP</a></li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="data-diri" role="tabpanel">
                        <div class="p-3">
                            <dl class="row">
                                <dt class="col-sm-4">Tempat Lahir</dt><dd class="col-sm-8">{{ $masyarakat->tempat_lahir ?? '-' }}</dd>
                                <dt class="col-sm-4">Tanggal Lahir</dt><dd class="col-sm-8">{{ $masyarakat->tanggal_lahir ? \Carbon\Carbon::parse($masyarakat->tanggal_lahir)->isoFormat('D MMMM YYYY') : '-' }}</dd>
                                <dt class="col-sm-4">Jenis Kelamin</dt><dd class="col-sm-8">{{ $masyarakat->jenis_kelamin ?? '-' }}</dd>
                                <dt class="col-sm-4">Agama</dt><dd class="col-sm-8">{{ $masyarakat->agama ?? '-' }}</dd>
                                <dt class="col-sm-4">Pekerjaan</dt><dd class="col-sm-8">{{ $masyarakat->pekerjaan ?? '-' }}</dd>
                                <dt class="col-sm-4">Status Perkawinan</dt><dd class="col-sm-8">{{ $masyarakat->status_perkawinan ?? '-' }}</dd>
                                <hr class="col-12">
                                <dt class="col-sm-4">Alamat Lengkap</dt><dd class="col-sm-8">{{ $masyarakat->alamat_lengkap ?? '-' }}</dd>
                                <dt class="col-sm-4">RT/RW</dt><dd class="col-sm-8">{{ $masyarakat->rt ?? '-' }}/{{ $masyarakat->rw ?? '-' }}</dd>
                                <dt class="col-sm-4">Dusun/Lingkungan</dt><dd class="col-sm-8">{{ $masyarakat->dusun_atau_lingkungan ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ktp" role="tabpanel">
                        <div class="p-3">
                            @if($masyarakat->foto_ktp)
                                <a href="{{ Storage::url($masyarakat->foto_ktp) }}" target="_blank">
                                    <img src="{{ Storage::url($masyarakat->foto_ktp) }}" alt="Foto KTP {{ $masyarakat->nama_lengkap }}" class="img-fluid rounded">
                                </a>
                            @else
                                <div class="text-center text-muted my-5"><i class="fas fa-image fa-3x d-block mb-2"></i> Foto KTP tidak diunggah.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('layouts.partials.modals', ['item' => $masyarakat])
@endsection