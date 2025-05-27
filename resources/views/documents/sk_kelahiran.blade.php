<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Kelahiran</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .header, .footer {
            text-align: center;
        }
        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
        }
        .kop-surat img {
            width: 80px; /* Sesuaikan ukuran logo */
            height: auto;
            float: left;
            margin-right: 15px;
        }
        .kop-surat h3, .kop-surat h4 {
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .kop-surat h3 { font-size: 14pt; }
        .kop-surat h4 { font-size: 12pt; }
        .kop-surat p {
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }
        .kop-surat hr {
            border: 2px solid black;
            margin-top: 5px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0 10px 0;
            font-size: 14pt;
        }
        .nomor-surat {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 20px;
        }
        .content {
            text-align: justify;
            margin-bottom: 30px;
        }
        .indent {
            text-indent: 40px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table.data-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        table.data-table td:first-child {
            width: 30%; /* Lebar label */
        }
        table.data-table td:nth-child(2) {
            width: 5%; /* Lebar titik dua */
        }
        .signature {
            width: 40%;
            float: right;
            text-align: center;
            margin-top: 30px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="kop-surat clearfix">
            <img src="{{ public_path('img/logo_mukomuko.png') }}" alt="Logo Mukomuko"> {{-- Ganti dengan path logo Anda --}}
            <h4>PEMERINTAH KABUPATEN MUKOMUKO</h4>
            <h4>KECAMATAN IPUH</h4>
            <h3>DESA SEMUNDAM</h3>
            <p>Alamat : Desa Semundam, Kecamatan Ipuh, Kabupaten Mukomuko Kode Pos 38364</p>
            <hr>
        </div>

        <div class="title">
            SURAT KETERANGAN KELAHIRAN
        </div>
        <div class="nomor-surat">
            NO : {{ $nomor_surat ?? '01/SKK/DS/SM/' . \Carbon\Carbon::now()->translatedFormat('m/Y') }}
        </div>

        <div class="content">
            <p class="indent">Yang bertanda tangan di bawah ini:</p>

            <table class="data-table">
                <tr><td>Nama</td><td>:</td><td>JAFRI . R</td></tr> {{-- Ganti dengan nama kepala desa --}}
                <tr><td>Jabatan</td><td>:</td><td>Kepala Desa Kumantan</td></tr>
                <tr><td>Kecamatan</td><td>:</td><td>Ipuh</td></tr>
                <tr><td>Kabupaten</td><td>:</td><td>Mukomuko</td></tr>
            </table>

            <p class="indent">Dengan ini menerangkan dengan sesungguhnya telah lahir seorang anak:</p>

            <table class="data-table">
                <tr><td>Nama</td><td>:</td><td>{{ $permohonan->nama_anak }}</td></tr>
                <tr><td>Tempat tanggal lahir</td><td>:</td><td>{{ $permohonan->tempat_lahir_anak }}, {{ \Carbon\Carbon::parse($permohonan->tanggal_lahir_anak)->translatedFormat('d F Y') }}</td></tr>
                <tr><td>Jenis kelamin</td><td>:</td><td>{{ $permohonan->jenis_kelamin_anak }}</td></tr>
                <tr><td>Agama</td><td>:</td><td>{{ $permohonan->agama_anak }}</td></tr>
                <tr><td>Alamat</td><td>:</td><td>{{ $permohonan->alamat_anak }}</td></tr>
            </table>

            <p>Orang tersebut di atas adalah anak dari:</p>

            <table class="data-table">
                <tr><td>Nama ayah</td><td>:</td><td>{{ $permohonan->nama_ayah }}</td></tr>
                <tr><td>Nama Ibu</td><td>:</td><td>{{ $permohonan->nama_ibu }}</td></tr>
            </table>

            <p class="indent">Demikianlah surat keterangan kelahiran ini kami buat dengan sebenarnya dan dapat dipergunakan seperlunya.</p>
        </div>

        <div class="signature clearfix">
            Semundam, {{ \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses ?? \Carbon\Carbon::now())->translatedFormat('d F Y') }}<br>
            Kepala Desa Semundam<br><br><br><br>
            (JAFRI.R)<br> {{-- Ganti dengan nama kepala desa --}}
        </div>

        <div class="footer" style="position: absolute; bottom: 20px; left: 0; right: 0;">
            <p style="font-size: 8pt; text-align: center;">Dokumen ini dicetak secara elektronik oleh Sistem Informasi Layanan Desa Semundam</p>
        </div>
    </div>
</body>
</html>
