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
        Schema::table('permohonan_kk_hilang', function (Blueprint $table) {
            // Menambahkan kolom baru
            $table->string('file_hasil_akhir')->nullable()->after('status');
            $table->timestamp('tanggal_selesai_proses')->nullable()->after('file_hasil_akhir');
            $table->text('catatan_penolakan')->nullable()->after('catatan'); // Tambahkan setelah kolom 'catatan'

            // Memperbarui ENUM status
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'diproses', 'selesai'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_kk_hilang', function (Blueprint $table) {
            // Mengembalikan ENUM status ke definisi awal
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();

            // Menghapus kolom yang ditambahkan
            $table->dropColumn('tanggal_selesai_proses');
            $table->dropColumn('file_hasil_akhir');
            $table->dropColumn('catatan_penolakan');
        });
    }
};