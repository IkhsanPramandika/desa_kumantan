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
        Schema::create('masyarakat', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->unique()->comment('Nomor Induk Kependudukan, untuk login');
            $table->string('nama_lengkap');
            $table->string('password')->comment('Password yang sudah di-hash');
            $table->string('nomor_hp', 20)->unique()->nullable()->comment('Nomor HP aktif, bisa untuk OTP atau notifikasi');
            $table->string('email')->unique()->nullable()->comment('Email aktif, bisa untuk notifikasi atau reset password');
            
            // Data pribadi tambahan (opsional saat registrasi awal, bisa dilengkapi nanti)
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['LAKI-LAKI', 'PEREMPUAN'])->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('dusun_atau_lingkungan', 100)->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('status_perkawinan', 50)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            
            $table->string('foto_ktp')->nullable()->comment('Path ke file foto KTP untuk verifikasi');
            $table->enum('status_akun', ['pending_verification', 'active', 'inactive', 'rejected'])
                  ->default('pending_verification')
                  ->comment('Status akun masyarakat');
            $table->text('catatan_verifikasi')->nullable()->comment('Catatan dari petugas terkait verifikasi akun');
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masyarakat');
    }
};
