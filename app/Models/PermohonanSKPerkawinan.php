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
class PermohonanSKPerkawinan extends Model implements PermohonanInterface
{
    use HasFactory, HasNomorSurat;

    protected $table = 'permohonan_sk_perkawinan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'masyarakat_id',
        'file_kk',
        'file_ktp_mempelai',
        'surat_nikah_orang_tua',
        'kartu_imunisasi_catin',
        'sertifikat_elsimil',
        'akta_penceraian',
        'catatan_pemohon', // Disesuaikan untuk konsistensi
        'status',
        'catatan_penolakan',
        
        // Data yang diinput oleh petugas
        'pemohon_surat',
        'nama_pria',
        'nik_pria',
        'tempat_lahir_pria',
        'tanggal_lahir_pria',
        'alamat_pria',
        'nama_wanita',
        'nik_wanita',
        'tempat_lahir_wanita',
        'tanggal_lahir_wanita',
        'alamat_wanita',
        'nomor_surat',
        'nomor_urut',
        'tanggal_selesai_proses',
        'file_hasil_akhir',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir_pria' => 'date',
        'tanggal_lahir_wanita' => 'date',
        'tanggal_akad_nikah' => 'date',
        'tanggal_selesai_proses' => 'datetime',
    ];

    /**
     * Get the masyarakat that owns the permohonan.
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
        return "Permohonan SK Pengantar Nikah";
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
        return route('petugas.permohonan-sk-perkawinan.show', $this->id);
    }
}
