<?php

namespace App\Models;

use App\Models\Masyarakat;
use App\Interfaces\PermohonanInterface;
use App\Traits\NomorSuratGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property-read \App\Models\Masyarakat $masyarakat
 */

class PermohonanKKBaru extends Model implements PermohonanInterface
{
    use HasFactory,NomorSuratGenerator;

    
    protected $table = 'permohonan_kk_baru';

    protected $fillable = [
        'masyarakat_id',
        'file_kk',
        'file_ktp',
        'surat_pengantar_rt_rw',
        'buku_nikah_akta_cerai',
        'surat_pindah_datang',
        'ijazah_terakhir',
        'catatan_pemohon',
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

// ===================================================================
    // IMPLEMENTASI METODE DARI INTERFACE
    // ===================================================================

    public function getJudulNotifikasi(): string
    {
        return "Permohonan KK Baru";
    }

    public function getPemohon(): Masyarakat
    {
        return $this->masyarakat;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRouteTujuan(): string
    {
        // Pastikan nama route ini benar
        return route('petugas.permohonan-kk-baru.show', $this->id);
    }
}

