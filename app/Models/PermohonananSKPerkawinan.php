<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananSKPerkawinan extends Model
{
      protected $table = 'permohonan_sk_perkawinan'; // Sesuaikan dengan nama tabel di database
      protected $fillable = [
        'file_kk',            
        'file_ktp_mempelai',
        'surat_nikah_orang_tua',    
        'kartu_imunisasi_catin',    
        'sertifikat_elsimil',  
        'akta_penceraian',
        'catatan',
        'status'
      ];
}
