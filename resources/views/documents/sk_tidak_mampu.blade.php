<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Tidak Mampu</title>
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
        .title-document {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 8px 0;
            font-size: 12.5pt;
        }
        .nomor-surat-doc {
            text-align: center;
            font-size: 10.5pt;
            margin-bottom: 15px;
        }
        .content-doc {
            text-align: justify;
            margin-bottom: 20px;
        }
        .content-doc p {
            margin-bottom: 5px;
        }
        .indent {
            text-indent: 30px;
        }
        table.data-table-doc {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        table.data-table-doc td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 11pt;
        }
        table.data-table-doc td:first-child {
            width: 30%;
            padding-left: 30px;
        }
        table.data-table-doc td:nth-child(2) {
            width: 3%;
            text-align: center;
        }
        .signature-doc {
            width: 45%;
            float: right;
            text-align: center;
            margin-top: 20px;
            font-size: 11pt;
        }
        .signature-area-doc {
            min-height: 60px;
            margin-top: 5px;
            margin-bottom: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .signature-area-doc img {
            max-width: 120px;
            max-height: 60px;
            height: auto;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .section-data {
            margin-left: 30px;
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

        <div class="title-document">SURAT KETERANGAN TIDAK MAMPU</div>
        <div class="nomor-surat-doc">
            NO : {{ $nomor_surat ?? '[NOMOR_SURAT]' }}
        </div>

        <div class="content-doc">
            <p class="indent">Yang bertanda tangan di bawah ini Kepala Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, dengan ini menerangkan bahwa:</p>

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
                    <td>{{ $permohonan->jenis_kelamin_pemohon ?? '[Jenis Kelamin]' }}</td> {{-- PERBAIKAN --}}
                </tr>
                <tr>
                    <td>Tempat, Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{ $permohonan->tempat_lahir_pemohon ?? '[Tempat Lahir]' }}, {{ $permohonan->tanggal_lahir_pemohon ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pemohon)->isoFormat('D MMMM YYYY') : '[Tanggal Lahir]' }}</td> {{-- PERBAIKAN --}}
                </tr>
                <tr>
                    <td>Warganegara / Agama</td>
                    <td>:</td>
                    <td>{{ $permohonan->kewarganegaraan_pemohon ?? '[Warganegara]' }} / {{ $permohonan->agama_pemohon ?? '[Agama]' }}</td> {{-- PERBAIKAN --}}
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ $permohonan->pekerjaan_pemohon ?? '[Pekerjaan]' }}</td> {{-- PERBAIKAN --}}
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $permohonan->alamat_pemohon ?? '[Alamat Pemohon]' }}</td>
                </tr>
            </table>

            <p style="margin-top: 10px;" class="indent">
                Berdasarkan data yang ada dan pengamatan kami bahwa benar nama tersebut di atas merupakan warga yang tergolong tidak mampu.
            </p>

            <p style="margin-top: 10px;" class="ind
             nbent">
                Surat keterangan ini dibuat sebagai kelengkapan administrasi {{ $permohonan->keperluan_surat ?? '[Keperluan]' }}. {{-- PERBAIKAN --}}
            </p>

            <p style="margin-top: 10px;" class="indent">
                Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
            </p>
        </div>

        <div class="signature-doc clearfix">
            Kumantan, {{ $tanggal_surat_dibuat ?? \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br> {{-- MENGGUNAKAN VARIABEL YANG DIKIRIM DARI CONTROLLER --}}
            {{ $jabatan_pejabat ?? 'Kepala Desa Kumantan' }},<br> {{-- MENGGUNAKAN VARIABEL YANG DIKIRIM DARI CONTROLLER --}}
            <div class="signature-area-doc">
                <img src="{{ public_path('sbadmin/img/ttd_kepala_desa.png') }}" alt="Tanda Tangan Kepala Desa">
            </div>
            <u>{{ $nama_pejabat ?? 'FIRDAUS, S.Pd' }}</u><br> {{-- MENGGUNAKAN VARIABEL YANG DIKIRIM DARI CONTROLLER --}}
        </div>

        <div class="footer">
            <p>Dokumen ini dicetak secara elektronik oleh Sistem Informasi Layanan Desa Kumantan</p>
        </div>
    </div>
</body>
</html>