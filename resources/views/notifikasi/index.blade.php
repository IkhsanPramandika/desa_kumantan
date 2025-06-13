@extends('layouts.app')

@section('title', 'Semua Notifikasi Permohonan')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Semua Riwayat Notifikasi</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian Riwayat</h6>
    </div>
    <div class="card-body">
     
        {{-- Form untuk Search dan Filter --}}
        <div class="mb-4">
            {{-- <form action="{{ route('notifikasi.index') }}" method="GET"> --}}
                <div class="form-row align-items-end">
                    <div class="col-md-4 mb-2">
                        <label for="search" class="sr-only">Cari</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Cari nama pemohon..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="status" class="sr-only">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="jenis_surat" class="sr-only">Jenis Surat</label>
                        <select class="form-control" id="jenis_surat" name="jenis_surat">
                            <option value="">-- Semua Jenis Surat --</option>
                            @foreach($jenisSuratOptions as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis_surat') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Daftar Notifikasi --}}
        <div class="list-group list-group-flush">
            @forelse ($semuaNotifikasi  as $notif)
                <a href="{{ $notif['url'] }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle {{ $notif['bg_color'] }} mr-3">
                            <i class="{{ $notif['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold text-dark">
                                Permohonan {{ $notif['jenis_surat'] }}
                                @if($notif['status'] == 'selesai')
                                    <span class="badge badge-success ml-2">Selesai</span>
                                @elseif($notif['status'] == 'ditolak')
                                    <span class="badge badge-danger ml-2">Ditolak</span>
                                @elseif($notif['status'] == 'pending')
                                    <span class="badge badge-warning ml-2">Pending</span>
                                @else
                                     <span class="badge badge-info ml-2">{{ ucfirst($notif['status']) }}</span>
                                @endif
                            </div>
                            <small class="text-muted">
                                Oleh: <strong>{{ $notif['nama_pemohon'] }}</strong> &bull; Dibuat pada: {{ \Carbon\Carbon::parse($notif['waktu'])->format('d M Y, H:i') }}
                            </small>
                        </div>
                    </div>
                    <small class="text-gray-500">{{ \Carbon\Carbon::parse($notif['waktu'])->diffForHumans() }}</small>
                </a>
            @empty
                <div class="list-group-item text-center">
                    Tidak ada riwayat yang cocok dengan filter Anda. <a href="{{ route('notifikasi.index') }}">Reset Filter</a>.
                </div>
            @endforelse
        </div>

        {{-- Link Paginasi --}}
        <div class="mt-4 d-flex justify-content-center">
            {{-- Penting: appends(request()->query()) akan mempertahankan parameter filter saat berpindah halaman --}}
            {{ $semuaNotifikasi ->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
