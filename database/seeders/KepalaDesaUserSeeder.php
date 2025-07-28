<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan Anda mengimpor model User
use Illuminate\Support\Facades\Hash; // Untuk mengenkripsi password

class KepalaDesaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah user kepala desa sudah ada untuk menghindari duplikasi
        if (!User::where('email', 'kepala.desa@example.com')->exists()) {
            User::create([
                'name' => 'Kepala Desa',
                'email' => 'kepaladesa@example.com',
                'password' => Hash::make('password123'), // Ganti dengan password yang lebih kuat
                'role' => 'kepala_desa', // Menetapkan role sebagai 'kepala_desa'
                'email_verified_at' => now(), // Opsional: Tandai sebagai terverifikasi
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('User Kepala Desa berhasil ditambahkan!');
        } else {
            $this->command->info('User Kepala Desa sudah ada.');
        }
    }
}