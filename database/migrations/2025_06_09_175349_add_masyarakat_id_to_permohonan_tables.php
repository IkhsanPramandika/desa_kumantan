<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // [PERBAIKAN] Menambahkan pengecekan apakah kolom sudah ada sebelum menambahkannya.
        // Ganti 'permohonan_sk_domisili' dengan nama tabel Anda jika berbeda.
        $tableName = 'permohonan_sk_domisili';

        if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'masyarakat_id')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('masyarakat_id')
                      ->nullable()
                      ->after('id') // Meletakkan kolom setelah kolom 'id'
                      ->constrained('masyarakat')
                      ->onDelete('cascade');
            });
        }
        
        // [CONTOH] Jika ada tabel lain yang perlu diubah di file migration ini,
        // terapkan pola yang sama.
        /*
        $tableName2 = 'permohonan_kk_baru';
        if (Schema::hasTable($tableName2) && !Schema::hasColumn($tableName2, 'masyarakat_id')) {
            Schema::table($tableName2, function (Blueprint $table) {
                $table->foreignId('masyarakat_id')->nullable()->after('id')->constrained('masyarakat')->onDelete('cascade');
            });
        }
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // [PERBAIKAN] Mengisi method 'down' untuk bisa melakukan rollback dengan aman.
        $tableName = 'permohonan_sk_domisili';

        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'masyarakat_id')) {
            Schema::table($tableName, function (Blueprint $table) {
                // Hapus foreign key constraint dulu sebelum menghapus kolomnya
                $table->dropForeign(['masyarakat_id']);
                $table->dropColumn('masyarakat_id');
            });
        }

        // [CONTOH] Untuk tabel lain:
        /*
        $tableName2 = 'permohonan_kk_baru';
        if (Schema::hasTable($tableName2) && Schema::hasColumn($tableName2, 'masyarakat_id')) {
            Schema::table($tableName2, function (Blueprint $table) {
                $table->dropForeign(['masyarakat_id']);
                $table->dropColumn('masyarakat_id');
            });
        }
        */
    }
};