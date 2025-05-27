<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PengajuanSurat extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_layanan',
        'data_tambahan',
        'status',
    ];

    protected $casts = [
        'data_tambahan' => 'array',
    ];

    protected $table = 'pengajuan_surat';  

    public const LAYANAN = [
        'kk_baru' => 'Kartu Keluarga Baru',
        'kk_perubahan' => 'Perubahan Data KK',
        'kk_hilang' => 'KK Hilang',
        'surat_kelahiran' => 'Surat Keterangan Kelahiran',
        'surat_ahli_waris' => 'Surat Ahli Waris',
        'surat_nikah' => 'Surat Pengantar Nikah',
        'surat_usaha' => 'Surat Keterangan Usaha',
        'surat_domisili' => 'Surat Domisili',
        'surat_tidak_mampu' => 'Surat Keterangan Tidak Mampu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
