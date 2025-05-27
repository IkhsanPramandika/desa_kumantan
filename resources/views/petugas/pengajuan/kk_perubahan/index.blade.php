@extends('layouts.app')

@section('title', 'Daftar Permohonan KK Perubahan Data')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Daftar Permohonan KK Perubahan Data</h1>

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
        <a href="{{ route('permohonan-kk-perubahan.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat Permohonan Baru
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>File KK</th>
                        <th>File KTP</th>
                        <th>Surat Pengantar RT/RW</th>
                        <th>Surat Keterangan Pendukung</th>
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
                            <td colspan="10" class="text-center">Tidak ada data permohonan KK Perubahan Data.</td>
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
                            <td>
                                @if ($item->surat_pengantar_rt_rw)
                                    <a href="{{ asset('storage/' . $item->surat_pengantar_rt_rw) }}" target="_blank">Lihat Surat</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($item->surat_keterangan_pendukung)
                                    <a href="{{ asset('storage/' . $item->surat_keterangan_pendukung) }}" target="_blank">Lihat Surat</a>
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
                                    <form action="{{ route('permohonan-kk-perubahan.verifikasi', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm mb-1" onclick="return confirm('Yakin ingin verifikasi permohonan ini?')">Verifikasi</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#tolakModal{{ $item->id }}">
                                        Tolak
                                    </button>
                                @elseif ($item->status == 'diterima' || $item->status == 'diproses')
                                    @if (!$item->file_hasil_akhir)
                                        <button type="button" class="btn btn-primary btn-sm mb-1" data-toggle="modal" data-target="#uploadPdfModal{{ $item->id }}">
                                            Unggah KK Final
                                        </button>
                                    @else
                                        <a href="{{ route('permohonan-kk-perubahan.download-final', $item->id) }}" class="btn btn-info btn-sm mb-1" target="_blank">Lihat KK Final</a>
                                    @endif
                                @elseif ($item->status == 'selesai')
                                    <span class="text-success">Telah Selesai</span>
                                    <a href="{{ route('permohonan-kk-perubahan.download-final', $item->id) }}" class="btn btn-info btn-sm mt-1" target="_blank">Unduh Final</a>
                                @else {{-- Ditolak --}}
                                    <span class="text-muted">Sudah Ditolak</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Modal untuk Unggah PDF Final --}}
                        <div class="modal fade" id="uploadPdfModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="uploadPdfModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="uploadPdfModalLabel{{ $item->id }}">Unggah Kartu Keluarga Perubahan Data Final</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('permohonan-kk-perubahan.upload-final-pdf', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="file_hasil_akhir{{ $item->id }}">Pilih File PDF Kartu Keluarga Perubahan Data Final:</label>
                                                <input type="file" class="form-control-file @error('file_hasil_akhir') is-invalid @enderror" id="file_hasil_akhir{{ $item->id }}" name="file_hasil_akhir" accept="application/pdf" required>
                                                @error('file_hasil_akhir')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Unggah dan Selesaikan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

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
                                    <form action="{{ route('permohonan-kk-perubahan.tolak', $item->id) }}" method="POST">
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
    // Contoh untuk menangani validasi form dari server-side jika modal ditutup
    @if ($errors->has('file_hasil_akhir') || $errors->has('catatan_penolakan'))
        $(document).ready(function() {
            // Anda perlu mengimplementasikan logika untuk mendapatkan ID permohonan yang salah
            // Jika Anda hanya punya 1 form, ini tidak masalah. Jika banyak, ini penting.
            // Misalnya, passing id dari controller saat redirect withErrors
        });
    @endif
</script>
@endpush
