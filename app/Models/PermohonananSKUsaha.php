<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananSKUsaha extends Model
{
     protected $table = 'permohonan_sk_usaha'; // Sesuaikan dengan nama tabel di database
    protected $fillable = [
        'file_kk',            
        'file_ktp',
        'nama_usaha',
        'alamat_usaha', 
        'catatan',
        'status'
      ];
}
