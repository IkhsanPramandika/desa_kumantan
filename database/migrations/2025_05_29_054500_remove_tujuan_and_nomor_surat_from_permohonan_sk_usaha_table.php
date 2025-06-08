<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('permohonan_sk_usaha', function (Blueprint $table) {
        $table->dropColumn(['tujuan', 'nomor_surat']);
    });
}

public function down()
{
    Schema::table('permohonan_sk_usaha', function (Blueprint $table) {
        $table->string('tujuan')->nullable()->after('alamat_usaha');
        $table->string('nomor_surat')->nullable()->after('status');
    });
}
};
