@extends('layouts.app')

@section('title', 'Detail & Proses Permohonan SK Kelahiran')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Detail Permohonan #{{ $permohonan->id }}</h1>

{{-- Notifikasi --}}
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    {{-- KOLOM KIRI: MENAMPILKAN DATA READ-ONLY DARI MASYARAKAT --}}
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan oleh Masyarakat</h6>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Anak</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama Anak</dt><dd class="col-sm-8">{{ $permohonan->nama_anak ?? '-' }}</dd>
                    <dt class="col-sm-4">Jenis Kelamin</dt><dd class="col-sm-8">{{ $permohonan->jenis_kelamin_anak ?? '-' }}</dd>
                    <dt class="col-sm-4">Tempat, Tgl Lahir</dt><dd class="col-sm-8">{{ $permohonan->tempat_lahir_anak ?? '-' }}, {{ $permohonan->tanggal_lahir_anak ? $permohonan->tanggal_lahir_anak->format('d F Y') : '-' }}</dd>
                    <dt class="col-sm-4">Agama</dt><dd class="col-sm-8">{{ $permohonan->agama_anak ?? '-' }}</dd>
                    <dt class="col-sm-4">Alamat Anak</dt><dd class="col-sm-8">{{ $permohonan->alamat_anak ?? '-' }}</dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Data Orang Tua</h5>
                <dl class="row mt-3">
                    <dt class="col-sm-4">Nama Ayah</dt><dd class="col-sm-8">{{ $permohonan->nama_ayah ?? '-' }}</dd>
                    <dt class="col-sm-4">NIK Ayah</dt><dd class="col-sm-8">{{ $permohonan->nik_ayah ?? '-' }}</dd>
                    <dt class="col-sm-4">Nama Ibu</dt><dd class="col-sm-8">{{ $permohonan->nama_ibu ?? '-' }}</dd>
                    <dt class="col-sm-4">NIK Ibu</dt><dd class="col-sm-8">{{ $permohonan->nik_ibu ?? '-' }}</dd>
                    <dt class="col-sm-4">No. Buku Nikah</dt><dd class="col-sm-8">{{ $permohonan->no_buku_nikah ?? '-' }}</dd>
                </dl>
                
                {{-- PERBAIKAN: Menambahkan bagian untuk menampilkan catatan pemohon --}}
                <hr>
                <h5 class="mt-4 font-weight-bold">Catatan dari Pemohon</h5>
                <p><em>{{ $permohonan->catatan_pemohon ?? 'Tidak ada catatan.' }}</em></p>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: STATUS, AKSI, DAN LAMPIRAN --}}
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                @if ($permohonan->status == 'pending') <span class="badge badge-warning">Pending</span>
                @elseif (in_array($permohonan->status, ['diterima', 'diproses'])) <span class="badge badge-info">{{ ucfirst($permohonan->status) }}</span>
                @elseif ($permohonan->status == 'selesai') <span class="badge badge-success">Selesai</span>
                @elseif ($permohonan->status == 'ditolak') <span class="badge badge-danger">Ditolak</span>
                @endif
            </div>
            <div class="card-body">
                @if($permohonan->status == 'pending')
                    <p>Periksa dokumen lampiran. Jika valid, klik "Verifikasi" untuk melanjutkan.</p>
                    <form action="{{ route('petugas.permohonan-sk-kelahiran.verifikasi', $permohonan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data valid?')">
                            <i class="fas fa-check"></i> Verifikasi Permohonan
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#tolakModal">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                
                {{-- PERBAIKAN: Tombol 'Buat Surat' sekarang langsung memanggil route 'selesaikan' --}}
                @elseif($permohonan->status == 'diterima')
                    <p>Permohonan telah diverifikasi. Klik tombol di bawah untuk membuat surat secara otomatis.</p>
                    <form action="{{ route('petugas.permohonan-sk-kelahiran.selesaikan', $permohonan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block" onclick="return confirm('Anda akan membuat surat berdasarkan data yang sudah ada. Lanjutkan?')">
                            <i class="fas fa-print"></i> Buat Surat & Selesaikan
                        </button>
                    </form>
                
                @elseif($permohonan->status == 'selesai')
                    <p>Surat telah dibuat pada {{ $permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->format('d F Y, H:i') : '' }}.</p>
                    <a href="{{ route('petugas.permohonan-sk-kelahiran.download-final', $permohonan->id) }}" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh Surat</a>

                @elseif($permohonan->status == 'ditolak')
                    <p>Permohonan ini telah ditolak dengan alasan:</p>
                    <blockquote class="blockquote-footer"><em>"{{ $permohonan->catatan_penolakan }}"</em></blockquote>
                @endif
                
                <a href="{{ route('petugas.permohonan-sk-kelahiran.index') }}" class="btn btn-secondary btn-block mt-3">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @php
                        $lampiran = [
                            'file_kk' => 'Kartu Keluarga',
                            'file_ktp' => 'KTP Orang Tua',
                            'surat_pengantar_rt_rw' => 'Surat Pengantar',
                            'surat_nikah_orangtua' => 'Buku Nikah',
                            'surat_keterangan_kelahiran' => 'Ket. Kelahiran Bidan/RS'
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
            <form action="{{ route('petugas.permohonan-sk-kelahiran.tolak', $permohonan->id) }}" method="POST">
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
