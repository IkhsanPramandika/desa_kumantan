<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananSKTidakMampu extends Model
{
    protected $table = 'permohonan_sk_tidak_mampu'; // Sesuaikan dengan nama tabel di database
    protected $fillable = [
        'file_kk',            
        'file_ktp',
        'catatan',
        'status'
      ];
}
