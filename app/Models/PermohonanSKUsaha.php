<?php

namespace App\Models;

use App\Models\Masyarakat;
use App\Traits\NomorSuratGenerator;
use App\Interfaces\PermohonanInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * Class PermohonanSKUsaha
 *
 * [TAMBAHKAN INI] Beri tahu linter tentang "magic property" dari relasi.
 * @property-read \App\Models\Masyarakat $masyarakat
 */
class PermohonanSKUsaha extends Model implements PermohonanInterface
{
    use HasFactory, NomorSuratGenerator;

    protected $table = 'permohonan_sk_usaha';

    // ... (kode $fillable, $casts Anda yang sudah ada, biarkan saja) ...
    protected $fillable = [
        'masyarakat_id', 'file_kk', 'file_ktp', 'nama_pemohon', 'nik_pemohon', 
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'warganegara_agama', 
        'pekerjaan', 'alamat_pemohon', 'nama_usaha', 'alamat_usaha', 
        'catatan_pemohon', 'status', 'catatan_penolakan', 'nomor_urut', 
        'nomor_surat', 'file_hasil_akhir', 'tanggal_selesai_proses',
    ];

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

    // ===================================================================
    // IMPLEMENTASI METODE DARI INTERFACE - TAMBAHKAN BLOK INI
    // ===================================================================

    public function getJudulNotifikasi(): string
    {
        return "Permohonan SK Usaha";
    }

    public function getPemohon(): Masyarakat
    {
        // Berdasarkan relasi Anda, pemohon adalah dari model Masyarakat
        return $this->masyarakat;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRouteTujuan(): string
    {
        // PENTING: Pastikan nama route ini benar sesuai dengan file routes/web.php Anda
        return route('petugas.permohonan-sk-usaha.show', $this->id);
    }
    // ===================================================================
    // AKHIR DARI BLOK TAMBAHAN
    // ===================================================================
}