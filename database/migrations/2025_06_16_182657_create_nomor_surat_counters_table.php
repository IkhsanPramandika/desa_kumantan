<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomor_surat_counters', function (Blueprint $table) {
            $table->year('tahun')->primary(); // Tahun sebagai primary key
            $table->unsignedInteger('nomor_terakhir');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomor_surat_counters');
    }
};
