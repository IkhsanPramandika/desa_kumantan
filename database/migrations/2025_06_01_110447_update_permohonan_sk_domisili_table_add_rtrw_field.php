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
            if (Schema::hasColumn('permohonan_sk_domisili', 'catatan') && !Schema::hasColumn('permohonan_sk_domisili', 'catatan_internal')) {
                $table->renameColumn('catatan', 'catatan_internal');
            } elseif (!Schema::hasColumn('permohonan_sk_domisili', 'catatan_internal') && !Schema::hasColumn('permohonan_sk_domisili', 'catatan')) {
                // Jika keduanya tidak ada, tambahkan catatan_internal (sesuaikan posisi jika perlu)
                $table->text('catatan_internal')->nullable()->after('file_ktp'); // Asumsi file_ktp sudah ada
            }

            // Mengubah enum 'status' untuk menambahkan 'selesai'
            // Pastikan doctrine/dbal terinstall: composer require doctrine/dbal
            if (Schema::hasColumn('permohonan_sk_domisili', 'status')) {
                $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending')->change();
            }


            // Tambahkan kolom-kolom baru yang belum ada
            if (!Schema::hasColumn('permohonan_sk_domisili', 'nama_pemohon_atau_lembaga')) {
                $table->string('nama_pemohon_atau_lembaga')->after('id');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'nik_pemohon')) {
                $table->string('nik_pemohon', 20)->nullable()->after('nama_pemohon_atau_lembaga');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'jenis_kelamin_pemohon')) {
                $table->string('jenis_kelamin_pemohon', 20)->nullable()->after('nik_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'tempat_lahir_pemohon')) {
                $table->string('tempat_lahir_pemohon', 100)->nullable()->after('jenis_kelamin_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'tanggal_lahir_pemohon')) {
                $table->date('tanggal_lahir_pemohon')->nullable()->after('tempat_lahir_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'pekerjaan_pemohon')) {
                $table->string('pekerjaan_pemohon', 100)->nullable()->after('tanggal_lahir_pemohon');
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
                $table->string('dusun_domisili', 100)->nullable()->after('rw_domisili');
            }
            if (!Schema::hasColumn('permohonan_sk_domisili', 'keperluan_domisili')) {
                $table->text('keperluan_domisili')->after('dusun_domisili');
            }

            // Tambahkan kolom file_surat_pengantar_rt_rw jika belum ada
            // Asumsikan file_kk dan file_ktp sudah ada dari migrasi create awal
            if (!Schema::hasColumn('permohonan_sk_domisili', 'file_surat_pengantar_rt_rw')) {
                $table->string('file_surat_pengantar_rt_rw')->nullable()->after('file_ktp'); // Setelah file_ktp
            }

            // Kolom untuk informasi surat
            if (!Schema::hasColumn('permohonan_sk_domisili', 'nomor_urut')) {
                $table->integer('nomor_urut')->nullable()->comment('Nomor urut surat')->after('file_surat_pengantar_rt_rw');
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
            
            // Tambahkan catatan_penolakan
            $posisiSetelahCatatanInternal = Schema::hasColumn('permohonan_sk_domisili', 'catatan_internal') ? 'catatan_internal' : 'status';
            if (!Schema::hasColumn('permohonan_sk_domisili', 'catatan_penolakan')) {
                $table->text('catatan_penolakan')->nullable()->comment('Alasan penolakan')->after($posisiSetelahCatatanInternal);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_domisili', function (Blueprint $table) {
            $columnsToDrop = [
                'nama_pemohon_atau_lembaga', 'nik_pemohon', 'jenis_kelamin_pemohon', 
                'tempat_lahir_pemohon', 'tanggal_lahir_pemohon', 'pekerjaan_pemohon', 
                'alamat_lengkap_domisili', 'rt_domisili', 'rw_domisili', 'dusun_domisili', 
                'keperluan_domisili', 'file_surat_pengantar_rt_rw', 'nomor_urut', 'nomor_surat', 
                'tanggal_selesai_proses', 'file_hasil_akhir', 'catatan_penolakan'
            ];

            $existingColumnsToDrop = [];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('permohonan_sk_domisili', $column)) {
                    $existingColumnsToDrop[] = $column;
                }
            }
            if (!empty($existingColumnsToDrop)) {
                $table->dropColumn($existingColumnsToDrop);
            }

            if (Schema::hasColumn('permohonan_sk_domisili', 'catatan_internal') && !Schema::hasColumn('permohonan_sk_domisili', 'catatan')) {
                 $table->renameColumn('catatan_internal', 'catatan');
            }
            if (Schema::hasColumn('permohonan_sk_domisili', 'status')) {
                $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();
            }
        });
    }
};
