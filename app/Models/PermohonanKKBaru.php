<?php

namespace App\Models;

use App\Models\Masyarakat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermohonanKKBaru extends Model
{
    use HasFactory;

    protected $table = 'permohonan_kk_baru';

    protected $fillable = [
        'masyarakat_id',
        'file_kk',
        'file_ktp',
        'surat_pengantar_rt_rw',
        'buku_nikah_akta_cerai',
        'surat_pindah_datang',
        'ijazah_terakhir',
        'catatan_pemohon', // Mengganti 'catatan' menjadi lebih deskriptif
        'status',
        'file_hasil_akhir',
        'tanggal_selesai_proses',
        'catatan_penolakan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_selesai_proses' => 'datetime',
    ];

    /**
     * Relasi ke model Masyarakat.
     */
   public function masyarakat()
{

 return $this->belongsTo(Masyarakat::class, 'masyarakat_id');
}

}