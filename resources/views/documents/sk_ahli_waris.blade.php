<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Ahli Waris</title>
    {{-- [PENINGKATAN] Style dipindahkan ke sini agar lebih rapi --}}
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 2cm; }
        .kop-surat { text-align: center; line-height: 1.2; border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat img { width: 80px; height: auto; position: absolute; left: 2cm; }
        .kop-surat h1, .kop-surat h2, .kop-surat p { margin: 0; }
        .kop-surat h1 { font-size: 18pt; font-weight: bold; }
        .kop-surat h2 { font-size: 16pt; }
        .kop-surat p { font-size: 10pt; }
        .judul-surat { text-align: center; margin-bottom: 5px; }
        .judul-surat h3 { font-size: 14pt; font-weight: bold; text-decoration: underline; margin: 0; }
        .nomor-surat { text-align: center; font-size: 12pt; margin-bottom: 20px; }
        .paragraf { text-indent: 50px; text-align: justify; margin-bottom: 15px; }
        .data-table { border-collapse: collapse; width: 100%; margin-left: 50px; }
        .data-table td { padding: 2px; vertical-align: top; }
        .data-table td.label { width: 35%; }
        .data-table td.separator { width: 5%; }
        .tanda-tangan { margin-top: 50px; }
        .ttd-kanan { width: 45%; float: right; text-align: center; }
        .ttd-kiri { width: 45%; float: left; text-align: center; }
        .nama-pejabat { font-weight: bold; text-decoration: underline; margin-top: 80px; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="container">
        <div class="kop-surat">
            {{-- [PERBAIKAN] Pastikan path gambar ini benar di folder public Anda --}}
            <img src="{{ public_path('img/logo_desa.png') }}" alt="Logo Desa">
            <h1>PEMERINTAH KABUPATEN KAMPAR</h1>
            <h2>KECAMATAN BANGKINANG KOTA</h2>
            <h2>KEPALA DESA KUMANTAN</h2>
            <p>Alamat: Desa Kumantan, Kecamatan Bangkinang Kota, Kode Pos 28463</p>
        </div>

        <div class="judul-surat">
            <h3>SURAT KETERANGAN AHLI WARIS</h3>
        </div>
        <div class="nomor-surat">
            {{-- [PERBAIKAN] Mengakses nomor_surat dari objek permohonan --}}
            Nomor : {{ $permohonan->nomor_surat ?? 'BELUM ADA NOMOR' }}
        </div>

        <div class="content">
            <p class="paragraf">Yang bertanda tangan di bawah ini, Kepala Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, dengan ini menerangkan bahwa:</p>

            <table class="data-table">
                @if(isset($permohonan->daftar_ahli_waris) && is_array($permohonan->daftar_ahli_waris) && count($permohonan->daftar_ahli_waris) > 0)
                    {{-- [PENINGKATAN] Data pemohon utama ditampilkan pertama --}}
                    @foreach ($permohonan->daftar_ahli_waris as $index => $ahli_waris)
                        <tr>
                            <td class="label" style="padding-left: 20px;"><strong>{{ $index + 1 }}. Nama Lengkap</strong></td>
                            <td class="separator">:</td>
                            <td><strong>{{ $ahli_waris['nama'] ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">NIK</td>
                            <td>:</td>
                            <td>{{ $ahli_waris['nik'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">Hubungan dalam Keluarga</td>
                            <td>:</td>
                            <td>{{ $ahli_waris['hubungan'] ?? 'N/A' }}</td>
                        </tr>
                         <tr>
                            <td style="padding-left: 20px;">Alamat</td>
                            <td>:</td>
                            <td>{{ $ahli_waris['alamat'] ?? 'N/A' }}</td>
                        </tr>
                        <tr><td colspan="3" style="height: 10px;"></td></tr>
                    @endforeach
                @else
                    <tr><td colspan="3">Data Ahli Waris tidak ditemukan.</td></tr>
                @endif
            </table>

            <p class="paragraf">Adalah benar merupakan Ahli Waris yang sah dari Almarhum/Almarhumah:</p>
            
             <table class="data-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    {{-- [PENINGKATAN] Menambahkan fallback jika data null --}}
                    <td><strong>{{ $permohonan->nama_pewaris ?? 'Nama Pewaris Tidak Ada' }}</strong></td>
                </tr>
                 <tr>
                    <td class="label">Alamat Terakhir</td>
                    <td>:</td>
                    <td>{{ $permohonan->alamat_pewaris ?? 'Alamat Pewaris Tidak Ada' }}</td>
                </tr>
            </table>

            <p class="paragraf">Demikian Surat Keterangan Ahli Waris ini kami buat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya. Atas perhatiannya kami ucapkan terima kasih.</p>
        </div>

        <div class="tanda-tangan clearfix">
            <div class="ttd-kanan">
                {{-- [PENINGKATAN] Membuat data dinamis --}}
                Kumantan, {{ \Carbon\Carbon::parse($permohonan->tanggal_selesai_proses)->translatedFormat('d F Y') }}<br>
                Kepala Desa Kumantan,
                <div class="nama-pejabat">SUHERI</div>
            </div>
        </div>
    </div>
</body>
</html>