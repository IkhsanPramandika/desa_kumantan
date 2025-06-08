@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Daftar Pengumuman Desa')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manajemen Pengumuman Desa</h1>
    <p class="mb-4">Kelola semua pengumuman dan berita desa dari halaman ini.</p>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengumuman</h6>
            <a href="{{ route('petugas.pengumuman.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-sm"></i> Tambah Pengumuman Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTablePengumuman" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Judul</th>
                            <th>Tanggal Publikasi</th>
                            <th>Status</th>
                            <th>Pembuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengumuman as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($pengumuman->currentPage() - 1) * $pengumuman->perPage() }}</td>
                            <td>{{ $item->judul }}</td>
                            <td>{{ $item->tanggal_publikasi->format('d M Y') }}</td>
                            <td>
                                @if ($item->status_publikasi == 'dipublikasikan')
                                    <span class="badge badge-success">Dipublikasikan</span>
                                @else
                                    <span class="badge badge-warning">Draft</span>
                                @endif
                            </td>
                            <td>{{ $item->user->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('petugas.pengumuman.edit', $item->id) }}" class="btn btn-info btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('petugas.pengumuman.show', $item->id) }}" class="btn btn-secondary btn-sm" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('petugas.pengumuman.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pengumuman.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pengumuman->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Jika Anda menggunakan DataTables server-side, inisialisasi akan berbeda.
        // Ini untuk DataTables client-side sederhana:
        // $('#dataTablePengumuman').DataTable({
        //     "language": {
        //         "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
        //     },
        //     "order": [[ 1, "desc" ]] // Urutkan berdasarkan judul (kolom kedua) descending
        // });
    });
</script>
@endpush
