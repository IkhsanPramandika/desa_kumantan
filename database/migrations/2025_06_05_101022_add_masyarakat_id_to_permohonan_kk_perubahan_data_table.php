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
        Schema::table('permohonan_kk_perubahan_data', function (Blueprint $table) {
            // Tambahkan kolom masyarakat_id setelah kolom id, atau sesuaikan posisinya
            // Pastikan tabel 'masyarakat' sudah ada sebelum menjalankan migrasi ini
            if (!Schema::hasColumn('permohonan_kk_perubahan_data', 'masyarakat_id')) {
                $table->foreignId('masyarakat_id')
                      ->nullable() // Buat nullable jika ada data lama yang tidak memiliki masyarakat_id
                      ->after('id') // Atau sesuaikan posisi kolom yang diinginkan
                      ->comment('ID pengguna masyarakat yang mengajukan permohonan')
                      ->constrained('masyarakat') // Membuat foreign key ke tabel 'masyarakat'
                      ->onDelete('set null'); // Aksi jika record masyarakat dihapus (bisa juga 'cascade')
            }

            // Opsional: Jika Anda juga ingin menambahkan kolom lain seperti nomor_urut, nomor_surat, dll.
            // yang mungkin belum ada dari migrasi lama, bisa ditambahkan di sini juga.
            // Contoh:
            // if (!Schema::hasColumn('permohonan_kk_perubahan_data', 'nomor_urut')) {
            //     $table->integer('nomor_urut')->nullable()->after('status'); // Sesuaikan posisi
            // }
            // if (!Schema::hasColumn('permohonan_kk_perubahan_data', 'nomor_surat')) {
            //     $table->string('nomor_surat')->nullable()->after('nomor_urut');
            // }

            // Pastikan juga enum 'status' sudah mencakup 'selesai' jika belum
            // if (Schema::hasColumn('permohonan_kk_perubahan_data', 'status')) {
            //    $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending')->change();
            // }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_kk_perubahan_data', function (Blueprint $table) {
            if (Schema::hasColumn('permohonan_kk_perubahan_data', 'masyarakat_id')) {
                // Untuk menghapus foreign key, Anda mungkin perlu mengetahui nama constraintnya
                // Jika menggunakan ->constrained(), Laravel akan mencoba menghapusnya dengan nama default.
                // Jika tidak berhasil, Anda mungkin perlu $table->dropForeign(['masyarakat_id']); atau nama constraint spesifik.
                $table->dropConstrainedForeignId('masyarakat_id'); // Cara mudah jika menggunakan constrained()
                // Atau jika tidak menggunakan constrained() di atas:
                // $table->dropColumn('masyarakat_id');
            }

            // Jika Anda menambahkan kolom lain di 'up()', tambahkan logika dropColumn di sini
            // if (Schema::hasColumn('permohonan_kk_perubahan_data', 'nomor_urut')) {
            //     $table->dropColumn('nomor_urut');
            // }
            // if (Schema::hasColumn('permohonan_kk_perubahan_data', 'nomor_surat')) {
            //     $table->dropColumn('nomor_surat');
            // }

            // Kembalikan enum 'status' jika diubah
            // if (Schema::hasColumn('permohonan_kk_perubahan_data', 'status')) {
            //    $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();
            // }
        });
    }
};
