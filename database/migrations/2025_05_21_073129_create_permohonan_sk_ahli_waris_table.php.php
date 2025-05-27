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
        Schema::create('permohonan_sk_ahli_waris', function (Blueprint $table) {
            $table->id(); 
            $table->string('file_kk')->nullable();               // Path Kartu Keluarga
            $table->string('file_ktp')->nullable();              // Path Kartu Tanda Penduduk Pemohon
            $table->string('file_ktp_ahli_waris')->nullable();   // Path KTP Ahli Waris (jika lebih dari satu, perlu penyesuaian di model)
            $table->string('surat_pengantar_rt_rw')->nullable(); // Path Surat Pengantar RT/RW
            $table->string('surat_kematian_almarhum')->nullable(); // Path Surat Kematian Almarhum/Almarhumah
            $table->text('catatan')->nullable(); // Catatan atau komentar, bisa panjang
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending'); // Status permohonan
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_sk_ahli_waris');
    }
};