<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

use App\Models\PermohonanKKBaru;
use App\Models\PermohonanKKHilang;
use App\Models\PermohonanKKPerubahanData;
use App\Models\PermohonanSKAhliWaris;
use App\Models\PermohonanSKKelahiran;
use App\Models\PermohonanSKDomisili;
use App\Models\PermohonanSKPerkawinan;
use App\Models\PermohonanSKTidakMampu;
use App\Models\PermohonanSKUsaha;

class Masyarakat extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'masyarakat';

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
        'email_verified_at' => 'datetime',
        'tanggal_lahir' => 'date',
        'password' => 'hashed',
    ];

    // Relasi ke masing-masing permohonan
    public function permohonanKKBaru()
    {
        return $this->hasMany(PermohonanKKBaru::class);
    }

    public function permohonanKKHilang()
    {
        return $this->hasMany(PermohonanKKHilang::class);
    }

    public function permohonanKKPerubahanData()
    {
        return $this->hasMany(PermohonanKKPerubahanData::class);
    }

    public function permohonanSKAhliWaris()
    {
        return $this->hasMany(PermohonanSKAhliWaris::class);
    }

    public function permohonanSKKelahiran()
    {
        return $this->hasMany(PermohonanSKKelahiran::class);
    }

    public function permohonanSKDomisili()
    {
        return $this->hasMany(PermohonanSKDomisili::class);
    }

    public function permohonanSKPerkawinan()
    {
        return $this->hasMany(PermohonanSKPerkawinan::class);
    }

    public function permohonanSKTidakMampu()
    {
        return $this->hasMany(PermohonanSKTidakMampu::class);
    }

    public function permohonanSKUsaha()
    {
        return $this->hasMany(PermohonanSKUsaha::class);
    }
}
