<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonananSKPerkawinan extends Model
{
    use HasFactory;

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
        'tanggal_akad_nikah',
        'tempat_akad_nikah',
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
}
