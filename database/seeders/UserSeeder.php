<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
 public function run(): void
{
    // Membuat akun dengan role 'admin'
    User::create([
        'name' => 'Nama Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'petugas', // <-- TAMBAHKAN BARIS INI
    ]);

    // Contoh membuat akun dengan role 'user'
}
}