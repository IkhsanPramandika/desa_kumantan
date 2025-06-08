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
            Schema::table('permohonan_sk_perkawinan', function (Blueprint $table) {
                // Tambahkan kolom nomor_surat (string, nullable)
                // Pastikan kolom ini belum ada sebelum menjalankannya
                if (!Schema::hasColumn('permohonan_sk_perkawinan', 'nomor_surat')) {
                    $table->string('nomor_surat')->nullable()->after('status');
                }
                // Tambahkan kolom nomor_urut (integer, nullable)
                if (!Schema::hasColumn('permohonan_sk_perkawinan', 'nomor_urut')) {
                    $table->integer('nomor_urut')->nullable()->after('nomor_surat');
                }
                // Tambahkan kolom file_hasil_akhir (string, nullable)
                if (!Schema::hasColumn('permohonan_sk_perkawinan', 'file_hasil_akhir')) {
                     $table->string('file_hasil_akhir')->nullable()->after('nomor_urut');
                }
                // Ubah kolom tanggal_selesai_proses menjadi nullable jika belum
                // Pastikan kolom ini sudah ada dan ingin diubah sifatnya
                if (Schema::hasColumn('permohonan_sk_perkawinan', 'tanggal_selesai_proses')) {
                    $table->timestamp('tanggal_selesai_proses')->nullable()->change();
                } else {
                    // Jika kolom tanggal_selesai_proses belum ada, tambahkan
                    $table->timestamp('tanggal_selesai_proses')->nullable();
                }
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('permohonan_sk_perkawinan', function (Blueprint $table) {
                // Hapus kolom saat rollback jika ada
                if (Schema::hasColumn('permohonan_sk_perkawinan', 'nomor_surat')) {
                    $table->dropColumn('nomor_surat');
                }
                if (Schema::hasColumn('permohonan_sk_perkawinan', 'nomor_urut')) {
                    $table->dropColumn('nomor_urut');
                }
                if (Schema::hasColumn('permohonan_sk_perkawinan', 'file_hasil_akhir')) {
                    $table->dropColumn('file_hasil_akhir');
                }
                // Kembalikan kolom tanggal_selesai_proses ke kondisi semula jika diperlukan
                // Anda perlu tahu apakah sebelumnya non-nullable atau tidak
                // $table->timestamp('tanggal_selesai_proses')->nullable(false)->change(); // Contoh jika ingin mengembalikan ke non-nullable
            });
        }
    };
    