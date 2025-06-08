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
        Schema::create('masyarakat_password_reset_tokens', function (Blueprint $table) {
            // Menggunakan email sebagai primary key untuk token reset.
            // Pastikan kolom 'email' di tabel 'masyarakat' juga unik (jika diisi) dan diindeks dengan baik.
            $table->string('email')->primary(); 
            
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masyarakat_password_reset_tokens');
    }
};
