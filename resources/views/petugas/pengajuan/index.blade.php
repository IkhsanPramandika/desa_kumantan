@extends('layouts.app')

@section('title', 'Daftar Pengajuan Surat')

@section('content')
    <h2>Daftar Pengajuan Surat</h2>
    <a href="{{ route('petugas.pengajuan.create') }}" class="btn btn-primary mb-3">+ Tambah Pengajuan</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Jenis Layanan</th>
                <th>Status</th>
                <th>Data Tambahan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengajuan as $item)
                <tr>
                    <td>{{ \App\Models\PengajuanSurat::LAYANAN[$item->jenis_layanan] ?? $item->jenis_layanan }}</td>
                    <td>
                        <span class="badge bg-{{ $item->status == 'selesai' ? 'success' : 'warning' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>
                        <pre>{{ json_encode($item->data_tambahan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </td>
                    <td>
                        <a href="{{ route('petugas.pengajuan.show', $item->id) }}" class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
