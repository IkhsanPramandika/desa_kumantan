<?php

namespace App\Models;

use App\Traits\NomorSuratGenerator;
use App\Interfaces\PermohonanInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @property int $id
 * @property-read \App\Models\Masyarakat $masyarakat
 */
class PermohonanKKHilang extends Model implements PermohonanInterface
{
    use HasFactory,NomorSuratGenerator;

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

     // ===================================================================
    // IMPLEMENTASI METODE DARI INTERFACE
    // ===================================================================

    public function getJudulNotifikasi(): string
    {
        return "Permohonan KK Hilang";
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
        return route('petugas.permohonan-kk-hilang.show', $this->id);
    }
}
