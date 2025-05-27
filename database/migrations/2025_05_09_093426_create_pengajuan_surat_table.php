<?php

use App\Models\PengajuanSurat;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('pengajuan_surat', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // $table->enum('jenis_layanan', array_keys(\App\Models\PengajuanSurat::LAYANAN));
        $table->json('data_tambahan');
        $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};
