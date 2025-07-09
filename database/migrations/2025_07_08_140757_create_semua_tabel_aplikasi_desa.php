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
        // === TABEL UTAMA & PENGGUNA ===
        Schema::create('masyarakat', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->unique();
            $table->string('nama_lengkap');
            $table->string('password');
            $table->string('nomor_hp', 20)->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['LAKI-LAKI', 'PEREMPUAN'])->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('dusun_atau_lingkungan', 100)->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('status_perkawinan', 50)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('foto_ktp')->nullable();
            $table->enum('status_akun', ['pending_verification', 'active', 'inactive', 'rejected'])->default('pending_verification');
            $table->text('catatan_verifikasi')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('masyarakat_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });


        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // === TABEL KONTEN & PENGATURAN ===
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('isi');
            $table->string('gambar_pengumuman')->nullable();
            $table->string('file_pengumuman')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_publikasi');
            $table->enum('status_publikasi', ['draft', 'dipublikasikan'])->default('draft');
            $table->timestamps();
        });

        Schema::create('template_surat', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_layanan')->unique();
            $table->text('template_html');
            $table->timestamps();
        });

        Schema::create('nomor_surat_counters', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_surat')->unique();
            $table->unsignedInteger('nomor_terakhir')->default(0);
            $table->string('format')->comment('Contoh: {nomor}/SKU/{bulan}/{tahun}');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // === TABEL-TABEL PERMOHONAN ===
        Schema::create('permohonan_kk_baru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('surat_pengantar_rt_rw')->nullable();
            $table->string('buku_nikah_akta_cerai')->nullable();
            $table->string('surat_pindah_datang')->nullable();
            $table->string('ijazah_terakhir')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->enum('status', ['pending', 'diterima', 'diproses', 'ditolak', 'selesai'])->default('pending');
            $table->string('file_hasil_akhir')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->timestamps();
        });

        Schema::create('permohonan_kk_hilang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('surat_pengantar_rt_rw')->nullable();
            $table->string('surat_keterangan_hilang_kepolisian')->nullable();
            $table->string('file_kk_lama')->nullable();
            $table->string('file_ktp_pemohon')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->string('file_hasil_akhir')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'diproses', 'selesai'])->default('pending');
            $table->timestamps();
        });

        Schema::create('permohonan_kk_perubahan_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('file_kk')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('surat_pengantar_rt_rw')->nullable();
            $table->string('surat_keterangan_pendukung')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'diproses', 'selesai'])->default('pending');
            $table->string('file_hasil_akhir')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->timestamps();
        });

        Schema::create('permohonan_sk_ahli_waris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('nama_pewaris')->nullable();
            $table->string('nik_pewaris', 16)->nullable();
            $table->string('tempat_lahir_pewaris')->nullable();
            $table->date('tanggal_lahir_pewaris')->nullable();
            $table->date('tanggal_meninggal_pewaris')->nullable();
            $table->string('alamat_pewaris', 500)->nullable();
            $table->json('daftar_ahli_waris')->nullable();
            $table->string('file_kk_pemohon')->nullable();
            $table->string('file_ktp_pemohon')->nullable();
            $table->string('file_kk_ahli_waris')->nullable();
            $table->string('file_ktp_ahli_waris')->nullable();
            $table->string('surat_pengantar_rt_rw')->nullable();
            $table->string('surat_keterangan_kematian')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat_ahli_waris')->nullable();
            $table->string('catatan_penolakan', 500)->nullable();
            $table->string('file_hasil_akhir')->nullable();
            $table->datetime('tanggal_selesai_proses')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending');
            $table->timestamps();
        });

        Schema::create('permohonan_sk_domisili', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('nama_pemohon_atau_lembaga');
            $table->string('nik_pemohon')->nullable();
            $table->string('jenis_kelamin_pemohon')->nullable();
            $table->string('tempat_lahir_pemohon')->nullable();
            $table->date('tanggal_lahir_pemohon')->nullable();
            $table->string('pekerjaan_pemohon')->nullable();
            $table->text('alamat_lengkap_domisili');
            $table->string('rt_domisili', 5)->nullable();
            $table->string('rw_domisili', 5)->nullable();
            $table->string('dusun_domisili')->nullable();
            $table->text('keperluan_domisili');
            $table->string('file_kk')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_surat_pengantar_rt_rw')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->string('file_hasil_akhir')->nullable();
            $table->text('catatan_internal')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending');
            $table->timestamps();
        });

        Schema::create('permohonan_sk_kelahiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('nama_anak')->nullable();
            $table->string('tempat_lahir_anak')->nullable();
            $table->date('tanggal_lahir_anak')->nullable();
            $table->enum('jenis_kelamin_anak', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('agama_anak')->nullable();
            $table->text('alamat_anak')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nik_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nik_ibu')->nullable();
            $table->string('no_buku_nikah')->nullable();
            $table->string('file_kk')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('surat_pengantar_rt_rw')->nullable();
            $table->string('surat_nikah_orangtua')->nullable();
            $table->string('surat_keterangan_kelahiran')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('file_hasil_akhir')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'diproses', 'selesai'])->default('pending');
            $table->timestamps();
        });

        Schema::create('permohonan_sk_kematian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('file_kk')->nullable();
            $table->string('file_ktp_yang_meninggal')->nullable();
            $table->string('file_ktp_pelapor')->nullable();
            $table->string('surat_keterangan_kematian')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->string('file_hasil_akhir')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending');
            $table->timestamps();
        });
        
        Schema::create('permohonan_sk_perkawinan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('nama_pria')->nullable();
            $table->string('nik_pria', 16)->nullable();
            $table->string('tempat_lahir_pria')->nullable();
            $table->date('tanggal_lahir_pria')->nullable();
            $table->text('alamat_pria')->nullable();
            $table->string('nama_wanita')->nullable();
            $table->string('nik_wanita', 16)->nullable();
            $table->string('tempat_lahir_wanita')->nullable();
            $table->date('tanggal_lahir_wanita')->nullable();
            $table->text('alamat_wanita')->nullable();
            $table->date('tanggal_akad')->nullable();
            $table->string('tempat_akad')->nullable();
            $table->string('saksi_1')->nullable();
            $table->string('saksi_2')->nullable();
            $table->string('file_kk')->nullable();
            $table->string('file_ktp_mempelai')->nullable();
            $table->string('surat_nikah_orang_tua')->nullable();
            $table->string('kartu_imunisasi_catin')->nullable();
            $table->string('sertifikat_elsimil')->nullable();
            $table->string('akta_penceraian')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->string('file_hasil_akhir')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'diproses', 'selesai'])->default('pending');
            $table->timestamps();
        });

        Schema::create('permohonan_sk_tidak_mampu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('nama_pemohon');
            $table->string('nik_pemohon', 20);
            $table->string('tempat_lahir_pemohon', 100);
            $table->date('tanggal_lahir_pemohon');
            $table->string('jenis_kelamin_pemohon', 20);
            $table->string('agama_pemohon', 50)->nullable();
            $table->string('kewarganegaraan_pemohon', 50)->default('Indonesia');
            $table->string('pekerjaan_pemohon', 100);
            $table->text('alamat_pemohon');
            $table->string('nama_terkait')->nullable();
            $table->string('nik_terkait', 20)->nullable();
            $table->string('tempat_lahir_terkait', 100)->nullable();
            $table->date('tanggal_lahir_terkait')->nullable();
            $table->string('jenis_kelamin_terkait', 20)->nullable();
            $table->string('agama_terkait', 50)->nullable();
            $table->string('kewarganegaraan_terkait', 50)->default('Indonesia');
            $table->string('pekerjaan_atau_sekolah_terkait', 100)->nullable();
            $table->text('alamat_terkait')->nullable();
            $table->text('keperluan_surat');
            $table->string('file_kk')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_pendukung_lain')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->string('file_hasil_akhir')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending');
            $table->timestamps();
        });

        Schema::create('permohonan_sk_usaha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masyarakat_id')->nullable()->constrained('masyarakat')->onDelete('set null');
            $table->string('nama_pemohon')->nullable();
            $table->string('nik_pemohon')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('warganegara_agama')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->text('alamat_pemohon')->nullable();
            $table->string('nama_usaha')->nullable();
            $table->string('alamat_usaha')->nullable();
            $table->string('file_kk')->nullable();
            $table->string('file_ktp')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->string('file_hasil_akhir')->nullable();
            $table->timestamp('tanggal_selesai_proses')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus dalam urutan terbalik dari method up() untuk keamanan foreign key
        Schema::dropIfExists('permohonan_sk_usaha');
        Schema::dropIfExists('permohonan_sk_tidak_mampu');
        Schema::dropIfExists('permohonan_sk_perkawinan');
        Schema::dropIfExists('permohonan_sk_kematian');
        Schema::dropIfExists('permohonan_sk_kelahiran');
        Schema::dropIfExists('permohonan_sk_domisili');
        Schema::dropIfExists('permohonan_sk_ahli_waris');
        Schema::dropIfExists('permohonan_kk_perubahan_data');
        Schema::dropIfExists('permohonan_kk_hilang');
        Schema::dropIfExists('permohonan_kk_baru');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('nomor_surat_counters');
        Schema::dropIfExists('template_surat');
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('masyarakat_password_reset_tokens');
        Schema::dropIfExists('masyarakat');
        Schema::dropIfExists('personal_access_tokens');
    }
};