<?php

namespace App\Models;

use App\Interfaces\PermohonanInterface;
use App\Traits\NomorSuratGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @property int $id
 * @property-read \App\Models\Masyarakat $masyarakat
 */
class PermohonanSKKelahiran extends Model implements PermohonanInterface
{
    use HasFactory,NomorSuratGenerator;
    protected $table = 'permohonan_sk_kelahiran';

    protected $fillable = [
        'masyarakat_id', // PENTING: Pastikan kolom ini ada di migrasi dan fillable
        'file_kk',
        'file_ktp',
        'surat_pengantar_rt_rw',
        'surat_nikah_orangtua',
        'surat_keterangan_kelahiran',
        'catatan', // Direkomendasikan ganti nama dari 'catatan'

        // Data Anak
        'nama_anak',
        'tempat_lahir_anak',
        'tanggal_lahir_anak',
        'jenis_kelamin_anak',
        'agama_anak',
        'alamat_anak',

        // Data Orang Tua
        'nama_ayah',
        'nik_ayah',
        'nama_ibu',
        'nik_ibu',
        'no_buku_nikah', // Jika diinput manual

        // Diisi oleh sistem/petugas
        'status',
        'nomor_urut',
        'nomor_surat',
        'file_hasil_akhir',
        'tanggal_selesai_proses',
        'catatan_penolakan',
    ];

    protected $casts = [
        'tanggal_lahir_anak' => 'date',
        'tanggal_selesai_proses' => 'datetime',
    ];

    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class);
    }

     // ===================================================================
    // IMPLEMENTASI METODE DARI INTERFACE
    // ===================================================================

    public function getJudulNotifikasi(): string
    {
        return "Permohonan SK Kelahiran";
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
        return route('petugas.permohonan-sk-kelahiran.show', $this->id);
    }
}