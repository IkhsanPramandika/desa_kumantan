@extends('layouts.app')

@section('title', 'Daftar Permohonan KK Baru')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan Kartu Keluarga Baru</h1>

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

        {{-- PERBAIKAN: Menambahkan kembali Form Filter dan Pencarian Server-Side --}}
        <div class="mb-4">
            <form action="{{ route('petugas.permohonan-kk-baru.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <label for="search" class="sr-only">Cari</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Cari nama/NIK pemohon..." value="{{ request('search') }}">
                </div>
                <div class="form-group mr-2">
                    <label for="status" class="sr-only">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('petugas.permohonan-kk-baru.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pemohon</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Dokumen Hasil</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <strong>{{ $item->masyarakat->nama ?? 'N/A' }}</strong><br>
                                <small>NIK: {{ $item->masyarakat->nik ?? 'N/A' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($item->status == 'pending') <span class="badge badge-warning">Pending</span>
                                @elseif ($item->status == 'diterima') <span class="badge badge-info">Diterima</span>
                                @elseif ($item->status == 'diproses') <span class="badge badge-primary">Diproses</span>
                                @elseif ($item->status == 'selesai') <span class="badge badge-success">Selesai</span>
                                @elseif ($item->status == 'ditolak') <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->status == 'selesai' && $item->file_hasil_akhir)
                                    <a href="{{ route('petugas.permohonan-kk-baru.download-final', $item->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Unduh KK
                                    </a>
                                @else
                                    <span class="badge badge-secondary">Belum Tersedia</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('petugas.permohonan-kk-baru.show', $item->id) }}" class="btn btn-sm btn-primary">
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
        
        {{-- PERBAIKAN: Menambahkan kembali Link Paginasi Laravel --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $data->links() }}
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- PERBAIKAN: Skrip untuk menonaktifkan fitur DataTables yang tidak diperlukan --}}
<script>
$(document).ready(function() {
  // Hancurkan instance DataTable yang mungkin sudah dibuat oleh skrip bawaan tema
  if ($.fn.DataTable.isDataTable('#dataTable')) {
    $('#dataTable').DataTable().destroy();
  }

  // Inisialisasi ulang DataTable dengan konfigurasi yang kita inginkan
  $('#dataTable').DataTable({
    "searching": false, // Menonaktifkan kotak pencarian bawaan DataTables
    "paging": true,    // Menonaktifkan paginasi bawaan DataTables (kita pakai paginasi Laravel)
    "info": true,      // Menonaktifkan teks "Showing 1 to 10 of 57 entries"
    "order": [],        // Menonaktifkan sorting awal pada kolom pertama
    "columnDefs": [ {
      "targets": 'no-sort', // Menonaktifkan sorting untuk kolom dengan class 'no-sort'
      "orderable": true
    } ]
  });
});
</script>
@endpush
