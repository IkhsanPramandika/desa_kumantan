<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Kelahiran</title>
    <style>
        body {
            font-family: 'Calibri', sans-serif; /* Font Calibri */
            font-size: 11pt; /* Ukuran font dasar sedikit dikecilkan untuk efisiensi ruang */
            line-height: 1.3; /* Mengurangi jarak antar baris */
            margin: 0;
            padding: 15px; /* Mengurangi padding halaman */
        }
        .container {
            width: 90%; /* Lebar container diperbesar untuk memaksimalkan ruang */
            margin: auto;
            padding: 10px; /* Mengurangi padding container */
        }
        .header, .footer {
            text-align: center;
        }
        .kop-surat {
            text-align: center;
            margin-bottom: 15px; /* Mengurangi margin bawah kop surat */
            padding-bottom: 5px; /* Sedikit padding di bawah kop surat */
        }
        .kop-surat img {
            width: 50px; /* Ukuran logo diperkecil menjadi 50px */
            height: auto;
            float: left;
            margin-right: 15px;
            vertical-align: middle; /* Memastikan logo sejajar dengan teks */
        }
        .kop-surat div { /* Kontainer untuk teks kop surat */
            overflow: hidden; /* Clear float */
        }
        .kop-surat h3, .kop-surat h4 {
            margin: 0;
            padding: 0;
            line-height: 1.1; /* Mengurangi jarak antar baris di kop */
            font-weight: normal; /* Opsional: membuat teks kop tidak terlalu tebal */
        }
        .kop-surat h4 {
            font-size: 10.5pt; /* Menyesuaikan ukuran font */
        }
        .kop-surat h3 {
            font-size: 12.5pt; /* Menyesuaikan ukuran font */
            font-weight: bold;
        }
        .kop-surat p {
            margin: 0;
            padding: 0;
            font-size: 9pt; /* Menyesuaikan ukuran font */
        }
        .kop-surat hr {
            border: 1.5px solid black; /* Tebal garis dikurangi sedikit */
            margin-top: 5px;
            clear: both; /* Penting: Pastikan HR di bawah float */
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 8px 0; /* Mengurangi margin atas dan bawah */
            font-size: 12.5pt; /* Menyesuaikan ukuran font */
        }
        .nomor-surat {
            text-align: center;
            font-size: 10.5pt; /* Menyesuaikan ukuran font */
            margin-bottom: 15px; /* Mengurangi margin bawah nomor surat */
        }
        .content {
            text-align: justify;
            margin-bottom: 20px; /* Mengurangi margin bawah konten */
        }
        .content p {
            margin-bottom: 5px; /* Mengurangi margin bawah paragraf */
        }
        .indent {
            text-indent: 30px; /* Indentasi sedikit dikurangi */
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0; /* Mengurangi margin atas dan bawah tabel */
        }
        table.data-table td {
            padding: 1px 0; /* Mengurangi padding sel tabel */
            vertical-align: top;
            font-size: 11pt; /* Memastikan font di tabel sesuai body */
        }
        table.data-table td:first-child {
            width: 30%;
        }
        table.data-table td:nth-child(2) {
            width: 3%;
        }
        .signature {
            width: 45%; /* Diperlebar sedikit agar TTD muat */
            float: right;
            text-align: center;
            margin-top: 20px; /* Mengurangi margin atas tanda tangan */
            font-size: 11pt; /* Memastikan font di tanda tangan sesuai body */
        }
       .signature-area {
            min-height: 80px; /* Memberi ruang untuk TTD */
            margin-top: 5px;
            display: flex; /* Menggunakan flexbox untuk memposisikan gambar */
            justify-content: center; /* Pusatkan secara horizontal */
            align-items: center; /* Pusatkan secara vertikal */
        }
        .signature-area img {
            max-width: 150px; /* Atur lebar maksimal gambar TTD */
            height: auto;
            margin-bottom: 5px; /* Beri sedikit jarak di bawah gambar */
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .footer {
            position: absolute;
            bottom: 15px; /* Mengurangi jarak footer dari bawah */
            left: 0;
            right: 0;
        }
        .footer p {
            font-size: 7.5pt; /* Ukuran font footer sedikit lebih kecil */
            text-align: center;
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

        <div class="title">
            SURAT KETERANGAN KELAHIRAN
        </div>
        <div class="nomor-surat">
            NO : {{ $nomor_surat ?? '01/SKK/DS/SM/' . \Carbon\Carbon::now()->translatedFormat('m/Y') }}
        </div>

        <div class="content">
            <p class="indent">Yang bertanda tangan di bawah ini:</p>

            <table class="data-table">
                <tr><td>Nama</td><td>:</td><td>FIRDAUS, S.Pd</td></tr>
                <tr><td>Jabatan</td><td>:</td><td>Kepala Desa Kumantan</td></tr>
                <tr><td>Kecamatan</td><td>:</td><td>Bangkinang Kota</td></tr>
                <tr><td>Kabupaten</td><td>:</td><td>Kampar</td></tr>
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
            Desa Kumantan, {{ \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses ?? \Carbon\Carbon::now())->translatedFormat('d F Y') }}<br>
            Kepala Desa Kumantan<br>
            <div class="signature-area">
                {{-- Masukkan gambar tanda tangan di sini --}}
                <img src="{{ public_path('sbadmin/img/ttd_kepala_desa.png') }}" alt="Tanda Tangan Kepala Desa">
            </div>
            FIRDAUS, S.Pd<br>
        </div>

        <div class="footer">
            <p>Dokumen ini dicetak secara elektronik oleh Sistem Informasi Layanan Desa Kumantan</p>
        </div>
    </div>
</body>
</html>
