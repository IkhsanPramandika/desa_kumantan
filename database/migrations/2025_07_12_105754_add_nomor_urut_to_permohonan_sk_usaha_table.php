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
            // Menambahkan kolom 'nomor_urut' setelah kolom 'catatan_pemohon'
            $table->integer('nomor_urut')->nullable()->after('catatan_pemohon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_usaha', function (Blueprint $table) {
            $table->dropColumn('nomor_urut');
        });
    }
};
