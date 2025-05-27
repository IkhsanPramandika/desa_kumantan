@extends('layouts.app')

@section('title', 'Hasil Pencarian')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Hasil Pencarian untuk "{{ $query }}"</h1>

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
        <h6 class="m-0 font-weight-bold text-primary">Hasil Ditemukan ({{ $results->count() }})</h6>
    </div>
    <div class="card-body">
        @if ($results->isEmpty())
            <p>Tidak ada hasil yang ditemukan untuk "{{ $query }}".</p>
        @else
            {{-- Anda perlu menyesuaikan bagian ini untuk menampilkan hasil dari berbagai model --}}
            {{-- Contoh sederhana untuk menampilkan semua hasil --}}
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipe Permohonan</th>
                            <th>Catatan/Nama Usaha</th>
                            <th>Status</th>
                            {{-- Tambahkan kolom lain yang relevan --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $result)
                            <tr>
                                <td>{{ $result->id }}</td>
                                <td>
                                    {{-- Tentukan tipe permohonan berdasarkan nama kelas model --}}
                                    @php
                                        $modelClass = class_basename($result);
                                        $type = '';
                                        switch ($modelClass) {
                                            case 'PermohonananKKBaru': $type = 'KK Baru'; break;
                                            case 'PermohonananKKHilang': $type = 'KK Hilang'; break;
                                            case 'PermohonananKKPerubahanData': $type = 'KK Perubahan Data'; break;
                                            case 'PermohonananSKDomisili': $type = 'SK Domisili'; break;
                                            case 'PermohonananSKKelahiran': $type = 'SK Kelahiran'; break;
                                            case 'PermohonananSKKematian': $type = 'SK Kematian'; break;
                                            case 'PermohonananSKPerkawinan': $type = 'SK Perkawinan'; break;
                                            case 'PermohonananSKTidakMampu': $type = 'SK Tidak Mampu'; break;
                                            case 'PermohonananSKUsaha': $type = 'SK Usaha'; break;
                                            default: $type = 'Tidak Diketahui'; break;
                                        }
                                    @endphp
                                    {{ $type }}
                                </td>
                                <td>
                                    {{-- Tampilkan kolom yang relevan untuk pencarian --}}
                                    @if (isset($result->catatan))
                                        {{ Str::limit($result->catatan, 50) }}
                                    @elseif (isset($result->nama_usaha))
                                        {{ Str::limit($result->nama_usaha, 50) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($result->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif ($result->status == 'diterima')
                                        <span class="badge badge-success">Diterima</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                {{-- Anda bisa menambahkan link ke halaman detail permohonan di sini --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Jika Anda ingin menggunakan DataTables untuk hasil pencarian, aktifkan script ini --}}
<script src="{{ asset('sbadmin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('sbadmin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('sbadmin/js/demo/datatables-demo.js') }}"></script> {{-- Pastikan file ini ada --}}
@endpush
