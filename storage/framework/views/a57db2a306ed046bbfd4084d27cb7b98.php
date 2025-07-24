<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengantar Nikah</title>
    <style>
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
        <div class="kop-surat">
            <img src="<?php echo e(public_path('sbadmin/img/logo_kampar.png')); ?>" alt="Logo Desa" class="logo">
            <h1>PEMERINTAH KABUPATEN KAMPAR</h1>
            <h2>KECAMATAN BANGKINANG KOTA</h2>
            <h3>KEPALA DESA KUMANTAN</h3>
            <p>Alamat: JL. Mahmud Marzuki, Kelurahan Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, Kode Pos 28463</p>
        </div>

        <div class="judul-surat">
            <h4>SURAT PENGANTAR NIKAH</h4>
        </div>
        <div class="nomor-surat">
            Nomor : <?php echo e($permohonan->nomor_surat ?? '[NOMOR SURAT]'); ?>

        </div>

        <div class="isi-surat">
            <p class="paragraf">Yang bertanda tangan di bawah ini Kepala Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar menerangkan dengan sebenarnya bahwa :</p>
            
            <p class="paragraf-non-indent"><strong>I. Calon Suami</strong></p>
            <table class="tabel-data">
                <tr><td class="label">Nama</td><td class="separator">:</td><td class="data"><?php echo e(strtoupper($permohonan->nama_pria ?? '-')); ?></td></tr>
                <tr><td class="label">NIK</td><td class="separator">:</td><td><?php echo e($permohonan->nik_pria ?? '-'); ?></td></tr>
                <tr><td class="label">Tempat, Tanggal Lahir</td><td class="separator">:</td><td><?php echo e($permohonan->tempat_lahir_pria ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_pria ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pria)->isoFormat('D MMMM YYYY') : '-'); ?></td></tr>
                <tr><td class="label">Alamat</td><td class="separator">:</td><td><?php echo e($permohonan->alamat_pria ?? '-'); ?></td></tr>
            </table>

            <p class="paragraf-non-indent"><strong>II. Calon Istri</strong></p>
            <table class="tabel-data">
                <tr><td class="label">Nama</td><td class="separator">:</td><td class="data"><?php echo e(strtoupper($permohonan->nama_wanita ?? '-')); ?></td></tr>
                <tr><td class="label">NIK</td><td class="separator">:</td><td><?php echo e($permohonan->nik_wanita ?? '-'); ?></td></tr>
                <tr><td class="label">Tempat, Tanggal Lahir</td><td class="separator">:</td><td><?php echo e($permohonan->tempat_lahir_wanita ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_wanita ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_wanita)->isoFormat('D MMMM YYYY') : '-'); ?></td></tr>
                <tr><td class="label">Alamat</td><td class="separator">:</td><td><?php echo e($permohonan->alamat_wanita ?? '-'); ?></td></tr>
            </table>
          
            <p class="paragraf">Demikian surat pengantar ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="tanda-tangan clearfix">
            <div class="blok-ttd">
                Desa Kumantan, <?php echo e($permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->isoFormat('D MMMM YYYY') : \Carbon\Carbon::now()->isoFormat('D MMMM YYYY')); ?><br>
                Kepala Desa Kumantan<br>
                <div class="ttd-area">
                    <img src="<?php echo e(public_path('sbadmin/img/ttd_kepala_desa.png')); ?>" alt="Tanda Tangan Kepala Desa" style="max-height: 80px; max-width: 150px;">
                </div>
                <strong class="nama-pejabat">FIRDAUS, S.Pd</strong><br>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/documents/sk_nikah.blade.php ENDPATH**/ ?>