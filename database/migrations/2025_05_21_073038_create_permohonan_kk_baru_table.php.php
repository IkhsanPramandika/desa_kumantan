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
        Schema::create('permohonan_kk_baru', function (Blueprint $table) {
            $table->id();
            $table->string('file_kk')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('surat_pengantar_rt_rw')->nullable();
            $table->string('buku_nikah_akta_cerai')->nullable();
            $table->string('surat_pindah_datang')->nullable();
            $table->string('ijazah_terakhir')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_kk_baru');
    }
};