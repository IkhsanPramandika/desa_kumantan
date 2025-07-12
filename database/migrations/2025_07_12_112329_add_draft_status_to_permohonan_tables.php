<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nama-nama tabel permohonan yang akan diubah.
     */
    protected $tables = [
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

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                // Menggunakan DB::statement untuk mengubah kolom ENUM yang sudah ada
                DB::statement("ALTER TABLE {$tableName} MODIFY COLUMN status ENUM('draft', 'pending', 'diterima', 'diproses', 'ditolak', 'selesai') NOT NULL DEFAULT 'draft'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                // Mengembalikan ke state semula jika migrasi di-rollback
                 DB::statement("ALTER TABLE {$tableName} MODIFY COLUMN status ENUM('pending', 'diterima', 'diproses', 'ditolak', 'selesai') NOT NULL DEFAULT 'pending'");
            }
        }
    }
};
