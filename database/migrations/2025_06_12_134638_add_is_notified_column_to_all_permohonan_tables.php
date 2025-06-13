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
        // Daftar semua tabel permohonan Anda
        $tables = [
            'permohonan_kk_baru',
            'permohonan_kk_hilang',
            'permohonan_kk_perubahan_data',
            'permohonan_sk_ahli_waris',
            'permohonan_sk_domisili',
            'permohonan_sk_kelahiran',
            'permohonan_sk_perkawinan',
            'permohonan_sk_tidak_mampu',
            'permohonan_sk_usaha',
        ];

        foreach ($tables as $table) {
            // Cek jika tabelnya ada dan belum punya kolomnya
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'telah_dinotifikasi')) {
                Schema::table($table, function (Blueprint $table) {
                    // Tambahkan kolom boolean dengan nilai default false
                    $table->boolean('telah_dinotifikasi')->default(false)->after('status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'permohonan_kk_baru', 'permohonan_kk_hilang', 'permohonan_kk_perubahan_data',
            'permohonan_sk_ahli_waris', 'permohonan_sk_domisili', 'permohonan_sk_kelahiran',
            'permohonan_sk_perkawinan', 'permohonan_sk_tidak_mampu', 'permohonan_sk_usaha',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'telah_dinotifikasi')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('telah_dinotifikasi');
                });
            }
        }
    }
};
