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
        Schema::table('permohonan_sk_kelahiran', function (Blueprint $table) {
            // 1. Tambahkan kolom masyarakat_id
            if (!Schema::hasColumn('permohonan_sk_kelahiran', 'masyarakat_id')) {
                $table->foreignId('masyarakat_id')
                      ->nullable()
                      ->after('id') 
                      ->comment('ID pengguna masyarakat yang mengajukan permohonan')
                      ->constrained('masyarakat')
                      ->onDelete('set null');
            }

            // 2. Tambahkan kolom data anak dan orang tua jika belum ada
            // Berdasarkan model Anda sebelumnya, ini mungkin sudah ada.
            // Pengecekan ini untuk memastikan migrasi aman dijalankan.
            $kolomData = [
                'nama_anak' => 'string', 'tempat_lahir_anak' => 'string', 'tanggal_lahir_anak' => 'date', 
                'jenis_kelamin_anak' => 'string', 'agama_anak' => 'string', 'alamat_anak' => 'text',
                'nama_ayah' => 'string', 'nik_ayah' => 'string', 'nama_ibu' => 'string', 'nik_ibu' => 'string',
                'no_buku_nikah' => 'string'
            ];

            $lastColumn = 'status'; // Kolom terakhir yang diketahui ada dari migrasi lama

            foreach ($kolomData as $namaKolom => $tipeData) {
                if (!Schema::hasColumn('permohonan_sk_kelahiran', $namaKolom)) {
                    if ($tipeData === 'string') {
                        $table->string($namaKolom)->nullable()->after($lastColumn);
                    } elseif ($tipeData === 'date') {
                        $table->date($namaKolom)->nullable()->after($lastColumn);
                    } elseif ($tipeData === 'text') {
                        $table->text($namaKolom)->nullable()->after($lastColumn);
                    }
                }
                $lastColumn = $namaKolom;
            }

            // 3. Tambahkan kolom standar permohonan lainnya
            if (!Schema::hasColumn('permohonan_sk_kelahiran', 'nomor_urut')) {
                $table->integer('nomor_urut')->nullable()->after('status');
            }
            if (!Schema::hasColumn('permohonan_sk_kelahiran', 'nomor_surat')) {
                $table->string('nomor_surat')->nullable()->after('nomor_urut');
            }
            if (!Schema::hasColumn('permohonan_sk_kelahiran', 'tanggal_selesai_proses')) {
                $table->timestamp('tanggal_selesai_proses')->nullable()->after('nomor_surat');
            }
            if (!Schema::hasColumn('permohonan_sk_kelahiran', 'file_hasil_akhir')) {
                $table->string('file_hasil_akhir')->nullable()->after('tanggal_selesai_proses');
            }
            if (!Schema::hasColumn('permohonan_sk_kelahiran', 'catatan_penolakan')) {
                $table->text('catatan_penolakan')->nullable()->after('file_hasil_akhir');
            }
            
            // 4. Mengubah enum 'status' untuk menyertakan 'selesai', 'diproses'
            if (Schema::hasColumn('permohonan_sk_kelahiran', 'status')) {
                $table->enum('status', ['pending', 'diterima', 'diproses', 'ditolak', 'selesai'])->default('pending')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_kelahiran', function (Blueprint $table) {
            $columnsToDrop = [
                'masyarakat_id', 'nama_anak', 'tempat_lahir_anak', 'tanggal_lahir_anak',
                'jenis_kelamin_anak', 'agama_anak', 'alamat_anak', 'nama_ayah', 'nik_ayah',
                'nama_ibu', 'nik_ibu', 'no_buku_nikah', 'nomor_urut', 'nomor_surat', 
                'tanggal_selesai_proses', 'file_hasil_akhir', 'catatan_penolakan'
            ];

            // Filter kolom yang benar-benar ada sebelum mencoba menghapusnya
            $existingColumnsToDrop = [];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('permohonan_sk_kelahiran', $column)) {
                    $existingColumnsToDrop[] = $column;
                }
            }
            if (!empty($existingColumnsToDrop)) {
                // Hapus foreign key dulu sebelum drop kolomnya
                if (in_array('masyarakat_id', $existingColumnsToDrop)) {
                    $table->dropForeign(['masyarakat_id']);
                }
                $table->dropColumn($existingColumnsToDrop);
            }

            if (Schema::hasColumn('permohonan_sk_kelahiran', 'status')) {
                $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();
            }
        });
    }
};
