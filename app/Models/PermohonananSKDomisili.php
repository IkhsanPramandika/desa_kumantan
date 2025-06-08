<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonananSKDomisili extends Model
{
    use HasFactory;

    protected $table = 'permohonan_sk_domisili';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'masyarakat_id', // Penting untuk relasi
        'nama_pemohon_atau_lembaga',
        'nik_pemohon',
        'jenis_kelamin_pemohon',
        'tempat_lahir_pemohon',
        'tanggal_lahir_pemohon',
        'pekerjaan_pemohon',
        'alamat_lengkap_domisili',
        'rt_domisili',
        'rw_domisili',
        'dusun_domisili',
        'keperluan_domisili',
        'file_kk',
        'file_ktp',
        'file_surat_pengantar_rt_rw',
        'status',
        'catatan_pemohon', // Kolom standar untuk catatan dari masyarakat
        'catatan_penolakan',
        'nomor_urut',
        'nomor_surat',
        'file_hasil_akhir',
        'tanggal_selesai_proses',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir_pemohon' => 'date',
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
