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
            // Menambahkan kolom data anak
            $table->string('nama_anak')->nullable()->after('status');
            $table->string('tempat_lahir_anak')->nullable()->after('nama_anak');
            $table->date('tanggal_lahir_anak')->nullable()->after('tempat_lahir_anak');
            $table->enum('jenis_kelamin_anak', ['Laki-laki', 'Perempuan'])->nullable()->after('tanggal_lahir_anak');
            $table->string('agama_anak')->nullable()->after('jenis_kelamin_anak');
            $table->text('alamat_anak')->nullable()->after('agama_anak');

            // Menambahkan kolom data orang tua
            $table->string('nama_ayah')->nullable()->after('alamat_anak');
            $table->string('nik_ayah')->nullable()->after('nama_ayah');
            $table->string('nama_ibu')->nullable()->after('nik_ayah');
            $table->string('nik_ibu')->nullable()->after('nama_ibu');
            $table->string('no_buku_nikah')->nullable()->after('nik_ibu'); // Nomor buku nikah dari orang tua

            // Menambahkan kolom untuk proses petugas
            $table->string('file_hasil_akhir')->nullable()->after('no_buku_nikah');
            $table->timestamp('tanggal_selesai_proses')->nullable()->after('file_hasil_akhir');
            $table->text('catatan_penolakan')->nullable()->after('catatan'); // Tambahkan setelah kolom 'catatan'

            // Memperbarui ENUM status
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'diproses', 'selesai'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sk_kelahiran', function (Blueprint $table) {
            // Mengembalikan ENUM status ke definisi awal
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending')->change();

            // Menghapus kolom yang ditambahkan
            $table->dropColumn([
                'nama_anak', 'tempat_lahir_anak', 'tanggal_lahir_anak', 'jenis_kelamin_anak',
                'agama_anak', 'alamat_anak', 'nama_ayah', 'nik_ayah', 'nama_ibu', 'nik_ibu',
                'no_buku_nikah', 'file_hasil_akhir', 'tanggal_selesai_proses', 'catatan_penolakan'
            ]);
        });
    }
};
