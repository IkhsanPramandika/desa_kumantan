<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermohonananSKAhliWaris extends Model
{
    use HasFactory;

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
}
