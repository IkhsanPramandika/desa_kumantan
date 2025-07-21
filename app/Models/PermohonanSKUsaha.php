<?php

namespace App\Models;

use App\Interfaces\PermohonanInterface;
use App\Traits\HasNomorSurat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;




class PermohonanSKUsaha extends Model 
{
    // 2. Aktifkan kembali generator dengan baris ini
    use HasFactory, HasNomorSurat;
    

    /**
     * 3. Aktifkan kembali "saklar" ini agar generator berjalan.
     * Ganti '503' dengan kode klasifikasi yang benar jika perlu.
     */
    public string $kodeKlasifikasiSurat = '503';

    protected $table = 'permohonan_sk_usaha';
    protected $guarded = ['id'];

    protected $fillable = [
        'masyarakat_id', 'file_kk', 'file_ktp', 'nama_pemohon', 'nik_pemohon',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'warganegara_agama',
        'pekerjaan', 'alamat_pemohon','keperluan_surat', 'nama_usaha', 'alamat_usaha',
        'catatan_pemohon', 'status', 'catatan_penolakan', 
        'nomor_urut',
        'nomor_surat', 'file_hasil_akhir', 'tanggal_selesai_proses',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_selesai_proses' => 'datetime',
    ];

   public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class, 'masyarakat_id');
    }

    public function getJudulNotifikasi(): string
    {
        return 'Surat Keterangan Usaha (ID: ' . $this->id . ')';
    }

    public function getRouteTujuan(): string
    {
        return url('/admin/permohonan-sk-usaha/' . $this->id);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
