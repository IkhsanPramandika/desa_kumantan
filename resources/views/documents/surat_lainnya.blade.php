<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $permohonan->judul_surat_final }}</title>
    <style>
        /* CSS tetap sama dengan yang Anda berikan, tidak ada perubahan */
        body{font-family:'Times New Roman',Times,serif;font-size:12pt;line-height:1.5;margin:2cm}
        .container{width:100%}
        .kop-surat{text-align:center;line-height:1.3;border-bottom:3px double black;padding-bottom:15px;margin-bottom:30px;position:relative}
        .kop-surat .logo{width:80px;height:auto;position:absolute;left:0;top:0}
        .kop-surat h1,.kop-surat h2,.kop-surat h3,.kop-surat p{margin:0}
        .kop-surat h1{font-size:16pt;font-weight:bold}
        .kop-surat h2{font-size:14pt}
        .kop-surat h3{font-size:18pt;font-weight:bold}
        .kop-surat p{font-size:10pt}
        .judul-surat{text-align:center;margin-bottom:5px}
        .judul-surat h4{font-size:14pt;font-weight:bold;text-decoration:underline;margin:0}
        .nomor-surat{text-align:center;font-size:12pt;margin-bottom:30px}
        .isi-surat{text-align:justify}
        .isi-surat .paragraf{text-indent:50px;margin-bottom:15px}
        .isi-surat .paragraf-non-indent{margin-bottom:15px;margin-left:50px}
        .tabel-data{border-collapse:collapse;width:100%;margin-left:50px;margin-bottom:15px;margin-top:15px}
        .tabel-data td{padding:2px;vertical-align:top}
        .tabel-data .label{width:35%}
        .tabel-data .separator{width:5%}
        .tabel-data .data{font-weight:bold}
        .tanda-tangan{margin-top:50px}
        .blok-ttd{width:45%;float:right;text-align:center}
        .blok-ttd .ttd-area{min-height:80px}
        .blok-ttd .nama-pejabat{font-weight:bold;text-decoration:underline}
        .clearfix::after{content:"";clear:both;display:table}
    </style>
</head>
<body>
    <div class="container">
        <!-- KOP SURAT (HEADER) -->
        <div class="kop-surat">
            <img src="{{ public_path('sbadmin/img/logo_kampar.png') }}" alt="Logo Desa" class="logo">
            <h1>PEMERINTAH KABUPATEN KAMPAR</h1>
            <h2>KECAMATAN BANGKINANG KOTA</h2>
            <h3>KEPALA DESA KUMANTAN</h3>
            <p>Alamat: JL. Mahmud Marzuki, Kelurahan Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, Kode Pos 28463</p>
        </div>

        <!-- JUDUL DAN NOMOR SURAT -->
        <div class="judul-surat">
            {{-- Judul surat diambil dari inputan petugas untuk fleksibilitas --}}
            <h4>{{ strtoupper($permohonan->judul_surat_final) }}</h4>
        </div>
        <div class="nomor-surat">
            Nomor : {{ $permohonan->nomor_surat }}
        </div>

        <!-- ISI SURAT -->
        <div class="isi-surat">
            <p class="paragraf">Yang bertanda tangan di bawah ini Kepala Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, dengan ini menerangkan bahwa:</p>

            <!-- DATA PEMOHON (DIAMBIL OTOMATIS DARI DATABASE) -->
            <table class="tabel-data">
                {{-- Data pemohon diambil dari relasi 'masyarakat' yang ada di model PermohonanLainnya --}}
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="separator">:</td>
                    <td class="data">{{ strtoupper($permohonan->masyarakat->nama_lengkap ?? '[NAMA LENGKAP TIDAK DITEMUKAN]') }}</td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td class="separator">:</td>
                    <td>{{ $permohonan->masyarakat->nik ?? '[NIK TIDAK DITEMUKAN]' }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat/Tgl. Lahir</td>
                    <td class="separator">:</td>
                    <td>{{ $permohonan->masyarakat->tempat_lahir ?? '' }}, {{ $permohonan->masyarakat->tanggal_lahir ? \Carbon\Carbon::parse($permohonan->masyarakat->tanggal_lahir)->isoFormat('D MMMM YYYY') : '[TANGGAL LAHIR TIDAK DITEMUKAN]' }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="separator">:</td>
                    <td>{{ $permohonan->masyarakat->jenis_kelamin ?? '[JENIS KELAMIN TIDAK DITEMUKAN]' }}</td>
                </tr>
                 <tr>
                    <td class="label">Pekerjaan</td>
                    <td class="separator">:</td>
                    <td>{{ $permohonan->masyarakat->pekerjaan ?? '[PEKERJAAN TIDAK DITEMUKAN]' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="separator">:</td>
                    <td>{{ $permohonan->masyarakat->alamat_lengkap ?? '[ALAMAT TIDAK DITEMUKAN]' }}</td>
                </tr>
            </table>

            <p class="paragraf">Bahwa nama tersebut di atas adalah benar merupakan penduduk Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar.</p>
            
            <!-- KONTEN SPESIFIK DARI PETUGAS -->
            {{-- Bagian ini akan diisi oleh konten custom dari editor (CKEditor) yang diinput oleh petugas --}}
            {!! $permohonan->konten_final_html !!}

            <p class="paragraf">Demikian Surat Keterangan ini kami berikan kepada yang bersangkutan untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- TANDA TANGAN -->
        <div class="tanda-tangan clearfix">
            <div class="blok-ttd">
                Kumantan, {{ $permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->isoFormat('D MMMM YYYY') : \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>
                Kepala Desa Kumantan
                <div class="ttd-area">
                    {{-- Area untuk stempel dan tanda tangan basah --}}
                </div>
                <div class="nama-pejabat">FIRDAUS, S.Pd</div>
            </div>
        </div>
    </div>
</body>
</html>
