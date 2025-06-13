<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul'); // Cth: "Permohonan SK Usaha Baru"
            $table->text('pesan'); // Cth: "Budi Santoso telah mengajukan permohonan."
            $table->string('tipe_ikon'); // Cth: 'fas fa-briefcase' dari config Anda
            $table->string('warna_ikon'); // Cth: 'bg-calm-7' dari config Anda
            $table->string('url'); // URL untuk melihat detail permohonan
            $table->timestamp('dibaca_pada')->nullable(); // Kunci utama: null berarti belum dibaca
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};