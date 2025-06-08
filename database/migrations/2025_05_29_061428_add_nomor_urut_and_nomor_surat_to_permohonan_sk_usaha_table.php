<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('permohonan_sk_usaha', function (Blueprint $table) {
            $table->integer('nomor_urut')->nullable()->after('status'); // Atau sesuaikan posisi kolom
            $table->string('nomor_surat')->nullable()->after('nomor_urut');
        });
    }

    public function down()
    {
        Schema::table('permohonan_sk_usaha', function (Blueprint $table) {
            $table->dropColumn(['nomor_urut', 'nomor_surat']);
        });
    }
};