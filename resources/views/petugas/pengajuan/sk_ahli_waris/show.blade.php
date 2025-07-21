@extends('layouts.app')

@section('title', 'Detail Permohonan SK Ahli Waris')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Permohonan #{{ $permohonan->id }}</h1>
    <a href="{{ route('petugas.permohonan-sk-ahli-waris.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Daftar
    </a>
</div>

{{-- Notifikasi --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

<div class="row">
    {{-- KOLOM KIRI: DATA PERMOHONAN --}}
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan</h6></div>
            <div class="card-body">
                <h5 class="font-weight-bold">Data Pewaris</h5>
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $permohonan->nama_pewaris ?? '-' }}</dd>
                    <dt class="col-sm-4">NIK</dt><dd class="col-sm-8">{{ $permohonan->nik_pewaris ?? '-' }}</dd>
                    <dt class="col-sm-4">Tempat / Tgl Lahir</dt><dd class="col-sm-8">{{ $permohonan->tempat_lahir_pewaris ?? '-' }}, {{ $permohonan->tanggal_lahir_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pewaris)->isoFormat('D MMMM YYYY') : '-' }}</dd>
                    <dt class="col-sm-4">Tanggal Meninggal</dt><dd class="col-sm-8">{{ $permohonan->tanggal_meninggal_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_meninggal_pewaris)->isoFormat('D MMMM YYYY') : '-' }}</dd>
                    <dt class="col-sm-4">Alamat Terakhir</dt><dd class="col-sm-8">{{ $permohonan->alamat_pewaris ?? '-' }}</dd>
                </dl>
                <hr>
                <h5 class="font-weight-bold mt-4">Daftar Ahli Waris</h5>
                @php
                    $ahliWarisList = is_string($permohonan->daftar_ahli_waris) ? json_decode($permohonan->daftar_ahli_waris, true) : ($permohonan->daftar_ahli_waris ?? []);
                @endphp
                @if(!empty($ahliWarisList) && is_array($ahliWarisList))
                    <table class="table table-bordered table-sm mt-3">
                        <thead><tr><th>Nama</th><th>NIK</th><th>Hubungan</th><th>Alamat</th></tr></thead>
                        <tbody>
                            @foreach($ahliWarisList as $ahli)
                            <tr>
                                <td>{{ $ahli['nama'] ?? '-' }}</td>
                                <td>{{ $ahli['nik'] ?? '-' }}</td>
                                <td>{{ $ahli['hubungan'] ?? '-' }}</td>
                                <td>{{ $ahli['alamat'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p><em>Tidak ada data ahli waris yang diinput.</em></p>
                @endif
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: STATUS, AKSI, DAN LAMPIRAN --}}
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Status & Aksi</h6>
                @include('layouts.partials.status_badge', ['status' => $permohonan->status])
            </div>
            <div class="card-body">
                @if($permohonan->status == 'pending')
                    <p class="text-info"><i class="fas fa-info-circle fa-sm"></i> Periksa data dan lampiran. Jika valid, klik "Verifikasi". Jika perlu perbaikan, klik "Kembalikan untuk Revisi".</p>
                    <hr>
                    <form action="{{ route('petugas.permohonan-sk-ahli-waris.verifikasi', $permohonan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Anda yakin data dan lampiran sudah valid?')"><i class="fas fa-check-circle"></i> Verifikasi & Lanjutkan</button>
                    </form>
                    <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#tolakModal"><i class="fas fa-undo"></i> Kembalikan untuk Revisi</button>
                @elseif($permohonan->status == 'diterima')
                    <p>Permohonan telah diverifikasi. Klik tombol di bawah untuk memproses dan mengedit data sebelum membuat surat final.</p>
                    <a href="{{ route('petugas.permohonan-sk-ahli-waris.edit-surat', $permohonan->id) }}" class="btn btn-primary btn-block mb-2"><i class="fas fa-edit"></i> Proses & Edit Surat</a>
                @elseif($permohonan->status == 'membutuhkan_revisi')
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold">Menunggu Revisi dari Pengguna</h6>
                        <p class="mb-0 small">Permohonan telah dikembalikan untuk diperbaiki. Anda akan menerima notifikasi jika pengguna sudah mengirimkan revisi.</p>
                    </div>
                    <h6 class="font-weight-bold">Catatan Perbaikan:</h6>
                    <blockquote class="blockquote-footer"><em>"{{ $permohonan->catatan_penolakan }}"</em></blockquote>
                @elseif($permohonan->status == 'selesai')
                    <p>Proses selesai pada <strong>{{ $permohonan->tanggal_selesai_proses ? \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses)->isoFormat('D MMMM YYYY') : 'N/A' }}</strong>.</p>
                    <a href="{{ route('petugas.permohonan-sk-ahli-waris.download-final', $permohonan->id) }}" class="btn btn-success btn-block"><i class="fas fa-download"></i> Unduh Dokumen Final</a>
                @elseif($permohonan->status == 'ditolak')
                    <div class="alert alert-danger"><h6 class="font-weight-bold">Permohonan Ditolak</h6></div>
                    <h6 class="font-weight-bold">Alasan Penolakan:</h6>
                    <blockquote class="blockquote-footer"><em>"{{ $permohonan->catatan_penolakan }}"</em></blockquote>
                @endif
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @php
                        $lampiran = [
                            'file_ktp_pemohon' => 'KTP Pemohon',
                            'file_kk_pemohon' => 'Kartu Keluarga Pemohon',
                            'file_ktp_ahli_waris' => 'KTP Ahli Waris',
                            'file_kk_ahli_waris' => 'Kartu Keluarga Ahli Waris',
                            'surat_keterangan_kematian' => 'Surat Kematian',
                            'surat_pengantar_rt_rw' => 'Surat Pengantar RT/RW',
                        ];
                    @endphp
                    @foreach ($lampiran as $field => $label)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div><i class="fas fa-file-alt text-gray-500 mr-2"></i> {{ $label }}</div>
                        @if($permohonan->$field)
                            <a href="{{ asset('storage/' . $permohonan->$field) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye fa-sm"></i> Lihat</a>
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

{{-- Modal Kembalikan untuk Revisi --}}
<div class="modal fade" id="tolakModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('petugas.permohonan-sk-ahli-waris.tolak', $permohonan->id) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Kembalikan Permohonan untuk Revisi</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan"><strong>Tulis Catatan Perbaikan (Wajib):</strong></label>
                        <textarea class="form-control" name="catatan_penolakan" rows="4" required placeholder="Contoh: Scan KTP salah satu ahli waris buram, mohon unggah ulang."></textarea>
                        <small class="form-text text-muted">Catatan ini akan ditampilkan kepada pengguna.</small>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Ya, Kembalikan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
