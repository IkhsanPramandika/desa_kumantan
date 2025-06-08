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
            // 1. Tambahkan kolom masyarakat_id jika belum ada
            if (!Schema::hasColumn('permohonan_kk_hilang', 'masyarakat_id')) {
                $table->foreignId('masyarakat_id')
                      ->nullable()
                      ->after('id') 
                      ->comment('ID pengguna masyarakat yang mengajukan permohonan')
                      ->constrained('masyarakat') // Pastikan tabel 'masyarakat' sudah ada
                      ->onDelete('set null'); // atau cascade, tergantung kebijakan Anda
            }

            // Kolom yang sudah ada di migrasi lama Anda:
            // - surat_pengantar_rt_rw (VARCHAR, nullable)
            // - surat_keterangan_hilang_kepolisian (VARCHAR, nullable)
            // - catatan (TEXT, nullable) -> akan direname menjadi catatan_pemohon
            // - status (ENUM 'pending', 'diterima', 'ditolak') -> akan diubah
            // - timestamps

            // 2. Tambahkan kolom file_kk_lama (setelah surat_keterangan_hilang_kepolisian)
            if (!Schema::hasColumn('permohonan_kk_hilang', 'file_kk_lama')) {
                $table->string('file_kk_lama')->nullable()->after('surat_keterangan_hilang_kepolisian')->comment('File KK lama jika ada');
            }

            // 3. Tambahkan kolom file_ktp_pemohon (setelah file_kk_lama)
            if (!Schema::hasColumn('permohonan_kk_hilang', 'file_ktp_pemohon')) {
                $table->string('file_ktp_pemohon')->nullable()->after('file_kk_lama')->comment('File KTP Pemohon/Pelapor');
            }

            // 4. Ganti nama 'catatan' menjadi 'catatan_pemohon' jika 'catatan' ada dan 'catatan_pemohon' belum ada
            if (Schema::hasColumn('permohonan_kk_hilang', 'catatan') && !Schema::hasColumn('permohonan_kk_hilang', 'catatan_pemohon')) {
                $table->renameColumn('catatan', 'catatan_pemohon');
            } elseif (!Schema::hasColumn('permohonan_kk_hilang', 'catatan_pemohon') && !Schema::hasColumn('permohonan_kk_hilang', 'catatan')) {
                // Jika keduanya tidak ada, tambahkan catatan_pemohon
                $table->text('catatan_pemohon')->nullable()->after('file_ktp_pemohon'); // Sesuaikan posisi jika perlu
            }
            
            // 5. Tambahkan kolom standar permohonan lainnya
            // Tentukan kolom terakhir yang diketahui ada setelah penambahan/rename di atas
            $lastKnownColumnAfterFiles = Schema::hasColumn('permohonan_kk_hilang', 'catatan_pemohon') ? 'catatan_pemohon' : (Schema::hasColumn('permohonan_kk_hilang', 'catatan') ? 'catatan' : 'file_ktp_pemohon');
            // Jika file_ktp_pemohon tidak ada, gunakan 'surat_keterangan_hilang_kepolisian'
            if (!Schema::hasColumn('permohonan_kk_hilang', $lastKnownColumnAfterFiles) && Schema::hasColumn('permohonan_kk_hilang', 'surat_keterangan_hilang_kepolisian')) {
                $lastKnownColumnAfterFiles = 'surat_keterangan_hilang_kepolisian';
            }


            if (!Schema::hasColumn('permohonan_kk_hilang', 'nomor_urut')) {
                $table->integer('nomor_urut')->nullable()->after($lastKnownColumnAfterFiles);
            }
            $lastKnownColumnAfterFiles = 'nomor_urut'; // Update untuk kolom selanjutnya

            if (!Schema::hasColumn('permohonan_kk_hilang', 'nomor_surat')) {
                $table->string('nomor_surat')->nullable()->after($lastKnownColumnAfterFiles);
            }
            $lastKnownColumnAfterFiles = 'nomor_surat';
            
            if (!Schema::hasColumn('permohonan_kk_hilang', 'tanggal_selesai_proses')) {
                $table->timestamp('tanggal_selesai_proses')->nullable()->after($lastKnownColumnAfterFiles);
            }
            $lastKnownColumnAfterFiles = 'tanggal_selesai_proses';

            if (!Schema::hasColumn('permohonan_kk_hilang', 'file_hasil_akhir')) {
                $table->string('file_hasil_akhir')->nullable()->after($lastKnownColumnAfterFiles);
            }
            $lastKnownColumnAfterFiles = 'file_hasil_akhir';

            if (!Schema::hasColumn('permohonan_kk_hilang', 'catatan_penolakan')) {
                $table->text('catatan_penolakan')->nullable()->after($lastKnownColumnAfterFiles);
            }
            
            // 6. Mengubah enum 'status' untuk menambahkan 'selesai'
            // Pastikan package doctrine/dbal sudah terinstall: composer require doctrine/dbal
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

            // Hanya drop kolom yang PASTI ditambahkan oleh migrasi ini
            if (Schema::hasColumn('permohonan_kk_hilang', 'masyarakat_id')) $columnsToDrop[] = 'masyarakat_id'; // Hati-hati dengan foreign key jika di-rollback
            if (Schema::hasColumn('permohonan_kk_hilang', 'file_kk_lama')) $columnsToDrop[] = 'file_kk_lama';
            if (Schema::hasColumn('permohonan_kk_hilang', 'file_ktp_pemohon')) $columnsToDrop[] = 'file_ktp_pemohon';
            if (Schema::hasColumn('permohonan_kk_hilang', 'nomor_urut')) $columnsToDrop[] = 'nomor_urut';
            if (Schema::hasColumn('permohonan_kk_hilang', 'nomor_surat')) $columnsToDrop[] = 'nomor_surat';
            if (Schema::hasColumn('permohonan_kk_hilang', 'tanggal_selesai_proses')) $columnsToDrop[] = 'tanggal_selesai_proses';
            if (Schema::hasColumn('permohonan_kk_hilang', 'file_hasil_akhir')) $columnsToDrop[] = 'file_hasil_akhir';
            if (Schema::hasColumn('permohonan_kk_hilang', 'catatan_penolakan')) $columnsToDrop[] = 'catatan_penolakan';
            
            if (!empty($columnsToDrop)) {
                // Untuk foreign key 'masyarakat_id', jika menggunakan ->constrained(), Laravel akan tahu cara menghapusnya.
                // Jika dibuat manual, Anda mungkin perlu $table->dropForeign(['masyarakat_id']); atau nama constraint spesifiknya.
                // Untuk keamanan, kita drop kolomnya saja. Jika ada constraint, akan error dan perlu penanganan manual.
                $table->dropColumn($columnsToDrop);
            }

            // Kembalikan perubahan nama kolom
            if (Schema::hasColumn('permohonan_kk_hilang', 'catatan_pemohon') && !Schema::hasColumn('permohonan_kk_hilang', 'catatan')) {
                 $table->renameColumn('catatan_pemohon', 'catatan');
            }
            // Kembalikan enum 'status' ke versi lama
            if (Schema::hasColumn('permohonan_kk_hilang', 'status')) {
                $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();
            }
        });
    }
};
