<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananKKBaru extends Model
{
     protected $table = 'permohonan_kk_baru'; // Sesuaikan dengan nama tabel di database
      protected $fillable = [
        'file_kk',             // Kartu Keluarga
        'file_ktp',            // KTP Pemohon
        'surat_pengantar_rt_rw',
        'buku_nikah_akta_cerai',
        'surat_pindah_datang',
        'ijazah_terakhir',
        'catatan',
        'status',
        'file_hasil_akhir',       
        'tanggal_selesai_proses',  
        'catatan_penolakan'        
    ];
}
