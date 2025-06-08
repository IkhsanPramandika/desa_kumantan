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
        Schema::table('permohonan_sk_domisili', function (Blueprint $table) {
            // Ubah kolom yang sudah ada jika perlu
            // Misalnya, mengganti nama 'catatan' menjadi 'catatan_internal'
            if (Schema::hasColumn('permohonan_sk_domisili', 'catatan')) {
                $table->renameColumn('catatan', 'catatan_internal');
            }
            // Mengubah enum 'status' untuk menambahkan 'selesai'
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending')->change();

            // Tambahkan kolom-kolom baru yang belum ada
            // Pastikan kolom ini belum ada di tabel lama Anda sebelum menambahkannya
            if (!Schema::hasColumn('permohonan_sk_domisili', 'nama_pemohon_atau_lembaga')) {
                $table->string('nama_pemohon_atau_lembaga')->after('id'); // Tentukan posisi jika perlu
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'nik_pemohon')) {
                $table->string('nik_pemohon')->nullable()->after('nama_pemohon_atau_lembaga');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'jenis_kelamin_pemohon')) {
                $table->string('jenis_kelamin_pemohon')->nullable()->after('nik_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'tempat_lahir_pemohon')) {
                $table->string('tempat_lahir_pemohon')->nullable()->after('jenis_kelamin_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'tanggal_lahir_pemohon')) {
                $table->date('tanggal_lahir_pemohon')->nullable()->after('tempat_lahir_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'pekerjaan_pemohon')) {
                $table->string('pekerjaan_pemohon')->nullable()->after('tanggal_lahir_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'alamat_lengkap_domisili')) {
                $table->text('alamat_lengkap_domisili')->after('pekerjaan_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'rt_domisili')) {
                $table->string('rt_domisili', 5)->nullable()->after('alamat_lengkap_domisili');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'rw_domisili')) {
                $table->string('rw_domisili', 5)->nullable()->after('rt_domisili');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'dusun_domisili')) {
                $table->string('dusun_domisili')->nullable()->after('rw_domisili');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'keperluan_domisili')) {
                $table->text('keperluan_domisili')->after('dusun_domisili');
            }
            // file_kk dan file_ktp sudah ada di migrasi lama Anda

            // Kolom untuk informasi surat
            if (!Schema::hasColumn('permohonan_sk_domisili', 'nomor_urut')) {
                $table->integer('nomor_urut')->nullable()->comment('Nomor urut surat')->after('file_ktp');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'nomor_surat')) {
                $table->string('nomor_surat')->nullable()->comment('Nomor surat lengkap')->after('nomor_urut');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'tanggal_selesai_proses')) {
                $table->timestamp('tanggal_selesai_proses')->nullable()->comment('Tanggal surat diterbitkan')->after('nomor_surat');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'file_hasil_akhir')) {
                $table->string('file_hasil_akhir')->nullable()->comment('Path ke file PDF')->after('tanggal_selesai_proses');
            }

            // catatan_internal sudah di-rename dari 'catatan'
            // Tambahkan catatan_penolakan
            if (!Schema::hasColumn('permohonan_sk_domisili', 'catatan_penolakan')) {
                $table->text('catatan_penolakan')->nullable()->comment('Alasan penolakan')->after('catatan_internal'); // atau after status
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_domisili', function (Blueprint $table) {
            // Tuliskan kebalikan dari method up()
            // Hapus kolom yang ditambahkan, dan kembalikan perubahan jika ada
            // Ini perlu disesuaikan dengan apa saja yang Anda tambahkan/ubah di 'up()'

            // Contoh:
            // $table->dropColumn([
            //     'nama_pemohon_atau_lembaga', 
            //     'nik_pemohon',
            //     // ... daftar kolom lain yang ditambahkan ...
            //     'catatan_penolakan' 
            // ]);
            // $table->renameColumn('catatan_internal', 'catatan');
            // $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();

            // Anda perlu membuat ini lebih spesifik berdasarkan kolom yang benar-benar baru
            // Daripada mendaftar satu per satu, jika Anda menambahkan banyak,
            // bisa jadi lebih mudah untuk tidak mendefinisikan 'down' secara detail jika tidak akan di-rollback,
            // atau pastikan Anda tahu persis kolom apa saja yang baru ditambahkan.
            // Untuk keamanan, hanya definisikan drop untuk kolom yang PASTI baru.
            $columnsToDrop = [];
            if (Schema::hasColumn('permohonan_sk_domisili', 'nama_pemohon_atau_lembaga')) $columnsToDrop[] = 'nama_pemohon_atau_lembaga';
            if (Schema::hasColumn('permohonan_sk_domisili', 'nik_pemohon')) $columnsToDrop[] = 'nik_pemohon';
            if (Schema::hasColumn('permohonan_sk_domisili', 'jenis_kelamin_pemohon')) $columnsToDrop[] = 'jenis_kelamin_pemohon';
            if (Schema::hasColumn('permohonan_sk_domisili', 'tempat_lahir_pemohon')) $columnsToDrop[] = 'tempat_lahir_pemohon';
            if (Schema::hasColumn('permohonan_sk_domisili', 'tanggal_lahir_pemohon')) $columnsToDrop[] = 'tanggal_lahir_pemohon';
            if (Schema::hasColumn('permohonan_sk_domisili', 'pekerjaan_pemohon')) $columnsToDrop[] = 'pekerjaan_pemohon';
            if (Schema::hasColumn('permohonan_sk_domisili', 'alamat_lengkap_domisili')) $columnsToDrop[] = 'alamat_lengkap_domisili';
            if (Schema::hasColumn('permohonan_sk_domisili', 'rt_domisili')) $columnsToDrop[] = 'rt_domisili';
            if (Schema::hasColumn('permohonan_sk_domisili', 'rw_domisili')) $columnsToDrop[] = 'rw_domisili';
            if (Schema::hasColumn('permohonan_sk_domisili', 'dusun_domisili')) $columnsToDrop[] = 'dusun_domisili';
            if (Schema::hasColumn('permohonan_sk_domisili', 'keperluan_domisili')) $columnsToDrop[] = 'keperluan_domisili';
            if (Schema::hasColumn('permohonan_sk_domisili', 'nomor_urut')) $columnsToDrop[] = 'nomor_urut';
            if (Schema::hasColumn('permohonan_sk_domisili', 'nomor_surat')) $columnsToDrop[] = 'nomor_surat';
            if (Schema::hasColumn('permohonan_sk_domisili', 'tanggal_selesai_proses')) $columnsToDrop[] = 'tanggal_selesai_proses';
            if (Schema::hasColumn('permohonan_sk_domisili', 'file_hasil_akhir')) $columnsToDrop[] = 'file_hasil_akhir';
            if (Schema::hasColumn('permohonan_sk_domisili', 'catatan_penolakan')) $columnsToDrop[] = 'catatan_penolakan';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            // Kembalikan perubahan kolom 'catatan' dan 'status'
            if (Schema::hasColumn('permohonan_sk_domisili', 'catatan_internal')) {
                 $table->renameColumn('catatan_internal', 'catatan');
            }
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();
        });
    }
};