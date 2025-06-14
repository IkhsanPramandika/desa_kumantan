@extends('layouts.app')

@section('title', 'Riwayat Notifikasi')

@push('styles')
{{-- CSS Tambahan untuk efek hover --}}
<style>
    .notification-card {
        transition: transform .2s ease-out, box-shadow .2s ease-out;
    }
    .notification-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,.12)!important;
    }
    .notification-card .stretched-link::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
        content: "";
    }
</style>
@endpush

@section('content')
<h1 class="h3 mb-2 text-gray-800">Riwayat Notifikasi</h1>
<p class="mb-4">Lihat semua riwayat notifikasi permohonan yang masuk ke sistem.</p>

{{-- KARTU FILTER --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Riwayat</h6>
        <a href="{{ route('petugas.notifikasi.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-sync-alt fa-sm"></i> Reset Filter</a>
    </div>
    <div class="card-body">
        <form action="{{ route('petugas.notifikasi.index') }}" method="GET">
            <div class="form-row align-items-end">
                <div class="col-md-4 mb-3">
                    <label for="search">Cari Nama Pemohon</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Contoh: Budi" value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">Status Permohonan</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="jenis_surat">Jenis Surat</label>
                    <select class="form-control" id="jenis_surat" name="jenis_surat">
                        <option value="">-- Semua Jenis Surat --</option>
                        @isset($jenisSuratOptions)
                            @foreach($jenisSuratOptions as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis_surat') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search fa-sm"></i> Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- DAFTAR NOTIFIKASI BERBASIS KARTU --}}
<div>
    @forelse ($semuaNotifikasi as $notif)
        @php
            $status = $notif->data['status'] ?? 'pending';
            // PERBAIKAN 1: Ikon diubah menjadi statis (ikon surat)
            $iconClass = 'fas fa-envelope-open-text'; 
            $bgColorClass = 'bg-primary';
            $badgeColorClass = 'badge-info';

            switch ($status) {
                case 'selesai':
                    $bgColorClass = 'bg-success';
                    $badgeColorClass = 'badge-success';
                    break;
                case 'ditolak':
                    $bgColorClass = 'bg-danger';
                    $badgeColorClass = 'badge-danger';
                    break;
                case 'pending':
                    $bgColorClass = 'bg-warning';
                    $badgeColorClass = 'badge-warning';
                    break;
            }
        @endphp

        {{-- Kartu untuk setiap notifikasi --}}
        <div class="card shadow-sm mb-3 notification-card">
            <div class="card-body p-3">
                <div class="row no-gutters align-items-center">
                    {{-- Kolom Ikon --}}
                    <div class="col-auto">
                        <div class="d-flex align-items-center justify-content-center rounded-circle {{ $bgColorClass }}" style="width: 50px; height: 50px;">
                            <i class="{{ $iconClass }} text-white fa-lg"></i>
                        </div>
                    </div>
                    
                    {{-- Kolom Informasi Utama --}}
                    <div class="col pl-3">
                        <h6 class="font-weight-bold text-dark mb-0">
                            Permohonan {{ $notif->data['jenis_surat'] ?? 'Tidak Diketahui' }}
                            {{-- Tautan ini membuat seluruh kartu bisa di-klik --}}
                            <a href="{{ $notif->data['url'] ?? '#' }}" class="stretched-link"></a>
                        </h6>
                        <small class="text-muted">
                            Oleh <strong>{{ $notif->data['nama_pemohon'] ?? 'Warga' }}</strong>
                        </small>
                    </div>

                    {{-- Kolom Status dan Waktu --}}
                    <div class="col-auto text-right">
                        @if ($notif->read_at == null)
                            <span class="badge badge-danger font-weight-bold mb-2">BARU</span>
                        @else
                            <span class="badge {{ $badgeColorClass }} mb-2">{{ ucfirst($status) }}</span>
                        @endif
                        <div class="small text-gray-600">
                            {{-- PERBAIKAN 2: Menambahkan format hari (memerlukan locale 'id' di config/app.php) --}}
                            {{ \Carbon\Carbon::parse($notif->created_at)->translatedFormat('l') }}, {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-folder-open fa-4x text-gray-300"></i>
            </div>
            <h4 class="font-weight-bold">Tidak Ada Riwayat Notifikasi</h4>
            <p class="text-muted">Belum ada notifikasi yang cocok dengan filter Anda.</p>
        </div>
    @endforelse
</div>

{{-- Link Paginasi --}}
@if($semuaNotifikasi->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $semuaNotifikasi->appends(request()->query())->links() }}
</div>
@endif

@endsection
