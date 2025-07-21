@extends('layouts.app')

@section('title', 'Proses Surat Keterangan Ahli Waris')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Proses & Edit Surat Keterangan Ahli Waris</h1>

{{-- Menampilkan error validasi jika ada --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <p><strong>Harap perbaiki error berikut:</strong></p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('petugas.permohonan-sk-ahli-waris.selesaikan', $permohonan->id) }}" method="POST">
    @csrf
    <div class="row">
        {{-- KOLOM KIRI: DATA DARI MASYARAKAT (READ-ONLY) --}}
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data yang Diajukan</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Data Pewaris (Alm)</h5>
                    <dl>
                        <dt>Nama:</dt><dd>{{ $permohonan->nama_pewaris ?? '-' }}</dd>
                        <dt>NIK:</dt><dd>{{ $permohonan->nik_pewaris ?? '-' }}</dd>
                        <dt>Tanggal Meninggal:</dt><dd>{{ $permohonan->tanggal_meninggal_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_meninggal_pewaris)->isoFormat('D MMMM YYYY') : '-' }}</dd>
                    </dl>
                    <hr>
                    <h5 class="font-weight-bold mt-4">Daftar Ahli Waris</h5>
                    <ol>
                        @php
                            $ahliWarisListReadOnly = is_string($permohonan->daftar_ahli_waris) ? json_decode($permohonan->daftar_ahli_waris, true) : $permohonan->daftar_ahli_waris;
                        @endphp
                        @forelse ($ahliWarisListReadOnly ?? [] as $ahliWaris)
                            <li>{{ $ahliWaris['nama'] ?? '-' }} ({{ $ahliWaris['hubungan'] ?? '-' }})</li>
                        @empty
                            <li>Tidak ada data ahli waris yang diajukan.</li>
                        @endforelse
                    </ol>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: FORM ISIAN PETUGAS (EDITABLE) --}}
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Isian Surat oleh Petugas</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Verifikasi dan perbaiki data di bawah ini sebelum membuat surat final.</p>
                    <div class="alert alert-info">Nomor surat akan dibuat secara otomatis oleh sistem.</div>

                    <h5 class="font-weight-bold mt-4">Data Pewaris (Alm)</h5>
                    <div class="form-group">
                        <label>Nama Lengkap Pewaris</label>
                        <input type="text" class="form-control" name="nama_pewaris" value="{{ old('nama_pewaris', $permohonan->nama_pewaris) }}" required>
                    </div>
                    {{-- Tambahkan field pewaris lainnya jika perlu diedit --}}

                    <hr>
                    <h5 class="font-weight-bold mt-4">Daftar Ahli Waris</h5>
                    <div id="ahli-waris-fields">
                        @php
                            $ahliWarisData = old('daftar_ahli_waris', $permohonan->daftar_ahli_waris);
                            if (is_string($ahliWarisData)) {
                                $ahliWarisData = json_decode($ahliWarisData, true) ?? [];
                            }
                            if (empty($ahliWarisData)) {
                                $ahliWarisData = [['nama'=>'', 'nik'=>'', 'hubungan'=>'', 'alamat'=>'']];
                            }
                        @endphp

                        @foreach ($ahliWarisData as $index => $ahliWaris)
                        <div class="ahli-waris-item card mb-3">
                            <div class="card-body">
                                <h6 class="font-weight-bold">Ahli Waris {{ $index + 1 }}</h6>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" class="form-control" name="daftar_ahli_waris[{{ $index }}][nama]" value="{{ $ahliWaris['nama'] ?? '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label>NIK</label>
                                    <input type="text" class="form-control" name="daftar_ahli_waris[{{ $index }}][nik]" value="{{ $ahliWaris['nik'] ?? '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Hubungan Keluarga</label>
                                    <input type="text" class="form-control" name="daftar_ahli_waris[{{ $index }}][hubungan]" value="{{ $ahliWaris['hubungan'] ?? '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea class="form-control" name="daftar_ahli_waris[{{ $index }}][alamat]" rows="2" required>{{ $ahliWaris['alamat'] ?? '' }}</textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-ahli-waris">Hapus</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-ahli-waris" class="btn btn-secondary btn-sm mt-2">Tambah Ahli Waris</button>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Anda akan membuat surat final berdasarkan data di form ini. Lanjutkan?')">
                        <i class="fas fa-print"></i> Buat Surat Final & Selesaikan
                    </button>
                    <a href="{{ route('petugas.permohonan-sk-ahli-waris.show', $permohonan->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
@php
    // --- PERBAIKAN KUNCI ADA DI SINI ---
    // Decode data menjadi array SEBELUM digunakan oleh fungsi count() di dalam script
    $ahliWarisForScript = old('daftar_ahli_waris', $permohonan->daftar_ahli_waris);
    if (is_string($ahliWarisForScript)) {
        $ahliWarisForScript = json_decode($ahliWarisForScript, true) ?? [];
    }
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Gunakan variabel yang sudah pasti array
    let ahliWarisIndex = {{ count($ahliWarisForScript) }};
    const container = document.getElementById('ahli-waris-fields');

    document.getElementById('add-ahli-waris').addEventListener('click', function () {
        const newItem = document.createElement('div');
        newItem.classList.add('ahli-waris-item', 'card', 'mb-3');
        newItem.innerHTML = `
            <div class="card-body">
                <h6 class="font-weight-bold">Ahli Waris ${ahliWarisIndex + 1}</h6>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-control" name="daftar_ahli_waris[${ahliWarisIndex}][nama]" required>
                </div>
                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" class="form-control" name="daftar_ahli_waris[${ahliWarisIndex}][nik]" required>
                </div>
                <div class="form-group">
                    <label>Hubungan Keluarga</label>
                    <input type="text" class="form-control" name="daftar_ahli_waris[${ahliWarisIndex}][hubungan]" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea class="form-control" name="daftar_ahli_waris[${ahliWarisIndex}][alamat]" rows="2" required></textarea>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-ahli-waris">Hapus</button>
            </div>
        `;
        container.appendChild(newItem);
        ahliWarisIndex++;
    });

    container.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-ahli-waris')) {
            e.target.closest('.ahli-waris-item').remove();
        }
    });
});
</script>
@endpush
