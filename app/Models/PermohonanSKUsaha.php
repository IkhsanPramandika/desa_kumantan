<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSKUsaha extends Model
{
    use HasFactory;

    protected $table = 'permohonan_sk_usaha';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'masyarakat_id', // Penting untuk relasi
        'file_kk',
        'file_ktp',
        'nama_pemohon',
        'nik_pemohon',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'warganegara_agama',
        'pekerjaan',
        'alamat_pemohon',
        'nama_usaha',
        'alamat_usaha',
        'catatan_pemohon', // Disesuaikan untuk konsistensi
        'status',
        'catatan_penolakan',
        'nomor_urut',
        'nomor_surat',
        'file_hasil_akhir',
        'tanggal_selesai_proses',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_selesai_proses' => 'datetime',
    ];

    /**
     * Relasi ke model Masyarakat.
     */
    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class);
    }
}
