<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $fillable = ['judul', 'isi', 'user_id'];

    // Relasi ke petugas desa
    public function user() {
        return $this->belongsTo(User::class);
    }
}
