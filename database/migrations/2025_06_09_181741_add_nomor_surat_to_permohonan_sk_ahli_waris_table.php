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
            // Tambahkan kolom setelah kolom 'status' (atau sesuaikan posisinya)
            $table->string('nomor_surat')->nullable()->after('status');
            $table->integer('nomor_urut')->nullable()->after('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
            $table->dropColumn('nomor_surat');
            $table->dropColumn('nomor_urut');
        });
    }
};