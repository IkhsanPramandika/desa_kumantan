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
            $table->text('keperluan_surat')->nullable()->after('alamat_usaha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_usaha', function (Blueprint $table) {
              $table->dropColumn('keperluan_surat');
        });
    }
};
