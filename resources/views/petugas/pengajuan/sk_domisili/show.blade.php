@extends('layouts.app')

@section('title', 'Detail Permohonan SK Domisili')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan Surat Keterangan Domisili #{{ $permohonan->id }}</h1>

@if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="row">
    {{-- KOLOM KIRI: DETAIL DATA PERMOHONAN --}}
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan Masyarakat</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Nama Pemohon/Lembaga</dt>
                    <dd class="col-sm-7">{{ $permohonan->nama_pemohon_atau_lembaga ?? '-' }}</dd>
                    
                    <dt class="col-sm-5">NIK Pemohon</dt>
                    <dd class="col-sm-7">{{ $permohonan->nik_pemohon ?? '-' }}</dd>
                    
                    <dt class="col-sm-5">Alamat Domisili</dt>
                    <dd class="col-sm-7">{{ $permohonan->alamat_lengkap_domisili ?? '-' }}</dd>

                    <dt class="col-sm-5">RT/RW</dt>
                    <dd class="col-sm-7">{{ $permohonan->rt_domisili ?? '-' }} / {{ $permohonan->rw_domisili ?? '-' }}</dd>

                    <dt class="col-sm-5">Keperluan</dt>
                    <dd class="col-sm-7">{{ $permohonan->keperluan_domisili ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: STATUS, AKSI, DAN LAMPIRAN --}}
    <div class="col-lg-5">
        {{-- CARD UNTUK STATUS & AKSI --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                @if ($permohonan->status == 'pending') <span class="badge badge-warning">Pending</span>
                @elseif ($permohonan->status == 'diterima') <span class="badge badge-info">Diterima</span>
                @elseif ($permohonan->status == 'selesai') <span class="badge badge-success">Selesai</span>
                @elseif ($permohonan->status == 'ditolak') <span class="badge badge-danger">Ditolak</span>
                @endif
            </div>
            <div class="card-body">
                @if($permohonan->status == 'pending')
                    <p>Periksa lampiran. Jika valid, klik "Verifikasi" untuk melanjutkan.</p>
                    <form action="{{ route('petugas.permohonan-sk-domisili.verifikasi', $permohonan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data valid?')"><i class="fas fa-check"></i> Verifikasi Permohonan</button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-times"></i> Tolak</button>
                
                @elseif($permohonan->status == 'diterima')
                    <p>Permohonan telah diverifikasi. Klik tombol di bawah untuk memproses dan mengedit data sebelum membuat surat final.</p>
                    <a href="{{ route('petugas.permohonan-sk-domisili.edit-surat', $permohonan->id) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit"></i> Proses & Edit Surat
                    </a>
                
                @elseif($permohonan->status == 'selesai')
                    <p>Surat telah dibuat pada {{ $permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->format('d F Y, H:i') : '' }}.</p>
                    <a href="{{ route('petugas.permohonan-sk-domisili.download-final', $permohonan->id) }}" class="btn btn-success btn-block">
                        <i class="fas fa-download"></i> Unduh Surat
                    </a>

                @elseif($permohonan->status == 'ditolak')
                    <p>Permohonan ini telah ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"{{ $permohonan->catatan_penolakan }}"</em></blockquote>
                @endif
                
                <a href="{{ route('petugas.permohonan-sk-domisili.index') }}" class="btn btn-secondary btn-block mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
            </div>
        </div>

        {{-- CARD UNTUK DOKUMEN LAMPIRAN --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @php
                        $lampiran = [
                            'file_kk' => 'Kartu Keluarga',
                            'file_ktp' => 'KTP Pemohon',
                            'file_surat_pengantar_rt_rw' => 'Surat Pengantar RT/RW'
                        ];
                    @endphp
                    @foreach ($lampiran as $field => $label)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
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
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="tolakModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('petugas.permohonan-sk-domisili.tolak', $permohonan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Permohonan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan">Alasan Penolakan:</label>
                        <textarea class="form-control" name="catatan_penolakan" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
