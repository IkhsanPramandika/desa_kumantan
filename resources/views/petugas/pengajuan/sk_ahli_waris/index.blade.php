@extends('layouts.app')

@section('title', 'Daftar Permohonan SK Ahli Waris')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan Surat Keterangan Ahli Waris</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Permohonan</h6>
    </div>
    <div class="card-body">

        <div class="mb-4">
            <form action="{{ route('petugas.permohonan-sk-ahli-waris.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <input type="text" class="form-control" name="search" placeholder="Cari nama pemohon/pewaris..." value="{{ request('search') }}">
                </div>
                <div class="form-group mr-2">
                    <select class="form-control" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('petugas.permohonan-sk-ahli-waris.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pemohon</th>
                        <th>Pewaris</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->masyarakat->nama_lengkap ?? 'N/A' }}</td>
                            <td>{{ $item->nama_pewaris ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($item->status == 'pending') <span class="badge badge-warning">Pending</span>
                                @elseif (in_array($item->status, ['diterima', 'diproses'])) <span class="badge badge-info">{{ ucfirst($item->status) }}</span>
                                @elseif ($item->status == 'selesai') <span class="badge badge-success">Selesai</span>
                                @elseif ($item->status == 'ditolak') <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('petugas.permohonan-sk-ahli-waris.show', $item->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data yang cocok dengan filter Anda.</td>
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
$(document).ready(function() {
  if ($.fn.DataTable.isDataTable('#dataTable')) {
    $('#dataTable').DataTable().destroy();
  }
  $('#dataTable').DataTable({
    "searching": false,
    "paging": true,
    "paging": true,
    "info": true,
    "order": [],
    "columnDefs": [ { "targets": 'no-sort', "orderable": true } ]
  });
});
</script>
@endpush
