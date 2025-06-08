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
                // Tambahkan kolom jika belum ada
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'file_ktp_pemohon')) {
                    $table->string('file_ktp_pemohon')->nullable();
                }
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'file_kk_pemohon')) {
                    $table->string('file_kk_pemohon')->nullable();
                }
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'file_ktp_ahli_waris')) {
                    $table->string('file_ktp_ahli_waris')->nullable();
                }
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'file_kk_ahli_waris')) {
                    $table->string('file_kk_ahli_waris')->nullable();
                }
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_keterangan_kematian')) {
                    $table->string('surat_keterangan_kematian')->nullable();
                }
                // Tambahkan surat_nikah_pewaris untuk sementara, nanti akan dihapus di migrasi berikutnya
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_nikah_pewaris')) {
                    $table->string('surat_nikah_pewaris')->nullable();
                }
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_pengantar_rt_rw')) {
                    $table->string('surat_pengantar_rt_rw')->nullable();
                }
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
                // Hapus kolom saat rollback
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_ktp_pemohon')) {
                    $table->dropColumn('file_ktp_pemohon');
                }
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_kk_pemohon')) {
                    $table->dropColumn('file_kk_pemohon');
                }
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_ktp_ahli_waris')) {
                    $table->dropColumn('file_ktp_ahli_waris');
                }
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'file_kk_ahli_waris')) {
                    $table->dropColumn('file_kk_ahli_waris');
                }
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_keterangan_kematian')) {
                    $table->dropColumn('surat_keterangan_kematian');
                }
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_nikah_pewaris')) {
                    $table->dropColumn('surat_nikah_pewaris');
                }
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_pengantar_rt_rw')) {
                    $table->dropColumn('surat_pengantar_rt_rw');
                }
            });
        }
    };
    