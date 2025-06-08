<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Usaha</title>
    <style>
        body {
            font-family: 'Calibri', sans-serif; /* Mengikuti font SK Nikah */
            font-size: 11pt;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
        }
        .container {
            width: 90%;
            margin: auto;
            padding: 10px;
        }
        .header, .footer { /* .footer tidak digunakan secara eksplisit di SK Nikah, tapi style-nya ada */
            text-align: center;
        }
        .kop-surat {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 5px;
        }
        .kop-surat img {
            width: 50px; /* Logo disamakan ukurannya */
            height: auto;
            float: left;
            margin-right: 15px;
            vertical-align: middle;
        }
        .kop-surat div {
            overflow: hidden;
        }
        .kop-surat h3, .kop-surat h4 {
            margin: 0;
            padding: 0;
            line-height: 1.1;
            font-weight: normal;
        }
        .kop-surat h4 { /* PEMERINTAH & KECAMATAN */
            font-size: 10.5pt;
        }
        .kop-surat h3 { /* DESA */
            font-size: 12.5pt;
            font-weight: bold;
        }
        .kop-surat p { /* Alamat */
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }
        .kop-surat hr {
            border: 1.5px solid black;
            margin-top: 5px;
            clear: both;
        }
        .title-document { /* Mengganti .title agar tidak konflik jika ada style global */
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 8px 0;
            font-size: 12.5pt;
        }
        .nomor-surat-doc { /* Mengganti .nomor-surat */
            text-align: center;
            font-size: 10.5pt;
            margin-bottom: 15px;
        }
        .content-doc { /* Mengganti .content */
            text-align: justify;
            margin-bottom: 20px;
        }
        .content-doc p {
            margin-bottom: 5px;
        }
        .indent {
            text-indent: 30px;
        }
        table.data-table-doc { /* Mengganti .data-table */
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        table.data-table-doc td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 11pt;
        }
        table.data-table-doc td:first-child { /* Kolom Label (Nama, NIK, dll.) */
            width: 30%;
            padding-left: 30px; /* Indentasi untuk data setelah paragraf */
        }
        table.data-table-doc td:nth-child(2) { /* Kolom Titik Dua */
            width: 3%;
            text-align: center;
        }
        /* Kolom ketiga (data) akan mengambil sisa lebar */

        .signature-doc { /* Mengganti .signature */
            width: 45%;
            float: right;
            text-align: center;
            margin-top: 20px;
            font-size: 11pt;
        }
       .signature-area-doc { /* Mengganti .signature-area */
            min-height: 60px; /* Sedikit dikurangi jika TTD lebih kecil */
            margin-top: 5px;
            margin-bottom: 5px; /* Jarak sebelum nama */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .signature-area-doc img {
            max-width: 120px; /* Disesuaikan untuk TTD SKU */
            max-height: 60px; /* Batas tinggi agar tidak terlalu besar */
            height: auto;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        /* Styling untuk bagian data yang bertanda tangan dan data pemohon */
        .section-data {
            margin-left: 30px; /* Memberi indentasi untuk keseluruhan blok data */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="kop-surat clearfix">
            <img src="{{ public_path('sbadmin/img/logo_kampar.png') }}" alt="Logo Desa Kumantan">
            <div>
                <h4>PEMERINTAH KABUPATEN KAMPAR</h4>
                <h4>KECAMATAN BANGKINANG KOTA</h4>
                <h3>DESA KUMANTAN</h3>
                <p>Alamat : JL. Mahmud Marzuki, Kelurahan Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, Kode Pos 28463</p>
            </div>
            <hr>
        </div>

        <div class="title-document">
            SURAT KETERANGAN USAHA
        </div>
        <div class="nomor-surat-doc">
            NO : {{ $nomor_surat ?? '[NOMOR_SURAT]' }}
        </div>

        <div class="content-doc">
            <p class="indent">Yang bertanda tangan di bawah ini Kepala Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, menerangkan dengan sebenarnya bahwa:</p>
            
            <table class="data-table-doc section-data">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $permohonan->nama_pemohon ?? '[Nama Pemohon]' }}</td>
                </tr>
                <tr>
                    <td>NIK</td>
                    <td>:</td>
                    <td>{{ $permohonan->nik_pemohon ?? '[NIK Pemohon]' }}</td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ $permohonan->jenis_kelamin ?? '[Jenis Kelamin]' }}</td>
                </tr>
                <tr>
                    <td>Tempat, Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{ $permohonan->tempat_lahir ?? '[Tempat Lahir]' }}, {{ $permohonan->tanggal_lahir ? \Carbon\Carbon::parse($permohonan->tanggal_lahir)->isoFormat('D MMMM YYYY') : '[Tanggal Lahir]' }}</td>
                </tr>
                <tr>
                    <td>Warganegara / Agama</td>
                    <td>:</td>
                    <td>{{ $permohonan->warganegara_agama ?? '[Warganegara / Agama]' }}</td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ $permohonan->pekerjaan ?? '[Pekerjaan]' }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $permohonan->alamat_pemohon ?? '[Alamat Pemohon]' }}</td>
                </tr>
            </table>

            <p style="margin-top: 10px;">Sesuai dengan keterangan yang bersangkutan benar nama tersebut di atas mempunyai usaha sebagai berikut:</p>
            <table class="data-table-doc section-data">
                <tr>
                    <td>Nama Usaha</td>
                    <td>:</td>
                    <td>{{ $permohonan->nama_usaha ?? '[Nama Usaha]' }}</td>
                </tr>
                <tr>
                    <td>Alamat Usaha</td>
                    <td>:</td>
                    <td>{{ $permohonan->alamat_usaha ?? '[Alamat Usaha]' }}</td>
                </tr>
                {{-- Jika tujuan memang dihilangkan total dari model dan tidak ada input lagi --}}
                {{-- Baris untuk tujuan bisa dihilangkan, atau diganti dengan teks generik jika masih diperlukan --}}
                {{-- Contoh teks generik:
                <tr>
                    <td>Keperluan</td>
                    <td>:</td>
                    <td>Untuk kelengkapan administrasi usaha yang bersangkutan.</td>
                </tr>
                --}}
            </table>
            
            <p style="margin-top: 15px;" class="indent">Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p> 
        </div>

        <div class="signature-doc clearfix">
            Kumantan, {{ $tanggal_surat ?? \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>
            {{ $jabatan_kepala_desa ?? 'Kepala Desa Kumantan' }},<br>
            <div class="signature-area-doc">
                {{-- Pastikan path ke ttd_kepala_desa.png benar dan gambar tersedia --}}
                <img src="{{ public_path('sbadmin/img/ttd_kepala_desa.png') }}" alt="Tanda Tangan Kepala Desa">
            </div>
            <u>{{ $nama_kepala_desa ?? 'FIRDAUS, S.Pd' }}</u><br>
            {{-- NIP atau detail lain jika ada --}}
        </div>
        <div class="footer">
            <p>Dokumen ini dicetak secara elektronik oleh Sistem Informasi Layanan Desa Kumantan</p>
        </div>
    </div>
</body>
</html>