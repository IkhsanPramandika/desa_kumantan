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
                    Schema::table('masyarakat', function (Blueprint $table) {
                        $table->string('fcm_token')->nullable()->after('email'); // Atau setelah kolom lain yang Anda inginkan
                    });
                }

                /**
                 * Reverse the migrations.
                 */
                public function down(): void
                {
                    Schema::table('masyarakat', function (Blueprint $table) {
                        $table->dropColumn('fcm_token');
                    });
                }
            };