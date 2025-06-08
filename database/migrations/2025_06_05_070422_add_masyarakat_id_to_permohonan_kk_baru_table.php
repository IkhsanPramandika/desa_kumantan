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
        Schema::table('permohonan_kk_hilang', function (Blueprint $table) {
            // 1. Tambahkan kolom masyarakat_id
            if (!Schema::hasColumn('permohonan_kk_hilang', 'masyarakat_id')) {
                $table->foreignId('masyarakat_id')
                      ->nullable()
                      ->after('id') 
                      ->comment('ID pengguna masyarakat yang mengajukan permohonan')
                      ->constrained('masyarakat') // Asumsi nama tabel masyarakat adalah 'masyarakat'
                      ->onDelete('set null'); // Atau cascade, tergantung kebijakan
            }

            // 2. Tambahkan kolom file_kk_lama (setelah surat_keterangan_hilang_kepolisian atau sesuaikan)
            if (!Schema::hasColumn('permohonan_kk_hilang', 'file_kk_lama')) {
                $table->string('file_kk_lama')->nullable()->after('surat_keterangan_hilang_kepolisian');
            }

            // 3. Tambahkan kolom file_ktp_pemohon (setelah file_kk_lama atau sesuaikan)
            if (!Schema::hasColumn('permohonan_kk_hilang', 'file_ktp_pemohon')) {
                $table->string('file_ktp_pemohon')->nullable()->after('file_kk_lama');
            }

            // 4. Opsional: Ganti nama 'catatan' menjadi 'catatan_pemohon'
            if (Schema::hasColumn('permohonan_kk_hilang', 'catatan') && !Schema::hasColumn('permohonan_kk_hilang', 'catatan_pemohon')) {
                $table->renameColumn('catatan', 'catatan_pemohon');
            } elseif (!Schema::hasColumn('permohonan_kk_hilang', 'catatan_pemohon') && !Schema::hasColumn('permohonan_kk_hilang', 'catatan')) {
                $table->text('catatan_pemohon')->nullable()->after('file_ktp_pemohon'); // Sesuaikan posisi
            }
            
            // 5. Tambahkan kolom standar permohonan lainnya
            $lastKnownColumn = Schema::hasColumn('permohonan_kk_hilang', 'catatan_pemohon') ? 'catatan_pemohon' : (Schema::hasColumn('permohonan_kk_hilang', 'catatan') ? 'catatan' : 'file_ktp_pemohon');

            if (!Schema::hasColumn('permohonan_kk_hilang', 'nomor_urut')) {
                $table->integer('nomor_urut')->nullable()->after($lastKnownColumn);
            }
            $lastKnownColumn = 'nomor_urut';

            if (!Schema::hasColumn('permohonan_kk_hilang', 'nomor_surat')) {
                $table->string('nomor_surat')->nullable()->after($lastKnownColumn);
            }
            $lastKnownColumn = 'nomor_surat';
            
            if (!Schema::hasColumn('permohonan_kk_hilang', 'tanggal_selesai_proses')) {
                $table->timestamp('tanggal_selesai_proses')->nullable()->after($lastKnownColumn);
            }
            $lastKnownColumn = 'tanggal_selesai_proses';

            if (!Schema::hasColumn('permohonan_kk_hilang', 'file_hasil_akhir')) {
                $table->string('file_hasil_akhir')->nullable()->after($lastKnownColumn);
            }
            $lastKnownColumn = 'file_hasil_akhir';

            if (!Schema::hasColumn('permohonan_kk_hilang', 'catatan_penolakan')) {
                $table->text('catatan_penolakan')->nullable()->after($lastKnownColumn);
            }
            
            // 6. Mengubah enum 'status' untuk menambahkan 'selesai'
            // Pastikan doctrine/dbal terinstall: composer require doctrine/dbal
            if (Schema::hasColumn('permohonan_kk_hilang', 'status')) {
                $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_kk_hilang', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('permohonan_kk_hilang', 'masyarakat_id')) {
                // Hapus foreign key constraint dulu jika ada
                // Perlu nama constraint, atau cara yang lebih aman:
                // $table->dropForeign(['masyarakat_id']);
                // Untuk sementara, kita hanya drop kolom. Jika ada constraint, ini akan error.
                // Cara yang lebih baik adalah jika constraint dibuat dengan ->constrained(), Laravel tahu cara menghapusnya.
                // Jika Anda menambahkan foreign key dengan $table->foreign(...), Anda perlu $table->dropForeign('nama_foreign_key_constraint');
                $columnsToDrop[] = 'masyarakat_id';
            }
            if (Schema::hasColumn('permohonan_kk_hilang', 'file_kk_lama')) $columnsToDrop[] = 'file_kk_lama';
            if (Schema::hasColumn('permohonan_kk_hilang', 'file_ktp_pemohon')) $columnsToDrop[] = 'file_ktp_pemohon';
            if (Schema::hasColumn('permohonan_kk_hilang', 'nomor_urut')) $columnsToDrop[] = 'nomor_urut';
            if (Schema::hasColumn('permohonan_kk_hilang', 'nomor_surat')) $columnsToDrop[] = 'nomor_surat';
            if (Schema::hasColumn('permohonan_kk_hilang', 'tanggal_selesai_proses')) $columnsToDrop[] = 'tanggal_selesai_proses';
            if (Schema::hasColumn('permohonan_kk_hilang', 'file_hasil_akhir')) $columnsToDrop[] = 'file_hasil_akhir';
            if (Schema::hasColumn('permohonan_kk_hilang', 'catatan_penolakan')) $columnsToDrop[] = 'catatan_penolakan';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            if (Schema::hasColumn('permohonan_kk_hilang', 'catatan_pemohon') && !Schema::hasColumn('permohonan_kk_hilang', 'catatan')) {
                 $table->renameColumn('catatan_pemohon', 'catatan');
            }
            if (Schema::hasColumn('permohonan_kk_hilang', 'status')) {
                $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();
            }
        });
    }
};
