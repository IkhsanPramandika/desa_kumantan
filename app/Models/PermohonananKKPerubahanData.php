<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananKKPerubahanData extends Model
{
    protected $table = 'permohonan_kk_perubahan_data'; // Sesuaikan dengan nama tabel di database
      protected $fillable = [
        'file_kk',            
        'file_ktp',
        'surat_pengantar_rt_rw',    
        'surat_keterangan_pendukung',
        'catatan',
        'status',
        'file_hasil_akhir',        
        'tanggal_selesai_proses',  
        'catatan_penolakan'        
      ];        
}
