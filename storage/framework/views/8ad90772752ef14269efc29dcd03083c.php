<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Ahli Waris</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 2cm; }
        .container { width: 100%; }
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
        .data-table { border-collapse: collapse; width: 100%; margin-left: 50px; margin-bottom: 15px; }
        .data-table td { padding: 2px; vertical-align: top; }
        .data-table td.label { width: 35%; }
        .data-table td.separator { width: 5%; }
        .tanda-tangan { margin-top: 50px; }
        .ttd-kanan { width: 45%; float: right; text-align: center; }
        .nama-pejabat { font-weight: bold; text-decoration: underline; margin-top: 80px; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="container">
        <div class="kop-surat">
            
            <img src="<?php echo e(public_path('sbadmin/img/logo_kampar.png')); ?>" alt="Logo Desa">
            <h1>PEMERINTAH KABUPATEN KAMPAR</h1>
            <h2>KECAMATAN BANGKINANG KOTA</h2>
            <h2>KEPALA DESA KUMANTAN</h2>
            <p>Alamat: Desa Kumantan, Kecamatan Bangkinang Kota, Kode Pos 28463</p>
        </div>

        <div class="judul-surat">
            <h3>SURAT KETERANGAN AHLI WARIS</h3>
        </div>
        <div class="nomor-surat">
            Nomor : <?php echo e($permohonan->nomor_surat ?? '[NOMOR SURAT]'); ?>

        </div>

        <div class="content">
            <p class="paragraf">Yang bertanda tangan di bawah ini, Kepala Desa Kumantan, Kecamatan Bangkinang Kota, Kabupaten Kampar, dengan ini menerangkan bahwa nama-nama di bawah ini:</p>

            
            <table class="data-table">
                <?php
                    // Mengubah JSON string menjadi array untuk memastikan bisa di-loop
                    $ahliWarisList = is_string($permohonan->daftar_ahli_waris) ? json_decode($permohonan->daftar_ahli_waris, true) : $permohonan->daftar_ahli_waris;
                ?>

                <?php if(!empty($ahliWarisList) && is_array($ahliWarisList)): ?>
                    <?php $__currentLoopData = $ahliWarisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ahli_waris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="label" style="padding-left: 20px;"><strong><?php echo e($index + 1); ?>. Nama Lengkap</strong></td>
                            <td class="separator">:</td>
                            <td><strong><?php echo e(strtoupper($ahli_waris['nama'] ?? '-')); ?></strong></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">NIK</td>
                            <td>:</td>
                            <td><?php echo e($ahli_waris['nik'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">Hubungan dalam Keluarga</td>
                            <td>:</td>
                            <td><?php echo e($ahli_waris['hubungan'] ?? '-'); ?></td>
                        </tr>
                         <tr>
                            <td style="padding-left: 20px;">Alamat</td>
                            <td>:</td>
                            <td><?php echo e($ahli_waris['alamat'] ?? '-'); ?></td>
                        </tr>
                        <tr><td colspan="3" style="height: 10px;"></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <tr><td colspan="3">Data Ahli Waris tidak ditemukan.</td></tr>
                <?php endif; ?>
            </table>

            <p class="paragraf">Adalah benar merupakan Ahli Waris yang sah dari Almarhum/Almarhumah:</p>
            
            
            <table class="data-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    <td><strong><?php echo e(strtoupper($permohonan->nama_pewaris ?? '-')); ?></strong></td>
                </tr>
                 <tr>
                    <td class="label">NIK</td>
                    <td>:</td>
                    <td><?php echo e($permohonan->nik_pewaris ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Tempat/Tgl Lahir</td>
                    <td>:</td>
                    <td><?php echo e($permohonan->tempat_lahir_pewaris ?? '-'); ?>, <?php echo e($permohonan->tanggal_lahir_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_lahir_pewaris)->isoFormat('D MMMM YYYY') : '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Meninggal</td>
                    <td>:</td>
                    <td><?php echo e($permohonan->tanggal_meninggal_pewaris ? \Carbon\Carbon::parse($permohonan->tanggal_meninggal_pewaris)->isoFormat('D MMMM YYYY') : '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Alamat Terakhir</td>
                    <td>:</td>
                    <td><?php echo e($permohonan->alamat_pewaris ?? '-'); ?></td>
                </tr>
            </table>

            <p class="paragraf">Demikian Surat Keterangan Ahli Waris ini kami buat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="tanda-tangan clearfix">
            <div class="ttd-kanan">
                Kumantan, <?php echo e($permohonan->tanggal_selesai_proses ? $permohonan->tanggal_selesai_proses->isoFormat('D MMMM YYYY') : \Carbon\Carbon::now()->isoFormat('D MMMM YYYY')); ?><br>
                Kepala Desa Kumantan,
                <div class="nama-pejabat"><u>FIRDAUS, S.Pd</u></div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/documents/sk_ahli_waris.blade.php ENDPATH**/ ?>