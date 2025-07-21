// database/migrations/xxxx_xx_xx_xxxxxx_create_permohonan_lainnyas_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_lainnyas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->constrained('users')->onDelete('cascade');
            
            // Data dari pemohon (Flutter)
            $table->string('judul_permohonan'); // Misal: "Surat Keterangan Kehilangan"
            $table->text('keperluan'); // Misal: "Untuk mengurus SIM baru"
            $table->text('rincian_pemohon'); // Deskripsi detail dari pemohon
            $table->string('lampiran')->nullable(); // Path file lampiran jika ada

            // Data yang diisi oleh petugas
            $table->string('nomor_surat')->nullable();
            $table->string('judul_surat_final')->nullable(); // Judul resmi di kop surat
            $table->text('konten_final_html')->nullable(); // Isi surat dari Rich Text Editor
            $table->string('file_hasil_akhir')->nullable(); // Path file PDF yang sudah jadi
            $table->text('catatan_penolakan')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            
            $table->string('status')->default('pending'); // pending, diterima, ditolak, selesai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_lainnyas');
    }
};