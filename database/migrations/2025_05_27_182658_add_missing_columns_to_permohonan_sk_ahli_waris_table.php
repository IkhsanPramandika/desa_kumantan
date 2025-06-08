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
            // 1. Mengganti nama kolom yang sudah ada agar sesuai dengan model
            // Pastikan kolom-kolom ini ada sebelum diganti namanya
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_ktp')) {
                $table->renameColumn('file_ktp', 'file_ktp_pemohon');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_kk')) {
                $table->renameColumn('file_kk', 'file_kk_pemohon');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_kematian_almarhum')) {
                $table->renameColumn('surat_kematian_almarhum', 'surat_keterangan_kematian');
            }

            // 2. Menambahkan kolom-kolom yang belum ada
            // Pastikan kolom-kolom ini belum ada sebelum ditambahkan
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'nama_pewaris')) {
                $table->string('nama_pewaris')->nullable()->after('status');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'nik_pewaris')) {
                $table->string('nik_pewaris', 16)->nullable()->after('nama_pewaris');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'tempat_lahir_pewaris')) {
                $table->string('tempat_lahir_pewaris')->nullable()->after('nik_pewaris');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'tanggal_lahir_pewaris')) {
                $table->date('tanggal_lahir_pewaris')->nullable()->after('tempat_lahir_pewaris');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'tanggal_meninggal_pewaris')) {
                $table->date('tanggal_meninggal_pewaris')->nullable()->after('tanggal_lahir_pewaris');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'alamat_pewaris')) {
                $table->string('alamat_pewaris', 500)->nullable()->after('tanggal_meninggal_pewaris');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_nikah_pewaris')) {
                $table->string('surat_nikah_pewaris')->nullable()->after('surat_keterangan_kematian');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'catatan_penolakan')) {
                $table->string('catatan_penolakan', 500)->nullable()->after('status');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'nomor_surat_ahli_waris')) {
                $table->string('nomor_surat_ahli_waris')->nullable()->after('daftar_ahli_waris'); // Ini akan ditambahkan setelah daftar_ahli_waris
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'file_hasil_akhir')) {
                $table->string('file_hasil_akhir')->nullable()->after('nomor_surat_ahli_waris');
            }
            if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'tanggal_selesai_proses')) {
                $table->dateTime('tanggal_selesai_proses')->nullable()->after('file_hasil_akhir');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
            // 1. Mengembalikan nama kolom (jika ada)
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_ktp_pemohon')) {
                $table->renameColumn('file_ktp_pemohon', 'file_ktp');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_kk_pemohon')) {
                $table->renameColumn('file_kk_pemohon', 'file_kk');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_keterangan_kematian')) {
                $table->renameColumn('surat_keterangan_kematian', 'surat_kematian_almarhum');
            }

            // 2. Menghapus kolom-kolom yang ditambahkan (jika ada)
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'nama_pewaris')) {
                $table->dropColumn('nama_pewaris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'nik_pewaris')) {
                $table->dropColumn('nik_pewaris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'tempat_lahir_pewaris')) {
                $table->dropColumn('tempat_lahir_pewaris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'tanggal_lahir_pewaris')) {
                $table->dropColumn('tanggal_lahir_pewaris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'tanggal_meninggal_pewaris')) {
                $table->dropColumn('tanggal_meninggal_pewaris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'alamat_pewaris')) {
                $table->dropColumn('alamat_pewaris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_nikah_pewaris')) {
                $table->dropColumn('surat_nikah_pewaris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'catatan_penolakan')) {
                $table->dropColumn('catatan_penolakan');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'nomor_surat_ahli_waris')) {
                $table->dropColumn('nomor_surat_ahli_waris');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_hasil_akhir')) {
                $table->dropColumn('file_hasil_akhir');
            }
            if (Schema::hasColumn('permohonan_sk_ahli_waris', 'tanggal_selesai_proses')) {
                $table->dropColumn('tanggal_selesai_proses');
            }
        });
    }
};
