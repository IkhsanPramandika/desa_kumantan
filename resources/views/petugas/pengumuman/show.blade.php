@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Detail Pengumuman: ' . $pengumuman->judul)

@push('styles')
<style>
    .pengumuman-header-meta {
        font-size: 0.875rem; /* 14px jika font dasar 16px */
        color: #858796; /* Warna teks abu-abu dari SB Admin 2 */
        margin-bottom: 1rem; /* Jarak bawah dari metadata */
    }
    .pengumuman-header-meta .meta-item {
        margin-right: 1.5rem; /* Jarak antar item metadata */
    }
    .pengumuman-header-meta .meta-item i {
        margin-right: 0.35rem; /* Jarak ikon dari teksnya */
    }
    .pengumuman-gambar-utama {
        width: 100%;
        max-height: 450px; /* Batasi tinggi maksimum gambar agar tidak terlalu dominan */
        object-fit: cover; /* Pastikan gambar menutupi area tanpa distorsi, mungkin memotong bagian gambar */
        border-radius: 0.35rem; /* Sudut membulat standar Bootstrap */
        margin-bottom: 1.5rem; /* Jarak bawah dari gambar */
    }
    .pengumuman-isi-lengkap {
        font-size: 1rem; /* Ukuran font standar untuk konten */
        line-height: 1.7; /* Jarak antar baris yang nyaman dibaca */
        color: #5a5c69; /* Warna teks konten standar SB Admin 2 */
        text-align: justify; /* Rata kiri-kanan untuk tampilan formal */
    }
    .pengumuman-isi-lengkap p, 
    .pengumuman-isi-lengkap ul, 
    .pengumuman-isi-lengkap ol,
    .pengumuman-isi-lengkap blockquote,
    .pengumuman-isi-lengkap table { /* Beri margin bawah untuk elemen blok utama dari WYSIWYG */
        margin-bottom: 1rem;
    }
    .pengumuman-isi-lengkap h1, 
    .pengumuman-isi-lengkap h2, 
    .pengumuman-isi-lengkap h3, 
    .pengumuman-isi-lengkap h4, 
    .pengumuman-isi-lengkap h5, 
    .pengumuman-isi-lengkap h6 { /* Styling untuk heading di dalam konten */
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: #3a3b45;
    }
    .lampiran-section {
        margin-top: 2rem;
        padding-top: 1.5rem; /* Padding atas lebih besar */
        border-top: 1px solid #e3e6f0; /* Garis pemisah standar SB Admin 2 */
    }
    .lampiran-section h5 {
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Tombol Aksi di Atas --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('petugas.pengumuman.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar Pengumuman
        </a>
        <div>
            <a href="{{ route('petugas.pengumuman.edit', $pengumuman->id) }}" class="btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Pengumuman
            </a>
            {{-- Tombol Hapus jika diperlukan di halaman detail --}}
            {{-- <form action="{{ route('petugas.pengumuman.destroy', $pengumuman->id) }}" method="POST" class="d-inline ml-2" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                    <i class="fas fa-trash fa-sm text-white-50"></i> Hapus
                </button>
            </form> --}}
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-md-5"> {{-- Padding lebih besar di card body --}}
            {{-- Judul Pengumuman --}}
            <h1 class="h2 mb-3 text-gray-900 font-weight-bold">{{ $pengumuman->judul }}</h1>

            {{-- Meta Data Pengumuman --}}
            <div class="pengumuman-header-meta">
                <span class="meta-item" title="Tanggal Publikasi">
                    <i class="fas fa-calendar-alt"></i>
                    {{ $pengumuman->tanggal_publikasi->translatedFormat('d F Y') }} {{-- Format tanggal Indonesia --}}
                </span>
                <span class="meta-item" title="Penulis">
                    <i class="fas fa-user"></i>
                    Oleh: {{ $pengumuman->user->name ?? 'Admin Desa' }}
                </span>
                <span class="meta-item" title="Status">
                     @if ($pengumuman->status_publikasi == 'dipublikasikan')
                        <i class="fas fa-check-circle text-success"></i> <span class="text-success">Dipublikasikan</span>
                    @else
                        <i class="fas fa-clock text-warning"></i> <span class="text-warning">Draft</span>
                    @endif
                </span>
                {{-- Contoh jika ada kategori --}}
                {{-- @if($pengumuman->kategori) 
                <span class="meta-item" title="Kategori">
                    <i class="fas fa-tag"></i>
                    {{ $pengumuman->kategori->nama_kategori ?? 'Umum' }}
                </span>
                @endif --}}
            </div>

            {{-- Gambar Utama Pengumuman --}}
            @if($pengumuman->gambar_pengumuman)
                <img src="{{ Storage::url($pengumuman->gambar_pengumuman) }}" alt="Gambar {{ $pengumuman->judul }}" class="pengumuman-gambar-utama img-fluid">
            @endif

            {{-- Isi Pengumuman --}}
            <div class="pengumuman-isi-lengkap mt-4">
                {{-- PENTING: Menampilkan konten HTML dari editor WYSIWYG --}}
                {{-- Pastikan konten $pengumuman->isi telah disanitasi di sisi server (Controller) sebelum disimpan ke DB untuk mencegah XSS --}}
                {!! $pengumuman->isi !!} 
            </div>

            {{-- File Lampiran --}}
            @if($pengumuman->file_pengumuman)
                <div class="lampiran-section">
                    <h5><i class="fas fa-paperclip"></i> File Lampiran:</h5>
                    <p>
                        <a href="{{ Storage::url($pengumuman->file_pengumuman) }}" target="_blank" class="btn btn-info">
                            <i class="fas fa-download fa-sm"></i> Unduh Lampiran ({{ basename($pengumuman->file_pengumuman) }})
                        </a>
                    </p>
                </div>
            @endif
        </div>

        <div class="card-footer text-muted">
            <small>
                Diterbitkan pada: {{ $pengumuman->created_at->translatedFormat('d M Y, H:i') }} | 
                Terakhir diperbarui: {{ $pengumuman->updated_at->translatedFormat('d M Y, H:i') }}
            </small>
        </div>
    </div>
</div>
@endsection
