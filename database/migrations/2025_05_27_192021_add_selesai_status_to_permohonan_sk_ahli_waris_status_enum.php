<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan baris ini

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah kolom 'status' untuk menambahkan nilai 'selesai' ke ENUM
        // Pastikan urutan nilai yang ada ('pending', 'diterima', 'ditolak') tetap sama
        // dan tambahkan 'selesai' di posisi yang Anda inginkan (misalnya, di akhir)
        DB::statement("ALTER TABLE `permohonan_sk_ahli_waris` CHANGE COLUMN `status` `status` ENUM('pending','diterima','ditolak','selesai') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika Anda ingin mengembalikan migrasi, Anda bisa menghapus 'selesai' dari ENUM
        DB::statement("ALTER TABLE `permohonan_sk_ahli_waris` CHANGE COLUMN `status` `status` ENUM('pending','diterima','ditolak') NOT NULL DEFAULT 'pending'");
    }
};