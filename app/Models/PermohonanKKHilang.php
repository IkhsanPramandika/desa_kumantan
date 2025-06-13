<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermohonanKKHilang extends Model
{
    use HasFactory;

    protected $table = 'permohonan_kk_hilang';

    protected $fillable = [
        'masyarakat_id',
        'surat_pengantar_rt_rw',
        'surat_keterangan_hilang_kepolisian',
        'catatan_pemohon', // Direkomendasikan untuk konsistensi
        'status',
        'nomor_urut',
        'nomor_surat',
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
        // TAMBAHKAN BARIS INI
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
