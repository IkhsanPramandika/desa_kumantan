<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananKKHilang extends Model
{
      protected $table = 'permohonan_kk_hilang'; // Sesuaikan dengan nama tabel di database
      protected $fillable = [
        'surat_pengantar_rt_rw',    
        'surat_keterangan_hilang_kepolisian',
        'catatan',
        'status',
        'file_hasil_akhir',        // <-- Tambahkan ini
        'tanggal_selesai_proses',  // <-- Tambahkan ini
        'catatan_penolakan'        // <-- Tambahkan ini
    ];  
}
