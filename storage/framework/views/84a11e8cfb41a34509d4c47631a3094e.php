<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Domisili</title>
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
            <p>Alamat: JL. Mahmud Marzuki, Desa Kumantan, Kecamatan Bangkinang Kota, Kode Pos 28463</p>
        </div>

        <div class="judul-surat">
            <h4>SURAT KETERANGAN DOMISILI</h4>
        </div>
        <div class="nomor-surat">
            Nomor : <?php echo e($permohonan->nomor_surat ?? '[NOMOR SURAT]'); ?>

        </div>

        <div class="isi-surat">
            <p class="paragraf">Yang bertanda tangan di bawah ini Kepala Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, menerangkan dengan sebenarnya bahwa:</p>

            <table class="tabel-data">
                <tr>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    <td class="data"><?php echo e(strtoupper($permohonan->nama_pemohon_atau_lembaga ?? '[NAMA PEMOHON/LEMBAGA]')); ?></td>
                </tr>
                <?php if($permohonan->nik_pemohon): ?>
                <tr>
                    <td class="label">NIK</td>
                    <td class="separator">:</td>
                    <td><?php echo e($permohonan->nik_pemohon); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="separator">:</td>
                    <td><?php echo e($permohonan->alamat_lengkap_domisili ?? '[Alamat Lengkap Domisili]'); ?>, RT <?php echo e($permohonan->rt_domisili ?? '-'); ?>/RW <?php echo e($permohonan->rw_domisili ?? '-'); ?>, Desa Kumantan</td>
                </tr>
            </table>

            <p class="paragraf">Bahwa nama tersebut di atas adalah benar berdomisili dan menetap pada alamat tersebut di Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar.</p>
            
            <p class="paragraf">Surat Keterangan Domisili ini dibuat untuk keperluan: <strong><?php echo e($permohonan->keperluan_domisili ?? '[Keperluan Domisili]'); ?></strong>.</p>

            <p class="paragraf">Demikian Surat Keterangan Domisili ini diberikan kepada yang bersangkutan, untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="tanda-tangan clearfix">
            <div class="blok-ttd">
                Kumantan, <?php echo e($permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->isoFormat('D MMMM YYYY') : \Carbon\Carbon::now()->isoFormat('D MMMM YYYY')); ?><br>
                Kepala Desa Kumantan
                <div class="ttd-area"></div>
                <p class="nama-pejabat">FIRDAUS, S.Pd</p>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/documents/sk_domisili.blade.php ENDPATH**/ ?>