<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonananSKKelahiran extends Model
{
     protected $table = 'permohonan_sk_kelahiran'; // Sesuaikan dengan nama tabel di database
      protected $fillable = [
        'file_kk',            
        'file_ktp',
        'surat_pengantar_rt_rw',    
        'surat_nikah_orangtua',
        'surat_keterangan_kelahiran',
        'catatan',
        'status',
        // Kolom baru untuk data anak/orang tua
        'nama_anak',
        'tempat_lahir_anak',
        'tanggal_lahir_anak',
        'jenis_kelamin_anak',
        'agama_anak',
        'alamat_anak',
        'nama_ayah',
        'nik_ayah',
        'nama_ibu',
        'nik_ibu',
        'no_buku_nikah',
        // Kolom baru untuk proses petugas
        'file_hasil_akhir',
        'tanggal_selesai_proses',
        'catatan_penolakan'
      ]; 
}
