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
        Schema::create('permohonan_sk_perkawinan', function (Blueprint $table) {
            $table->id();
            $table->string('file_kk')->nullable();
            $table->string('file_ktp_mempelai')->nullable();
            $table->string('surat_nikah_orang_tua')->nullable();
            $table->string('kartu_imunisasi_catin')->nullable();
            $table->string('sertifikat_elsimil')->nullable();
            $table->string('akta_penceraian')->nullable();
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
        Schema::dropIfExists('permohonan_sk_perkawinan');
    }
};