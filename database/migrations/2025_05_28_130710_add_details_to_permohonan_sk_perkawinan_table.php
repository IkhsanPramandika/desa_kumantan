<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk mengubah ENUM

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permohonan_sk_perkawinan', function (Blueprint $table) {
            // Kolom untuk data mempelai yang diinput petugas
            $table->string('nama_pria')->nullable();
            $table->string('nik_pria', 16)->nullable();
            $table->string('tempat_lahir_pria')->nullable();
            $table->date('tanggal_lahir_pria')->nullable();
            $table->text('alamat_pria')->nullable();

            $table->string('nama_wanita')->nullable();
            $table->string('nik_wanita', 16)->nullable();
            $table->string('tempat_lahir_wanita')->nullable();
            $table->date('tanggal_lahir_wanita')->nullable();
            $table->text('alamat_wanita')->nullable();

            $table->date('tanggal_akad')->nullable();
            $table->string('tempat_akad')->nullable();
            $table->string('saksi_1')->nullable();
            $table->string('saksi_2')->nullable();

            // Kolom untuk penomoran surat dan status proses
            $table->integer('nomor_urut')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->string('file_hasil_akhir')->nullable(); // Path ke PDF yang dihasilkan

            // Ubah kolom 'status' untuk menambahkan nilai 'diproses' dan 'selesai'
            // Pastikan urutan nilai yang ada ('pending', 'diterima', 'ditolak') tetap sama
            DB::statement("ALTER TABLE `permohonan_sk_perkawinan` CHANGE COLUMN `status` `status` ENUM('pending','diterima','ditolak','diproses','selesai') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_perkawinan', function (Blueprint $table) {
            // Hapus kolom-kolom yang ditambahkan
            $table->dropColumn([
                'nama_pria', 'nik_pria', 'tempat_lahir_pria', 'tanggal_lahir_pria', 'alamat_pria',
                'nama_wanita', 'nik_wanita', 'tempat_lahir_wanita', 'tanggal_lahir_wanita', 'alamat_wanita',
                'tanggal_akad', 'tempat_akad', 'saksi_1', 'saksi_2',
                'nomor_urut', 'tanggal_selesai_proses', 'file_hasil_akhir'
            ]);

            // Kembalikan kolom 'status' ke ENUM sebelumnya
            DB::statement("ALTER TABLE `permohonan_sk_perkawinan` CHANGE COLUMN `status` `status` ENUM('pending','diterima','ditolak') NOT NULL DEFAULT 'pending'");
        });
    }
};