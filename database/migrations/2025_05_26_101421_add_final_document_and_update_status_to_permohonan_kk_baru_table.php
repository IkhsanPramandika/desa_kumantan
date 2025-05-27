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
        Schema::table('permohonan_kk_baru', function (Blueprint $table) {
            // Menambahkan kolom baru untuk file hasil akhir
            $table->string('file_hasil_akhir')->nullable()->after('status');

            // Menambahkan kolom untuk tanggal selesai proses
            $table->timestamp('tanggal_selesai_proses')->nullable()->after('file_hasil_akhir');

            // Mengubah tipe ENUM untuk kolom 'status'
            // Pastikan 'diproses' dan 'selesai' adalah status yang ingin Anda tambahkan
            $table->enum('status', ['pending', 'diterima', 'diproses', 'ditolak', 'selesai'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_kk_baru', function (Blueprint $table) {
            // Mengembalikan tipe ENUM ke semula saat rollback
            // Pastikan ini sesuai dengan definisi ENUM di migrasi awal Anda
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();

            // Menghapus kolom yang ditambahkan saat rollback
            $table->dropColumn('tanggal_selesai_proses');
            $table->dropColumn('file_hasil_akhir');
        });
    }
};