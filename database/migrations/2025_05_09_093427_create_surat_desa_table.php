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
    Schema::create('template_surat', function (Blueprint $table) {
        $table->id();
        $table->string('jenis_layanan')->unique();
        $table->text('template_html'); // Template surat dalam HTML
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_desa');
    }
};
