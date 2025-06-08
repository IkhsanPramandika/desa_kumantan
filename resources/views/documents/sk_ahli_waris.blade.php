<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Ahli Waris</title>
    <style>
        body {
            font-family: 'Calibri', sans-serif;
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
        .header, .footer {
            text-align: center;
        }
        .kop-surat {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 5px;
        }
        .kop-surat img {
            width: 50px;
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
        .kop-surat h4 {
            font-size: 10.5pt;
        }
        .kop-surat h3 {
            font-size: 12.5pt;
            font-weight: bold;
        }
        .kop-surat p {
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }
        .kop-surat hr {
            border: 1.5px solid black;
            margin-top: 5px;
            clear: both;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 8px 0;
            font-size: 12.5pt;
        }
        .nomor-surat {
            text-align: center;
            font-size: 10.5pt;
            margin-bottom: 15px;
        }
        .content {
            text-align: justify;
            margin-bottom: 20px;
        }
        .content p {
            margin-bottom: 5px;
        }
        .indent {
            text-indent: 30px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        table.data-table td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 11pt;
        }
        table.data-table td:first-child {
            width: 30%;
        }
        table.data-table td:nth-child(2) {
            width: 3%;
        }
        .signature-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .signature-left, .signature-right {
            width: 48%;
            text-align: center;
            font-size: 11pt;
        }
        .signature-right {
            text-align: right;
        }
        .signature-area {
            min-height: 80px;
            margin-top: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .signature-area img {
            max-width: 150px;
            height: auto;
            margin-bottom: 5px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .signature-bottom-hr {
            border: 1px solid black;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .footer {
            margin-top: 30px;
        }
        .footer p {
            font-size: 7.5pt;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="kop-surat clearfix">
            <img src="{{ public_path('sbadmin/img/logo_kampar.png') }}" alt="Logo Kabupaten Kampar">
            <div>
                <h4>PEMERINTAH KABUPATEN KAMPAR</h4>
                <h4>KECAMATAN BANGKINANG KOTA</h4>
                <h3>DESA KUMANTAN</h3>
                <p>Alamat : Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar Kode Pos 28463</p>
            </div>
            <hr>
        </div>

        <div class="title">
            SURAT KETERANGAN AHLI WARIS
        </div>
        <div class="nomor-surat">
            NO : {{ $nomor_surat ?? '01/SKAW/DS/SM/' . \Carbon\Carbon::now()->translatedFormat('m/Y') }}
        </div>

        <div class="content">
            <p class="indent">Yang bertanda Tangan di bawah ini, Kepala Desa/Kelurahan Sungai Kuning Kecamatan Singingi Kabupaten Kuantan Singingi menerangkan bahwa berdasarkan Surat Pernyataan Ahli Waris Tanggal {{ \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses ?? \Carbon\Carbon::now())->translatedFormat('d F Y') }} (terlampir) maka nama tersebut di bawah ini:</p>

            <p>Adalah Ahli Waris dari Almarhum <strong>{{ $permohonan->nama_pewaris }}</strong>, tempat Tinggal terakhir di {{ $permohonan->alamat_pewaris ?? 'Desa/Kelurahan Sungai Kuning' }}.</p>

            <p>Berikut adalah daftar ahli waris:</p>
            <table class="data-table">
                @if ($permohonan->daftar_ahli_waris && count($permohonan->daftar_ahli_waris) > 0)
                    @foreach ($permohonan->daftar_ahli_waris as $index => $ahli_waris)
                        <tr><td colspan="3"><strong>{{ $index + 1 }}. {{ $ahli_waris['nama'] ?? '' }}</strong></td></tr>
                        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;NIK</td><td>:</td><td>{{ $ahli_waris['nik'] ?? '' }}</td></tr>
                        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Hubungan</td><td>:</td><td>{{ $ahli_waris['hubungan'] ?? '' }}</td></tr>
                        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Alamat</td><td>:</td><td>{{ $ahli_waris['alamat'] ?? '' }}</td></tr>
                        @if (!$loop->last)
                            <tr><td colspan="3"><br></td></tr> {{-- Add a small space between heirs --}}
                        @endif
                    @endforeach
                @else
                    <tr><td colspan="3">Tidak ada data ahli waris.</td></tr>
                @endif
            </table>

            <p class="indent">Demikianlah Surat Keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="signature-group">
            <div class="signature-left">
                KETUA RT 18<br>
                <br><br><br>
                <u>JYANG</u>
            </div>
            <div class="signature-right">
                Sungai Kuning, {{ \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses ?? \Carbon\Carbon::now())->translatedFormat('d F Y') }}<br>
                <br>
                Diketahui Oleh:<br>
                KETUA RW 08<br>
                <br><br><br>
                <u>SARDI</u><br>
                <br>
                Nomor : {{ $nomor_surat ?? '01/SKAW/DS/SM/' . \Carbon\Carbon::now()->translatedFormat('m/Y') }}<br>
                Tanggal : {{ \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses ?? \Carbon\Carbon::now())->translatedFormat('d F Y') }}<br>
                <br>
                Mengetahui<br>
                KEPALA DESA SUNGAI KUNING<br>
                <div class="signature-area">
                    <img src="{{ public_path('sbadmin/img/ttd_kepala_desa.png') }}" alt="Tanda Tangan Kepala Desa">
                </div>
                <u>SUHERI</u><br>
                <hr class="signature-bottom-hr">
            </div>
        </div>

        <div class="footer">
            <p>Dokumen ini dicetak secara elektronik oleh Sistem Informasi Layanan Desa Kumantan</p>
        </div>
    </div>
</body>
</html>
