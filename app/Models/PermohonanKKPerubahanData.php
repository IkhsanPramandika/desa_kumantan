<?php

namespace App\Models;

use App\Interfaces\PermohonanInterface; // <-- Pastikan ini ada
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property-read \App\Models\Masyarakat $masyarakat
 */
class PermohonanKKPerubahanData extends Model implements PermohonanInterface // <-- Pastikan ini ada
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

    // Pastikan Anda punya relasi ke masyarakat
    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class);
    }

    // ===================================================================
    // [INI BAGIAN PENTING] IMPLEMENTASI METODE DARI INTERFACE
    // ===================================================================

    public function getJudulNotifikasi(): string
    {
        return "Perubahan Data KK"; 
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
        // Pastikan nama route ini benar sesuai dengan file routes/web.php Anda
        return route('petugas.permohonan-kk-perubahan.show', $this->id);
    }
}