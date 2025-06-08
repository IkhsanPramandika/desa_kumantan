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
            Schema::table('pengumuman', function (Blueprint $table) {
                // Tambahkan kolom-kolom baru yang belum ada di tabel 'pengumuman' lama Anda.
                // Sesuaikan posisi kolom dengan ->after('nama_kolom_sebelumnya') jika perlu.

                // Kolom yang ada di migrasi create lengkap tapi mungkin belum ada di tabel lama Anda:
                if (!Schema::hasColumn('pengumuman', 'slug')) {
                    $table->string('slug')->unique()->after('judul');
                }
                if (!Schema::hasColumn('pengumuman', 'gambar_pengumuman')) {
                    $table->string('gambar_pengumuman')->nullable()->after('isi');
                }
                if (!Schema::hasColumn('pengumuman', 'file_pengumuman')) {
                    $table->string('file_pengumuman')->nullable()->after('gambar_pengumuman');
                }
                if (!Schema::hasColumn('pengumuman', 'tanggal_publikasi')) {
                    // Jika user_id sudah ada, letakkan setelahnya atau sesuaikan
                    // Berdasarkan migrasi lama Anda, user_id sudah ada.
                    $table->date('tanggal_publikasi')->after('user_id'); 
                }
                // Kolom user_id sudah ada di migrasi lama Anda
                if (!Schema::hasColumn('pengumuman', 'status_publikasi')) {
                    $table->enum('status_publikasi', ['draft', 'dipublikasikan'])->default('draft')->after('tanggal_publikasi'); // Disesuaikan afternya
                }
                
                // Pastikan foreign key constraint untuk user_id sudah benar
                // Jika di migrasi lama Anda $table->foreignId('user_id')->constrained()->onDelete('cascade');
                // dan itu sudah sesuai, Anda tidak perlu mengubahnya di sini.
                // Jika onDelete('cascade') belum ada dan ingin ditambahkan:
                // 1. Hapus constraint lama (jika ada dan berbeda)
                // $table->dropForeign(['user_id']); // Hati-hati jika sudah ada data
                // 2. Tambahkan constraint baru
                // $table->foreign('user_id')
                //       ->references('id')->on('users')
                //       ->onDelete('cascade')
                //       ->change(); // atau ->modify() tergantung versi Laravel dan DB
                // Untuk perubahan constraint yang sudah ada, biasanya lebih aman dilakukan dalam migrasi terpisah
                // atau dengan sangat hati-hati. Untuk sekarang, kita fokus pada penambahan kolom.
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('pengumuman', function (Blueprint $table) {
                // Hapus kolom-kolom yang ditambahkan di method up()
                $columnsToDrop = [];
                if (Schema::hasColumn('pengumuman', 'slug')) {
                    $columnsToDrop[] = 'slug';
                }
                if (Schema::hasColumn('pengumuman', 'gambar_pengumuman')) {
                    $columnsToDrop[] = 'gambar_pengumuman';
                }
                if (Schema::hasColumn('pengumuman', 'file_pengumuman')) {
                    $columnsToDrop[] = 'file_pengumuman';
                }
                if (Schema::hasColumn('pengumuman', 'tanggal_publikasi')) {
                    $columnsToDrop[] = 'tanggal_publikasi';
                }
                if (Schema::hasColumn('pengumuman', 'status_publikasi')) {
                    $columnsToDrop[] = 'status_publikasi';
                }

                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    };
    