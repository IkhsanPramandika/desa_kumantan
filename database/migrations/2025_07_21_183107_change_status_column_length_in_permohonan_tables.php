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
            'permohonan_lainnyas',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Ubah panjang kolom status menjadi 50 karakter
                $table->string('status', 50)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ini adalah kebalikan dari fungsi up, jika Anda perlu melakukan rollback
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
            'permohonan_lainnyas',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Kembalikan ke panjang sebelumnya (asumsi 20)
                $table->string('status', 20)->change();
            });
        }
    }
};