@extends('layouts.app')

@section('title', 'Detail Permohonan SK Perkawinan')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan Surat Keterangan Perkawinan #{{ $permohonan->id }}</h1>

@if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="row">
    {{-- KOLOM KIRI: DATA MEMPELAI --}}
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan Masyarakat</h6></div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Mempelai Pria</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $permohonan->nama_pria ?? '-' }}</dd>
                    <dt class="col-sm-4">NIK</dt><dd class="col-sm-8">{{ $permohonan->nik_pria ?? '-' }}</dd>
                    <dt class="col-sm-4">Tempat, Tgl Lahir</dt><dd class="col-sm-8">{{ $permohonan->tempat_lahir_pria ?? '-' }}, {{ $permohonan->tanggal_lahir_pria ? $permohonan->tanggal_lahir_pria->format('d F Y') : '-' }}</dd>
                    <dt class="col-sm-4">Alamat</dt><dd class="col-sm-8">{{ $permohonan->alamat_pria ?? '-' }}</dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold">Data Mempelai Wanita</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $permohonan->nama_wanita ?? '-' }}</dd>
                    <dt class="col-sm-4">NIK</dt><dd class="col-sm-8">{{ $permohonan->nik_wanita ?? '-' }}</dd>
                    <dt class="col-sm-4">Tempat, Tgl Lahir</dt><dd class="col-sm-8">{{ $permohonan->tempat_lahir_wanita ?? '-' }}, {{ $permohonan->tanggal_lahir_wanita ? $permohonan->tanggal_lahir_wanita->format('d F Y') : '-' }}</dd>
                    <dt class="col-sm-4">Alamat</dt><dd class="col-sm-8">{{ $permohonan->alamat_wanita ?? '-' }}</dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold">Detail Akad Nikah</h5>
                <dl class="row">
                    <dt class="col-sm-4">Tanggal</dt><dd class="col-sm-8">{{ $permohonan->tanggal_akad_nikah ? $permohonan->tanggal_akad_nikah->format('d F Y') : '-' }}</dd>
                    <dt class="col-sm-4">Tempat</dt><dd class="col-sm-8">{{ $permohonan->tempat_akad_nikah ?? '-' }}</dd>
                </dl>

                {{-- PERBAIKAN: Menambahkan bagian Catatan dari Pemohon --}}
                <hr>
                <h5 class="mt-4 font-weight-bold">Catatan dari Pemohon</h5>
                <p><em>{{ $permohonan->catatan_pemohon ?? 'Tidak ada catatan.' }}</em></p>

            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: STATUS, AKSI, DAN LAMPIRAN --}}
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                @if ($permohonan->status == 'pending') <span class="badge badge-warning">Pending</span>
                @elseif (in_array($permohonan->status, ['diterima', 'diproses'])) <span class="badge badge-info">{{ ucfirst($permohonan->status) }}</span>
                @elseif ($permohonan->status == 'selesai') <span class="badge badge-success">Selesai</span>
                @elseif ($permohonan->status == 'ditolak') <span class="badge badge-danger">Ditolak</span>
                @endif
            </div>
            <div class="card-body">
                @if($permohonan->status == 'pending')
                    <p>Periksa lampiran. Jika valid, klik "Verifikasi" untuk melanjutkan.</p>
                    <form action="{{ route('petugas.permohonan-sk-perkawinan.verifikasi', $permohonan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data valid?')"><i class="fas fa-check"></i> Verifikasi</button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-times"></i> Tolak</button>
                
                @elseif(in_array($permohonan->status, ['diterima', 'diproses']))
                    <p>Permohonan diverifikasi. Klik tombol di bawah untuk membuat surat secara otomatis.</p>
                    <form action="{{ route('petugas.permohonan-sk-perkawinan.selesaikan', $permohonan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block" onclick="return confirm('Anda akan membuat surat berdasarkan data yang sudah ada. Lanjutkan?')">
                            <i class="fas fa-print"></i> Buat Surat & Selesaikan
                        </button>
                    </form>
                
                @elseif($permohonan->status == 'selesai')
                    <p>Surat telah dibuat. Anda bisa mengunduhnya di bawah ini.</p>
                    <a href="{{ route('petugas.permohonan-sk-perkawinan.download-final', $permohonan->id) }}" class="btn btn-success btn-block mb-2"><i class="fas fa-download"></i> Unduh Surat</a>
                
                @elseif($permohonan->status == 'ditolak')
                    <p>Permohonan ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"{{ $permohonan->catatan_penolakan }}"</em></blockquote>
                @endif
                
                <a href="{{ route('petugas.permohonan-sk-perkawinan.index') }}" class="btn btn-secondary btn-block mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
            </div>
        </div>

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
                            'akta_penceraian' => 'Akta Perceraian (jika ada)',
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
            <form action="{{ route('petugas.permohonan-sk-perkawinan.tolak', $permohonan->id) }}" method="POST">
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
