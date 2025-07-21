<?php

namespace App\Models;

use App\Interfaces\PermohonanInterface;
use App\Traits\HasNomorSurat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @property int $id
 * @property-read \App\Models\Masyarakat $masyarakat
 */
class PermohonanSKAhliWaris extends Model implements PermohonanInterface
{
    use HasFactory,HasNomorSurat;

    protected $table = 'permohonan_sk_ahli_waris';

    protected $fillable = [
        'masyarakat_id', // Pastikan kolom ini ada di database
        'file_ktp_pemohon',
        'file_kk_pemohon',
        'file_ktp_ahli_waris',
        'file_kk_ahli_waris',
        'surat_keterangan_kematian',
        'surat_pengantar_rt_rw',
        'catatan_pemohon', // Nama kolom disesuaikan untuk konsistensi
        'status',
        'catatan_penolakan',
        'nama_pewaris',
        'nik_pewaris',
        'tempat_lahir_pewaris',
        'tanggal_lahir_pewaris',
        'tanggal_meninggal_pewaris',
        'alamat_pewaris',
        'daftar_ahli_waris',
        'nomor_surat',
        'file_hasil_akhir',
        'tanggal_selesai_proses',
        'nomor_urut',

        
    ];

    

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_lahir_pewaris' => 'date',
        'tanggal_meninggal_pewaris' => 'date',
        'tanggal_selesai_proses' => 'datetime',
        'daftar_ahli_waris' => 'array',
    ];

    /**
     * Relasi ke model Masyarakat.
     * PENTING: Tambahkan ini.
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
        return "Permohonan SK Ahli Waris";
    }

    public function getPemohon(): \App\Models\Masyarakat
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
        return route('petugas.permohonan-sk-ahli-waris.show', $this->id);
    }
}
