@extends('layouts.app')

@section('title', 'Detail Permohonan Lainnya')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan Lainnya #{{ $permohonan->id }}</h1>

@if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="row">
    {{-- KOLOM KIRI: DETAIL PERMOHONAN --}}
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data Permohonan dari Masyarakat</h6></div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nama Pemohon</dt><dd class="col-sm-8">{{ $permohonan->masyarakat->nama_lengkap ?? '-' }}</dd>
                    <dt class="col-sm-4">NIK Pemohon</dt><dd class="col-sm-8">{{ $permohonan->masyarakat->nik ?? '-' }}</dd>
                </dl>
                <hr>
                <dl>
                    <dt>Judul Permohonan</dt>
                    <dd>{{ $permohonan->judul_permohonan ?? '-' }}</dd>

                    <dt>Keperluan</dt>
                    <dd>{{ $permohonan->keperluan ?? '-' }}</dd>

                    <dt>Rincian Lengkap dari Pemohon</dt>
                    <dd>
                        <div class="p-3 bg-light border rounded mt-1">
                            {!! nl2br(e($permohonan->rincian_pemohon ?? 'Tidak ada rincian.')) !!}
                        </div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: STATUS, AKSI, DAN LAMPIRAN --}}
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                @if ($permohonan->status == 'pending') <span class="badge badge-warning">Pending</span>
                @elseif ($permohonan->status == 'selesai') <span class="badge badge-success">Selesai</span>
                @elseif ($permohonan->status == 'ditolak') <span class="badge badge-danger">Ditolak</span>
                @endif
            </div>
            <div class="card-body">
                @if($permohonan->status == 'pending')
                    <p>Periksa rincian permohonan. Jika data valid dan bisa diproses, klik "Buat Surat" untuk melanjutkan ke halaman penulisan surat.</p>
                    <a href="{{ route('petugas.permohonan-lainnya.create-surat', $permohonan->id) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-pen-alt"></i> Buat Surat
                    </a>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-times"></i> Tolak Permohonan</button>
                
                @elseif($permohonan->status == 'selesai')
                    <p>Surat telah dibuat pada {{ $permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->format('d F Y, H:i') : '' }}.</p>
                    <a href="{{ route('petugas.permohonan-lainnya.download-final', $permohonan->id) }}" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh Surat</a>

                @elseif($permohonan->status == 'ditolak')
                    <p>Permohonan ini telah ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"{{ $permohonan->catatan_penolakan }}"</em></blockquote>
                @endif
                
                <a href="{{ route('petugas.permohonan-lainnya.index') }}" class="btn btn-secondary btn-block mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Lampiran dari Pemohon
                        @if($permohonan->lampiran)
                            <a href="{{ asset('storage/' . $permohonan->lampiran) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                        @else
                            <span class="badge badge-secondary">Tidak Ada</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="tolakModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('petugas.permohonan-lainnya.tolak', $permohonan->id) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Tolak Permohonan</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan">Alasan Penolakan:</label>
                        <textarea class="form-control" name="catatan_penolakan" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Ya, Tolak</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
