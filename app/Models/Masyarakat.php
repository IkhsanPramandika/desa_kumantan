<?php

namespace App\Models;

use App\Models\PermohonananSKDomisili;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens; // Untuk autentikasi API via Sanctum
use Illuminate\Foundation\Auth\User as Authenticatable; // Penting untuk autentikasi

class Masyarakat extends Authenticatable // Ganti Model menjadi Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'masyarakat'; // Pastikan nama tabel sesuai

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'password',
        'nomor_hp',
        'email',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat_lengkap',
        'rt',
        'rw',
        'dusun_atau_lingkungan',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'foto_ktp',
        'status_akun',
        'catatan_verifikasi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', // Jika Anda menambahkan verifikasi email untuk masyarakat
        'tanggal_lahir' => 'date',
        'password' => 'hashed', // Otomatis hash password saat diset
    ];

    // Relasi ke permohonan-permohonan (contoh)
    public function permohonananSkDomisili()
    {
        return $this->hasMany(PermohonananSKDomisili::class); // Ganti PermohonanSKDomisili dengan nama model yang benar
    }
    // Tambahkan relasi lain jika perlu
}
