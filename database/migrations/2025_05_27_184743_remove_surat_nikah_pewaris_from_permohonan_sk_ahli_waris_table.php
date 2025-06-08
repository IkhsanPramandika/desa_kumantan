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
                if (Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_nikah_pewaris')) {
                    $table->dropColumn('surat_nikah_pewaris');
                }
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('permohonan_sk_ahli_waris', function (Blueprint $table) {
                // Tambahkan kembali jika perlu rollback
                if (!Schema::hasColumn('permohonan_sk_ahli_waris', 'surat_nikah_pewaris')) {
                    $table->string('surat_nikah_pewaris')->nullable();
                }
            });
        }
    };
    