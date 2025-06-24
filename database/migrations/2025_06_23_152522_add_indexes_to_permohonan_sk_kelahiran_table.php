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
        Schema::table('permohonan_sk_kelahiran', function (Blueprint $table) {
            // Menambahkan indeks pada kolom yang sering kita cari dan urutkan
            $table->index('masyarakat_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_kelahiran', function (Blueprint $table) {
            //
        });
    }
};
