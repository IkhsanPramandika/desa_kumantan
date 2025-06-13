<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSKTidakMampu extends Model 
{
    use HasFactory;

    protected $table = 'permohonan_sk_tidak_mampu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'masyarakat_id', // Penting untuk relasi
        'nama_pemohon',
        'nik_pemohon',
        'tempat_lahir_pemohon',
        'tanggal_lahir_pemohon',
        'jenis_kelamin_pemohon',
        'agama_pemohon',
        'kewarganegaraan_pemohon',
        'pekerjaan_pemohon',
        'alamat_pemohon',
        'nama_terkait',
        'nik_terkait',
        'tempat_lahir_terkait',
        'tanggal_lahir_terkait',
        'jenis_kelamin_terkait',
        'agama_terkait',
        'kewarganegaraan_terkait',
        'pekerjaan_atau_sekolah_terkait',
        'alamat_terkait',
        'keperluan_surat',
        'file_kk',
        'file_ktp',
        'file_pendukung_lain',
        'catatan_pemohon',
        'status',
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
        'tanggal_lahir_terkait' => 'date',
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
