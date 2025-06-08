<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permohonan_sk_usaha', function (Blueprint $table) {
            $table->string('nama_pemohon')->after('file_ktp')->nullable();
            $table->string('nik_pemohon')->after('nama_pemohon')->nullable();
            $table->string('jenis_kelamin')->after('nik_pemohon')->nullable();
            $table->string('tempat_lahir')->after('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir')->after('tempat_lahir')->nullable();
            $table->string('warganegara_agama')->after('tanggal_lahir')->nullable();
            $table->string('pekerjaan')->after('warganegara_agama')->nullable();
            $table->text('alamat_pemohon')->after('pekerjaan')->nullable();
            $table->string('tujuan')->after('alamat_usaha')->nullable();
            $table->string('nomor_surat')->after('catatan')->nullable();
            $table->string('file_hasil_akhir')->after('nomor_surat')->nullable();
            $table->timestamp('tanggal_selesai_proses')->after('file_hasil_akhir')->nullable();

            // Untuk ubah enum status — perlu ekstensi DB atau raw query (lihat catatan di bawah)
        });

        // Jika ingin ubah enum status, lakukan dengan raw query (karena Laravel tidak dukung enum change secara langsung)
        DB::statement("ALTER TABLE permohonan_sk_usaha MODIFY status ENUM('pending', 'diterima', 'ditolak', 'selesai') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_usaha', function (Blueprint $table) {
            $table->dropColumn([
                'nama_pemohon',
                'nik_pemohon',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'warganegara_agama',
                'pekerjaan',
                'alamat_pemohon',
                'tujuan',
                'nomor_surat',
                'file_hasil_akhir',
                'tanggal_selesai_proses'
            ]);
        });

        // Kembalikan enum status ke sebelumnya
        DB::statement("ALTER TABLE permohonan_sk_usaha MODIFY status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending'");
    }
};
