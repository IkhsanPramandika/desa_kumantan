@extends('layouts.app')

@section('title', 'Daftar Permohonan SK Kelahiran')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan Surat Keterangan Kelahiran</h1>

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

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Permohonan</h6>
    </div>
    <div class="card-body">

        <div class="card card-body mb-4 p-3 bg-light">
            <form action="{{ route('petugas.permohonan-sk-kelahiran.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="font-weight-bold">Cari Nama Anak/Orang Tua</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan kata kunci..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="font-weight-bold">Status Permohonan</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                            <option value="membutuhkan_revisi" @selected(request('status') == 'membutuhkan_revisi')>Perlu Revisi</option>
                            <option value="diterima" @selected(request('status') == 'diterima')>Diterima</option>
                            <option value="selesai" @selected(request('status') == 'selesai')>Selesai</option>
                            <option value="ditolak" @selected(request('status') == 'ditolak')>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="font-weight-bold">Tampilkan</label>
                        <select class="form-control" id="per_page" name="per_page">
                            <option value="10" @selected(request('per_page', 10) == 10)>10</option>
                            <option value="25" @selected(request('per_page') == 25)>25</option>
                            <option value="50" @selected(request('per_page') == 50)>50</option>
                            <option value="100" @selected(request('per_page') == 100)>100</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search fa-sm"></i> Terapkan Filter</button>
                        <a href="{{ route('petugas.permohonan-sk-kelahiran.index') }}" class="btn btn-secondary ml-2" title="Reset Filter"><i class="fas fa-sync"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th>Nama Anak</th>
                        <th>Orang Tua</th>
                        <th style="width: 15%;">Tanggal Pengajuan</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $item->nama_anak ?? 'N/A' }}</div>
                                <div class="small text-gray-600">{{ $item->jenis_kelamin_anak ?? '' }}</div>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $item->nama_ayah ?? 'N/A' }}</div>
                                <div class="small text-gray-600">{{ $item->nama_ibu ?? 'N/A' }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMMM YYYY, HH:mm') }}</td>
                            <td>
                                @include('layouts.partials.status_badge', ['status' => $item->status])
                            </td>
                            <td>
                                <a href="{{ route('petugas.permohonan-sk-kelahiran.show', $item->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye fa-sm"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="my-4">
                                    <i class="fas fa-box-open fa-3x text-gray-400"></i>
                                    <p class="mt-3 text-gray-600">Data tidak ditemukan.</p>
                                    <p class="small">Coba ubah atau reset filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $data->links() }}
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
