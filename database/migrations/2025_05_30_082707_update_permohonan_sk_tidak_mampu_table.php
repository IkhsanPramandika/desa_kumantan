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
        Schema::table('permohonan_sk_tidak_mampu', function (Blueprint $table) {
            // Ganti nama kolom 'catatan' menjadi 'catatan_pemohon' jika ada
            // dan tambahkan kolom baru lainnya setelah kolom yang sudah ada.
            // Tentukan posisi kolom baru dengan ->after('nama_kolom_sebelumnya')

            // Cek apakah kolom 'catatan' ada sebelum mengubah namanya
            if (Schema::hasColumn('permohonan_sk_tidak_mampu', 'catatan')) {
                $table->renameColumn('catatan', 'catatan_pemohon');
            } elseif (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'catatan_pemohon')) {
                // Jika 'catatan' tidak ada, tapi 'catatan_pemohon' juga belum ada, tambahkan 'catatan_pemohon'
                $table->text('catatan_pemohon')->nullable()->after('file_ktp');
            }

            // Ubah enum 'status' untuk menambahkan 'selesai'
            // Pastikan doctrine/dbal terinstall: composer require doctrine/dbal
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending')->change();

            // Tambahkan kolom-kolom baru (pastikan nama kolom unik dan belum ada)
            // Data Pemohon Utama
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'nama_pemohon')) {
                $table->string('nama_pemohon')->after('id'); // Atau sesuaikan posisi
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'nik_pemohon')) {
                $table->string('nik_pemohon', 20)->after('nama_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'tempat_lahir_pemohon')) {
                $table->string('tempat_lahir_pemohon', 100)->after('nik_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'tanggal_lahir_pemohon')) {
                $table->date('tanggal_lahir_pemohon')->after('tempat_lahir_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'jenis_kelamin_pemohon')) {
                $table->string('jenis_kelamin_pemohon', 20)->after('tanggal_lahir_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'agama_pemohon')) {
                $table->string('agama_pemohon', 50)->nullable()->after('jenis_kelamin_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'kewarganegaraan_pemohon')) {
                $table->string('kewarganegaraan_pemohon', 50)->default('Indonesia')->after('agama_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'pekerjaan_pemohon')) {
                $table->string('pekerjaan_pemohon', 100)->after('kewarganegaraan_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'alamat_pemohon')) {
                $table->text('alamat_pemohon')->after('pekerjaan_pemohon');
            }

            // Data Anak/Anggota Keluarga yang Berkepentingan
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'nama_terkait')) {
                $table->string('nama_terkait')->nullable()->comment('Nama anak/anggota keluarga')->after('alamat_pemohon');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'nik_terkait')) {
                $table->string('nik_terkait', 20)->nullable()->after('nama_terkait');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'tempat_lahir_terkait')) {
                $table->string('tempat_lahir_terkait', 100)->nullable()->after('nik_terkait');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'tanggal_lahir_terkait')) {
                $table->date('tanggal_lahir_terkait')->nullable()->after('tempat_lahir_terkait');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'jenis_kelamin_terkait')) {
                $table->string('jenis_kelamin_terkait', 20)->nullable()->after('tanggal_lahir_terkait');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'agama_terkait')) {
                $table->string('agama_terkait', 50)->nullable()->after('jenis_kelamin_terkait');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'kewarganegaraan_terkait')) {
                $table->string('kewarganegaraan_terkait', 50)->nullable()->default('Indonesia')->after('agama_terkait');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'pekerjaan_atau_sekolah_terkait')) {
                $table->string('pekerjaan_atau_sekolah_terkait', 100)->nullable()->after('kewarganegaraan_terkait');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'alamat_terkait')) {
                $table->text('alamat_terkait')->nullable()->after('pekerjaan_atau_sekolah_terkait');
            }

            // Keperluan Surat
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'keperluan_surat')) {
                $table->text('keperluan_surat')->after('alamat_terkait');
            }

            // Dokumen Pendukung Lain
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'file_pendukung_lain')) {
                $table->string('file_pendukung_lain')->nullable()->after('file_ktp');
            }

            // Informasi Surat
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'nomor_urut')) {
                $table->integer('nomor_urut')->nullable()->after('status');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'nomor_surat')) {
                $table->string('nomor_surat')->nullable()->after('nomor_urut');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'tanggal_selesai_proses')) {
                $table->timestamp('tanggal_selesai_proses')->nullable()->after('nomor_surat');
            }
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'file_hasil_akhir')) {
                $table->string('file_hasil_akhir')->nullable()->after('tanggal_selesai_proses');
            }
            
            // Catatan Penolakan
            if (!Schema::hasColumn('permohonan_sk_tidak_mampu', 'catatan_penolakan')) {
                $table->text('catatan_penolakan')->nullable()->after('catatan_pemohon'); // Setelah catatan_pemohon (yang tadinya 'catatan')
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_tidak_mampu', function (Blueprint $table) {
            // Kembalikan perubahan (hati-hati dengan urutan dan keberadaan kolom)
            $columnsToDrop = [
                'nama_pemohon', 'nik_pemohon', 'tempat_lahir_pemohon', 'tanggal_lahir_pemohon', 
                'jenis_kelamin_pemohon', 'agama_pemohon', 'kewarganegaraan_pemohon', 'pekerjaan_pemohon', 
                'alamat_pemohon', 'nama_terkait', 'nik_terkait', 'tempat_lahir_terkait', 
                'tanggal_lahir_terkait', 'jenis_kelamin_terkait', 'agama_terkait', 'kewarganegaraan_terkait', 
                'pekerjaan_atau_sekolah_terkait', 'alamat_terkait', 'keperluan_surat', 
                'file_pendukung_lain', 'nomor_urut', 'nomor_surat', 'tanggal_selesai_proses', 
                'file_hasil_akhir', 'catatan_penolakan'
            ];

            // Filter kolom yang benar-benar ada sebelum mencoba menghapusnya
            $existingColumnsToDrop = [];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('permohonan_sk_tidak_mampu', $column)) {
                    $existingColumnsToDrop[] = $column;
                }
            }
            if (!empty($existingColumnsToDrop)) {
                $table->dropColumn($existingColumnsToDrop);
            }

            // Kembalikan nama kolom 'catatan_pemohon' menjadi 'catatan'
            if (Schema::hasColumn('permohonan_sk_tidak_mampu', 'catatan_pemohon')) {
                $table->renameColumn('catatan_pemohon', 'catatan');
            }
            // Kembalikan enum 'status' ke versi lama
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();
        });
    }
};
