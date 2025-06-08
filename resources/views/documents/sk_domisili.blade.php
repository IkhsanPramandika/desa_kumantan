<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Domisili</title>
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
            position: relative;
            box-sizing: border-box;
        }

        .kop-surat {
            text-align: center;
            margin-bottom: 15px; 
            padding-bottom: 5px;
            position: relative; 
        }
        .kop-surat img.logo-desa {
            width: 60px; 
            height: auto;
            position: absolute; 
            left: 0px; 
            top: 0px; 
        }
        .kop-surat div.text-kop {
            margin-left: 75px; 
            text-align: center; 
        }
        .kop-surat h1, .kop-surat h2, .kop-surat h3, .kop-surat p {
            margin: 0;
            padding: 0;
            line-height: 1.2; 
        }
        .kop-surat h1 { /* PEMERINTAH KABUPATEN */
            font-size: 10.5pt; 
            font-weight: normal; 
        }
        .kop-surat h2 { /* KECAMATAN */
            font-size: 10.5pt; 
            font-weight: normal;
        }
        .kop-surat h3 { /* DESA */
            font-size: 12.5pt; 
            font-weight: bold;
            margin-top: 1px;
        }
        .kop-surat p.alamat-kop {
            font-size: 9pt;
            margin-top: 1px;
        }
        .kop-surat hr.garis-kop {
            border: 0;
            border-top: 1.5px solid black; 
            margin-top: 5px;
            margin-bottom: 15px; 
            clear: both;
        }

        .title-document { 
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 20px; 
            margin-bottom: 5px;
            font-size: 12.5pt; 
        }
        .nomor-surat-doc {
            text-align: center;
            font-size: 11pt; 
            margin-bottom: 25px;
        }

        .content-doc {
            text-align: justify;
            line-height: 1.5; 
        }
        .content-doc p {
            margin-top: 0;
            margin-bottom: 8px; 
        }
        .indent {
            text-indent: 30px; 
        }

        table.data-table-doc { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 8px;
        }
        table.data-table-doc td {
            padding: 1.5px 0; 
            vertical-align: top;
            font-size: 11pt;
        }
        table.data-table-doc td.label-data { 
            width: 30%; 
            padding-left: 30px; 
        }
        table.data-table-doc td.separator-data { 
            width: 3%;
            text-align: left; 
        }
        
        .signature-doc { 
            width: 45%; 
            float: right; 
            text-align: center;
            margin-top: 20px; 
            font-size: 11pt;
        }
        .signature-doc .tempat-tanggal-surat,
        .signature-doc .an-jabatan,
        .signature-doc .jabatan-penandatangan {
             display: block; /* Agar setiap baris terpisah */
        }
        .signature-doc .jabatan-penandatangan {
             font-weight: normal; 
        }
        .signature-area-doc { 
            min-height: 60px; 
            margin-top: 5px;
            margin-bottom: 5px; 
            display: flex; 
            justify-content: center; 
            align-items: center;
        }
        .signature-area-doc img { /* Untuk gambar TTD + Stempel jika ada */
            max-width: 120px; 
            max-height: 60px; 
            height: auto;
        }
        .signature-doc .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 0px; 
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .footer-doc { 
            margin-top: 40px; 
            text-align: center;
        }
        .footer-doc p {
            font-size: 7.5pt;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="kop-surat clearfix">
            <img src="{{ public_path('sbadmin/img/logo_kampar.png') }}" alt="Logo Kabupaten Kampar" class="logo-desa">
            <div class="text-kop">
                <h1>PEMERINTAH KABUPATEN {{ strtoupper($nama_kabupaten ?? 'KAMPAR') }}</h1>
                <h2>KECAMATAN {{ strtoupper($nama_kecamatan ?? 'KAMPAR KIRI') }}</h2>
                <h3>DESA {{ strtoupper($nama_desa ?? 'TANJUNG MAS') }}</h3>
                <p class="alamat-kop">Alamat : {{ $alamat_kantor_desa ?? 'Desa Tanjung Mas Kecamatan Kampar Kiri' }} Kode Pos {{ $kode_pos ?? '28471' }}</p>
            </div>
        </div>
        <hr class="garis-kop">

        <div class="title-document">
            SURAT KETERANGAN DOMISILI
        </div>
        <div class="nomor-surat-doc">
            Nomor : {{ $nomor_surat ?? '[NOMOR SURAT]' }}
        </div>

        <div class="content-doc">
            <p class="indent">Yang bertanda tangan di bawah ini {{ $jabatan_pejabat ?? 'Sekretaris Desa' }} {{ $nama_desa ?? '[Nama Desa]' }}, Kecamatan {{ $nama_kecamatan ?? '[Nama Kecamatan]' }}, Kabupaten {{ $nama_kabupaten ?? '[Nama Kabupaten]' }}, menerangkan dengan sebenarnya bahwa:</p>

            <table class="data-table-doc">
                <tr>
                    <td class="label-data">Nama</td>
                    <td class="separator-data">:</td>
                    <td><strong>{{ strtoupper($permohonan->nama_pemohon_atau_lembaga ?? '[NAMA PEMOHON/LEMBAGA]') }}</strong></td>
                </tr>
                 @if($permohonan->nik_pemohon)
                 <tr>
                    <td class="label-data">NIK</td>
                    <td class="separator-data">:</td>
                    <td>{{ $permohonan->nik_pemohon }}</td>
                </tr>
                @endif
                @if($permohonan->tempat_lahir_pemohon && $permohonan->tanggal_lahir_pemohon)
                <tr>
                    <td class="label-data">Tempat/Tgl Lahir</td>
                    <td class="separator-data">:</td>
                    <td>{{ $permohonan->tempat_lahir_pemohon }}, {{ \Carbon\Carbon::parse($permohonan->tanggal_lahir_pemohon)->isoFormat('D MMMM YYYY') }}</td>
                </tr>
                @endif
                @if($permohonan->jenis_kelamin_pemohon)
                <tr>
                    <td class="label-data">Jenis Kelamin</td>
                    <td class="separator-data">:</td>
                    <td>{{ $permohonan->jenis_kelamin_pemohon }}</td>
                </tr>
                @endif
                @if($permohonan->pekerjaan_pemohon)
                <tr>
                    <td class="label-data">Pekerjaan</td>
                    <td class="separator-data">:</td>
                    <td>{{ $permohonan->pekerjaan_pemohon }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label-data">Alamat</td>
                    <td class="separator-data">:</td>
                    <td>{{ $permohonan->alamat_lengkap_domisili ?? '[Alamat Lengkap Domisili]' }}
                        @if($permohonan->rt_domisili || $permohonan->rw_domisili)
                            RT {{ $permohonan->rt_domisili ?? '-' }}/RW {{ $permohonan->rw_domisili ?? '-' }}
                        @endif
                        @if($permohonan->dusun_domisili)
                            Dusun {{ $permohonan->dusun_domisili }}
                        @endif
                         Desa {{ $nama_desa ?? '[Nama Desa]' }}
                    </td>
                </tr>
            </table>

            <p class="indent" style="margin-top: 10px;">Bahwa nama tersebut di atas adalah benar berdomisili dan menetap pada alamat tersebut di Desa {{ $nama_desa ?? '[Nama Desa]' }} Kecamatan {{ $nama_kecamatan ?? '[Nama Kecamatan]' }} Kabupaten {{ $nama_kabupaten ?? '[Nama Kabupaten]' }}.</p>
            
            <p class="indent">Surat Keterangan Domisili ini dibuat untuk keperluan: <strong>{{ $permohonan->keperluan_domisili ?? '[Keperluan Domisili]' }}</strong>.</p>

            <p class="indent">Demikian Surat Keterangan Domisili ini diberikan kepada yang bersangkutan, untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="signature-doc clearfix">
            <span class="tempat-tanggal-surat">{{ strtoupper($nama_desa ?? 'TANJUNG MAS') }}, {{ $tanggal_surat_dibuat ? \Carbon\Carbon::parse($tanggal_surat_dibuat)->isoFormat('D MMMM YYYY') : \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</span>
            <span class="an-jabatan">{{ $an_pejabat ?? 'A.N KEPALA DESA TANJUNG MAS' }},</span>
            <span class="jabatan-penandatangan">{{ strtoupper($jabatan_pejabat ?? 'SEKRETARIS DESA') }}</span>
            <div class="signature-area-doc">
                {{-- Jika ada gambar stempel dan TTD digital (PNG transparan), bisa diletakkan di sini --}}
                {{-- Contoh: <img src="{{ public_path('images/ttd_stempel_sekdes.png') }}" alt="TTD dan Stempel"> --}}
            </div>
            <p class="nama-pejabat"><u>{{ strtoupper($nama_pejabat ?? 'MUHAMMAD ANGGI R') }}</u></p>
        </div>

        <div class="footer-doc">
            <p>Dokumen ini dicetak secara elektronik oleh Sistem Informasi Layanan Desa {{ $nama_desa ?? '[Nama Desa]' }}</p>
        </div>
    </div>
</body>
</html>