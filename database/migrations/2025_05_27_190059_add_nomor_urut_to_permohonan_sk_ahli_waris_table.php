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
        Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
            // Menambahkan kolom 'nomor_urut' sebagai integer dan bisa null
            // Kolom ini akan diisi saat permohonan selesai diproses
            $table->integer('nomor_urut')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
            // Menghapus kolom 'nomor_urut' jika migrasi di-rollback
            $table->dropColumn('nomor_urut');
        });
    }
};

