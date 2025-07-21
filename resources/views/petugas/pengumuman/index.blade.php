@extends('layouts.app')

@section('title', 'Manajemen Pengumuman')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Pengumuman & Berita</h1>
    <a href="{{ route('petugas.pengumuman.create') }}" class="btn btn-primary btn-icon-split">
        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
        <span class="text">Buat Pengumuman Baru</span>
    </a>
</div>

{{-- Notifikasi --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengumuman</h6>
    </div>
    <div class="card-body">
        <div class="card card-body mb-4 p-3 bg-light">
            <form action="{{ route('petugas.pengumuman.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="font-weight-bold">Cari Judul</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan judul pengumuman..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="font-weight-bold">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua --</option>
                            <option value="dipublikasikan" @selected(request('status') == 'dipublikasikan')>Dipublikasikan</option>
                            <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="font-weight-bold">Tampilkan</label>
                        <select class="form-control" id="per_page" name="per_page">
                            <option value="10" @selected(request('per_page', 10) == 10)>10</option>
                            <option value="25" @selected(request('per_page') == 25)>25</option>
                            <option value="50" @selected(request('per_page') == 50)>50</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search fa-sm"></i> Cari</button>
                        <a href="{{ route('petugas.pengumuman.index') }}" class="btn btn-secondary ml-2" title="Reset Filter"><i class="fas fa-sync"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th style="width: 15%;">Tgl Publikasi</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 18%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengumuman as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <a href="{{ route('petugas.pengumuman.show', $item->id) }}" class="font-weight-bold">{{ Str::limit($item->judul, 60) }}</a>
                            </td>
                            <td>{{ $item->user->name ?? 'N/A' }}</td>
                            <td>{{ $item->tanggal_publikasi->isoFormat('D MMM YYYY') }}</td>
                            <td>
                                @if($item->status_publikasi == 'dipublikasikan')
                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Dipublikasikan</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-clock mr-1"></i> Draft</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('petugas.pengumuman.show', $item->id) }}" class="btn btn-sm btn-info" title="Lihat"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('petugas.pengumuman.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('petugas.pengumuman.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="my-4">
                                    <i class="fas fa-bullhorn fa-3x text-gray-400"></i>
                                    <p class="mt-3 text-gray-600">Belum ada pengumuman yang dibuat.</p>
                                    <a href="{{ route('petugas.pengumuman.create') }}" class="btn btn-primary btn-sm">Buat Pengumuman Pertama Anda</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $pengumuman->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const perPageSelect = document.getElementById('per_page');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                this.closest('form').submit();
            });
        }
    });
</script>
@endpush
