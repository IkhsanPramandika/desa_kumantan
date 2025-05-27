@extends('layouts.app')

@section('title', 'Daftar Permohonan Surat Keterangan Kelahiran')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan Surat Keterangan Kelahiran Dan Proses Akta Kelahiran</h1>

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
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Permohonan</h6>
        <a href="{{ route('permohonan-sk-kelahiran.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat Permohonan Baru
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Anak</th>
                        <th>Tgl Lahir Anak</th>
                        <th>Nama Ayah</th>
                        <th>Nama Ibu</th>
                        <th>File KK</th>
                        <th>File KTP</th>
                        <th>Surat Pengantar RT/RW</th>
                        <th>Surat Nikah Orang Tua</th>
                        <th>Surat Keterangan Kelahiran</th>
                        <th>Catatan Pemohon</th>
                        <th>Status</th>
                        <th>Catatan Penolakan</th>
                        <th>Dokumen Hasil</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($data->isEmpty())
                        <tr>
                            <td colspan="15" class="text-center">Tidak ada data permohonan SK Kelahiran.</td>
                        </tr>
                    @else
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nama_anak ?? '-' }}</td>
                            {{-- START: Perbaikan untuk penanganan tanggal lahir anak --}}
                            <td>{{ ($item->tanggal_lahir_anak && $item->tanggal_lahir_anak instanceof \Carbon\Carbon) ? $item->tanggal_lahir_anak->format('d M Y') : '-' }}</td>
                            {{-- END: Perbaikan untuk penanganan tanggal lahir anak --}}
                            <td>{{ $item->nama_ayah ?? '-' }}</td>
                            <td>{{ $item->nama_ibu ?? '-' }}</td>
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
                            <td>
                                @if ($item->surat_pengantar_rt_rw)
                                    <a href="{{ asset('storage/' . $item->surat_pengantar_rt_rw) }}" target="_blank">Lihat Surat</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($item->surat_nikah_orangtua)
                                    <a href="{{ asset('storage/' . $item->surat_nikah_orangtua) }}" target="_blank">Lihat Surat</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($item->surat_keterangan_kelahiran)
                                    <a href="{{ asset('storage/' . $item->surat_keterangan_kelahiran) }}" target="_blank">Lihat Surat</a>
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
                                @elseif ($item->status == 'diproses')
                                    <span class="badge badge-info">Diproses</span>
                                @elseif ($item->status == 'selesai')
                                    <span class="badge badge-primary">Selesai</span>
                                @else {{-- Ditolak --}}
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>{{ $item->catatan_penolakan ?? '-' }}</td>
                            <td>
                                @if ($item->file_hasil_akhir)
                                    <a href="{{ asset($item->file_hasil_akhir) }}" target="_blank" class="btn btn-sm btn-outline-info">Unduh</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($item->status == 'pending')
                                    <form action="{{ route('permohonan-sk-kelahiran.verifikasi', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm mb-1" onclick="return confirm('Yakin ingin verifikasi permohonan ini?')">Verifikasi</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#tolakModal{{ $item->id }}">
                                        Tolak
                                    </button>
                                @elseif ($item->status == 'diterima' || $item->status == 'diproses')
                                    {{-- Tombol untuk mengisi data rinci --}}
                                    <a href="{{ route('permohonan-sk-kelahiran.input-data', $item->id) }}" class="btn btn-info btn-sm mb-1">Input Data & Buat SK</a>
                                @elseif ($item->status == 'selesai')
                                    <span class="text-success">Telah Selesai</span>
                                    <a href="{{ route('permohonan-sk-kelahiran.download-final', $item->id) }}" class="btn btn-info btn-sm mt-1" target="_blank">Unduh Final</a>
                                @else {{-- Ditolak --}}
                                    <span class="text-muted">Sudah Ditolak</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Modal untuk Catatan Penolakan --}}
                        <div class="modal fade" id="tolakModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="tolakModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="tolakModalLabel{{ $item->id }}">Tolak Permohonan</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('permohonan-sk-kelahiran.tolak', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="catatan_penolakan{{ $item->id }}">Alasan Penolakan:</label>
                                                <textarea class="form-control @error('catatan_penolakan') is-invalid @enderror" id="catatan_penolakan{{ $item->id }}" name="catatan_penolakan" rows="3" required></textarea>
                                                @error('catatan_penolakan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk menampilkan modal validasi jika ada error
    @if ($errors->any())
        $(document).ready(function() {
            // Ini akan mencoba membuka modal berdasarkan ID yang disimpan di session 'old'
            // Anda perlu memastikan controller Anda mengirimkan old('modal_id') saat redirect with errors
            // Contoh: return redirect()->back()->withErrors($validator)->withInput()->with('modal_id', $item->id);
            var errorItemId = "{{ old('modal_id') }}";
            if (errorItemId) {
                if ("{{ $errors->has('catatan_penolakan') }}") {
                    $('#tolakModal' + errorItemId).modal('show');
                }
            }
        });
    @endif
</script>
@endpush
