@extends('layouts.app')

@section('title', 'Detail Akun Masyarakat: ' . $masyarakat->nama_lengkap)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Akun: {{ $masyarakat->nama_lengkap }}</h1>
        <a href="{{ route('petugas.masyarakat.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama Lengkap:</strong> {{ $masyarakat->nama_lengkap }}</p>
                    <p><strong>NIK:</strong> {{ $masyarakat->nik }}</p>
                    <p><strong>Nomor HP:</strong> {{ $masyarakat->nomor_hp ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $masyarakat->email ?? '-' }}</p>
                    <p><strong>Status Akun:</strong> 
                        @if ($masyarakat->status_akun == 'pending_verification')
                            <span class="badge badge-warning">Pending Verifikasi</span>
                        @elseif ($masyarakat->status_akun == 'active')
                            <span class="badge badge-success">Aktif</span>
                        @elseif ($masyarakat->status_akun == 'inactive')
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @elseif ($masyarakat->status_akun == 'rejected')
                            <span class="badge badge-danger">Ditolak</span>
                        @else
                            <span class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $masyarakat->status_akun)) }}</span>
                        @endif
                    </p>
                    @if($masyarakat->status_akun == 'rejected' && $masyarakat->catatan_verifikasi)
                        <p><strong>Catatan Penolakan:</strong> {{ $masyarakat->catatan_verifikasi }}</p>
                    @elseif($masyarakat->catatan_verifikasi)
                         <p><strong>Catatan Verifikasi/Status:</strong> {{ $masyarakat->catatan_verifikasi }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p><strong>Tempat Lahir:</strong> {{ $masyarakat->tempat_lahir ?? '-' }}</p>
                    <p><strong>Tanggal Lahir:</strong> {{ $masyarakat->tanggal_lahir ? $masyarakat->tanggal_lahir->format('d F Y') : '-' }}</p>
                    <p><strong>Jenis Kelamin:</strong> {{ $masyarakat->jenis_kelamin ?? '-' }}</p>
                    <p><strong>Alamat Lengkap:</strong> {{ $masyarakat->alamat_lengkap ?? '-' }}</p>
                    <p><strong>RT/RW:</strong> {{ $masyarakat->rt ?? '-' }}/{{ $masyarakat->rw ?? '-' }}</p>
                    <p><strong>Dusun/Lingkungan:</strong> {{ $masyarakat->dusun_atau_lingkungan ?? '-' }}</p>
                    <p><strong>Agama:</strong> {{ $masyarakat->agama ?? '-' }}</p>
                    <p><strong>Status Perkawinan:</strong> {{ $masyarakat->status_perkawinan ?? '-' }}</p>
                    <p><strong>Pekerjaan:</strong> {{ $masyarakat->pekerjaan ?? '-' }}</p>
                </div>
            </div>
            @if($masyarakat->foto_ktp)
            <hr>
            <h5>Foto KTP:</h5>
            <img src="{{ Storage::url($masyarakat->foto_ktp) }}" alt="Foto KTP {{ $masyarakat->nama_lengkap }}" class="img-thumbnail" style="max-width: 400px;">
            @endif
        </div>
        <div class="card-footer">
            <p class="text-muted mb-0">Terdaftar pada: {{ $masyarakat->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
</div>
@endsection
