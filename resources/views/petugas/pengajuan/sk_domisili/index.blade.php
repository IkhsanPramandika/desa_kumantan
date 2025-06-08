@extends('layouts.app')

@section('title', 'Daftar Permohonan SK Domisili')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan Surat Keterangan Domisili</h1>

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
            <form action="{{ route('petugas.permohonan-sk-domisili.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <input type="text" class="form-control" name="search" placeholder="Cari nama/NIK..." value="{{ request('search') }}">
                </div>
                <div class="form-group mr-2">
                    <select class="form-control" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('petugas.permohonan-sk-domisili.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pemohon</th>
                        <th>Keperluan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <strong>{{ $item->nama_pemohon_atau_lembaga ?? 'N/A' }}</strong><br>
                                <small>NIK: {{ $item->nik_pemohon ?? 'N/A' }}</small>
                            </td>
                            <td>{{ Str::limit($item->keperluan_domisili, 50) ?? 'N/A' }}</td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($item->status == 'pending') <span class="badge badge-warning">Pending</span>
                                @elseif ($item->status == 'selesai') <span class="badge badge-success">Selesai</span>
                                @elseif ($item->status == 'ditolak') <span class="badge badge-danger">Ditolak</span>
                                @else <span class="badge badge-secondary">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('petugas.permohonan-sk-domisili.show', $item->id) }}" class="btn btn-sm btn-primary">
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
    "info": true,
    "order": [],
    "columnDefs": [ {
      "targets": 'no-sort',
      "orderable": true
    } ]
  });
});
</script>
@endpush
