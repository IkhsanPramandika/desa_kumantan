<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananSKDomisili extends Model
{
     protected $table = 'permohonan_sk_domisili'; // Sesuaikan dengan nama tabel di database
      protected $fillable = [
        'file_kk',             // Kartu Keluarga
        'file_ktp',            // Kartu Tanda Penduduk  
        'catatan',
        'status'
    ];  
}
