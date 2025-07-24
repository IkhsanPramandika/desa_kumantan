@extends('layouts.app')

@section('title', 'Manajemen Akun Masyarakat')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Manajemen Akun Masyarakat</h1>

{{-- Notifikasi --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Akun Warga</h6>
    </div>
    <div class="card-body">
        <div class="card card-body mb-4 p-3 bg-light">
            <form action="{{ route('petugas.masyarakat.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="font-weight-bold">Cari Warga</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan NIK, Nama, No. HP..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status_akun" class="font-weight-bold">Status Akun</label>
                        <select class="form-control" id="status_akun" name="status_akun">
                            <option value="">-- Semua Status --</option>
                            <option value="pending_verification" @selected(request('status_akun') == 'pending_verification')>Pending Verifikasi</option>
                            <option value="active" @selected(request('status_akun') == 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status_akun') == 'inactive')>Tidak Aktif</option>
                            <option value="rejected" @selected(request('status_akun') == 'rejected')>Ditolak</option>
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
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search fa-sm"></i> Terapkan</button>
                        <a href="{{ route('petugas.masyarakat.index') }}" class="btn btn-secondary ml-2" title="Reset Filter"><i class="fas fa-sync"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIK</th>
                        <th>Kontak</th>
                        <th>Status Akun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($masyarakat as $item)
                        <tr>
                            <td>{{ $item->nama_lengkap }}</td>
                            <td>{{ $item->nik }}</td>
                            <td>
                                <div>{{ $item->nomor_hp ?? '-' }}</div>
                                <div class="small text-muted">{{ $item->email ?? '-' }}</div>
                            </td>
                            <td>
                                @include('layouts.partials.akun_status_badge', ['status' => $item->status_akun])
                            </td>
                            <td>
                                <a href="{{ route('petugas.masyarakat.show', $item->id) }}" class="btn btn-sm btn-info" title="Lihat Detail & Verifikasi">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="my-4">
                                    <i class="fas fa-users-slash fa-3x text-gray-400"></i>
                                    <p class="mt-3 text-gray-600">Data warga tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $masyarakat->links() }}
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
