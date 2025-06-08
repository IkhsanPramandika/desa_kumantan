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
                // Hapus kolom nama_pemohon jika ada
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'nama_pemohon')) {
                    $table->dropColumn('nama_pemohon');
                }
                // Hapus kolom alamat_pemohon jika ada
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'alamat_pemohon')) {
                    $table->dropColumn('alamat_pemohon');
                }
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
                // Tambahkan kembali kolom nama_pemohon jika tidak ada (untuk rollback)
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'nama_pemohon')) {
                    $table->string('nama_pemohon')->nullable(); // Sesuaikan tipe data dan nullability jika berbeda sebelumnya
                }
                // Tambahkan kembali kolom alamat_pemohon jika tidak ada (untuk rollback)
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'alamat_pemohon')) {
                    $table->string('alamat_pemohon', 500)->nullable(); // Sesuaikan tipe data dan nullability jika berbeda sebelumnya
                }
            });
        }
    };
    