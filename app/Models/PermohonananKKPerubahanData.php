<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonananKKPerubahanData extends Model
{
    use HasFactory;
    
    protected $table = 'permohonan_kk_perubahan_data';

    protected $fillable = [
        'masyarakat_id',
        'file_kk',
        'file_ktp',
        'surat_pengantar_rt_rw',
        'surat_keterangan_pendukung',
        'catatan_pemohon', // Disesuaikan untuk konsistensi
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
     * @var array<string, string>
     */
    protected $casts = [
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
