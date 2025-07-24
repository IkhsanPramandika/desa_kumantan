<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Ahli Waris</title>
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
            <p>Alamat: Desa Kumantan, Kecamatan Bangkinang Kota, Kode Pos 28463</p>
        </div>

        <div class="judul-surat">
            <h4>SURAT KETERANGAN AHLI WARIS</h4>
        </div>
        <div class="nomor-surat">
            Nomor : <?php echo e($permohonan->nomor_surat ?? '[NOMOR SURAT]'); ?>

        </div>

        <div class="isi-surat">
            <p class="paragraf">Yang bertanda tangan di bawah ini, Kepala Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, dengan ini menerangkan bahwa nama-nama di bawah ini:</p>

            <table class="tabel-data">
                <?php
                    $ahliWarisList = is_string($permohonan->daftar_ahli_waris) ? json_decode($permohonan->daftar_ahli_waris, true) : $permohonan->daftar_ahli_waris;
                ?>

                <?php if(!empty($ahliWarisList) && is_array($ahliWarisList)): ?>
                    <?php $__currentLoopData = $ahliWarisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ahli_waris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="label"><strong><?php echo e($index + 1); ?>. Nama Lengkap</strong></td>
                            <td class="separator">:</td>
                            <td class="data"><?php echo e(strtoupper($ahli_waris['nama'] ?? '-')); ?></td>
                        </tr>
                         <tr>
                            <td class="label">NIK</td>
                            <td class="separator">:</td>
                            <td><?php echo e($ahli_waris['nik'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="label">Hubungan dalam Keluarga</td>
                            <td class="separator">:</td>
                            <td><?php echo e($ahli_waris['hubungan'] ?? '-'); ?></td>
                        </tr>
                         <tr>
                            <td class="label">Alamat</td>
                             <td class="separator">:</td>
                            <td><?php echo e($ahli_waris['alamat'] ?? '-'); ?></td>
                        </tr>
                        <tr><td colspan="3" style="height: 10px;"></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <tr><td colspan="3">Data Ahli Waris tidak ditemukan.</td></tr>
                <?php endif; ?>
            </table>

            <p class="paragraf">Adalah benar merupakan Ahli Waris yang sah dari Almarhum/Almarhumah:</p>
            
            <table class="tabel-data">
                <tr>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    <td class="data"><?php echo e(strtoupper($permohonan->nama_pewaris ?? '-')); ?></td>
                </tr>
                 <tr>
                    <td class="label">NIK</td>
                    <td class="separator">:</td>
                    <td><?php echo e($permohonan->nik_pewaris ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Tempat/Tgl Lahir</td>
                    <td class="separator">:</td>
                    <td><?php echo e($permohonan->tempat_lahir_pewaris ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pewaris)->isoFormat('D MMMM YYYY') : '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Meninggal</td>
                    <td class="separator">:</td>
                    <td><?php echo e($permohonan->tanggal_meninggal_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_meninggal_pewaris)->isoFormat('D MMMM YYYY') : '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Alamat Terakhir</td>
                    <td class="separator">:</td>
                    <td><?php echo e($permohonan->alamat_pewaris ?? '-'); ?></td>
                </tr>
            </table>

            <p class="paragraf">Demikian Surat Keterangan Ahli Waris ini kami buat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="tanda-tangan clearfix">
            <div class="blok-ttd">
                Kumantan, <?php echo e($permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->isoFormat('D MMMM YYYY') : \Carbon\Carbon::now()->isoFormat('D MMMM YYYY')); ?><br>
                Kepala Desa Kumantan,
                <div class="ttd-area"></div>
                <div class="nama-pejabat">FIRDAUS, S.Pd</div>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/documents/sk_ahli_waris.blade.php ENDPATH**/ ?>