<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Panggil seeder lain yang mungkin Anda miliki di sini
            KepalaDesaUserSeeder::class, // Tambahkan baris ini
        ]);
    }
    
}
