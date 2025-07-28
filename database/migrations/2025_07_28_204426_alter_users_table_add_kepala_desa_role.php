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
        Schema::table('users', function (Blueprint $table) {
            // Drop existing 'role' column if it's not an enum or needs to be changed
            $table->dropColumn('role');

            // Add the new 'role' column as an ENUM
            $table->enum('role', ['petugas', 'kepala_desa'])->default('petugas')->after('fcm_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert 'role' column back to its original state (e.g., varchar)
            $table->dropColumn('role');
            $table->string('role')->default('petugas')->after('fcm_token'); // Adjust to your original type if different
        });
    }
};