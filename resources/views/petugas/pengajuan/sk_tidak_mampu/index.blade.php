@extends('layouts.app')

@section('title', 'Daftar Permohonan SK Tidak Mampu')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan Surat Keterangan Tidak Mampu</h1>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Permohonan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>File KK</th>
                        <th>File KTP</th>
                        <th>Catatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($data->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data permohonan SK Tidak Mampu.</td>
                        </tr>
                    @else
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                @if ($item->file_kk)
                                    <a href="{{ asset('storage/' . $item->file_kk) }}" target="_blank">Lihat KK</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($item->file_ktp)
                                    <a href="{{ asset('storage/' . $item->file_ktp) }}" target="_blank">Lihat KTP</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                            <td>
                                @if ($item->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif ($item->status == 'diterima')
                                    <span class="badge badge-success">Diterima</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->status == 'pending')
                                    <form action="{{ route('permohonan-sk-tidak-mampu.verifikasi', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Yakin ingin verifikasi permohonan ini?')">Verifikasi</button>
                                    </form>
                                    <form action="{{ route('permohonan-sk-tidak-mampu.tolak', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin tolak permohonan ini?')">Tolak</button>
                                    </form>
                                @else
                                    <span class="text-muted">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script DataTables jika diperlukan --}}
{{-- <script src="{{ asset('sbadmin/js/demo/datatables-demo.js') }}"></script> --}}
@endpush
