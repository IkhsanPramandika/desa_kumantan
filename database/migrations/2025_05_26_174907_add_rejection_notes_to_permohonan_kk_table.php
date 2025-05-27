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
            $table->text('catatan_penolakan')->nullable()->after('catatan'); // Tambahkan setelah kolom 'catatan'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_kk_baru', function (Blueprint $table) {
            $table->dropColumn('catatan_penolakan');
        });
    }
};