<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananSKAhliWaris extends Model
{
     protected $table = 'permohonan_sk_ahli_waris'; // Sesuaikan dengan nama tabel di database
      protected $fillable = [
        'file_kk',             // Kartu Keluarga
        'file_ktp',            // Kartu Tanda Penduduk  
        'file_ktp_ahli_waris',
        'surat_pengantar_rt_rw',    
        'surat_kematian_almarhum',
        'catatan',
        'status'
    ];  
}
