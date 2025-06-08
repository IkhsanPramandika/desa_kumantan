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
        Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
            // Periksa apakah kolom-kolom lama ada sebelum mencoba menghapusnya
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'nama_ahli_waris')) {
                $table->dropColumn('nama_ahli_waris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'nik_ahli_waris')) {
                $table->dropColumn('nik_ahli_waris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'hubungan_ahli_waris')) {
                $table->dropColumn('hubungan_ahli_waris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'alamat_ahli_waris')) {
                $table->dropColumn('alamat_ahli_waris');
            }

            // Tambahkan kolom baru untuk daftar ahli waris (JSON) jika belum ada
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'daftar_ahli_waris')) {
                $table->json('daftar_ahli_waris')->nullable()->after('alamat_pewaris');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
            // Hapus kolom daftar_ahli_waris jika ada
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'daftar_ahli_waris')) {
                $table->dropColumn('daftar_ahli_waris');
            }

            // Kembalikan kolom ahli waris singular jika belum ada (untuk rollback)
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'nama_ahli_waris')) {
                $table->string('nama_ahli_waris')->nullable();
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'nik_ahli_waris')) {
                $table->string('nik_ahli_waris', 16)->nullable();
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'hubungan_ahli_waris')) {
                $table->string('hubungan_ahli_waris')->nullable();
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'alamat_ahli_waris')) {
                $table->string('alamat_ahli_waris', 500)->nullable();
            }
        });
    }
};
