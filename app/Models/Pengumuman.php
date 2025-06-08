<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Untuk membuat slug

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'gambar_pengumuman',
        'file_pengumuman',
        'tanggal_publikasi',
        'user_id',
        'status_publikasi',
    ];

    protected $casts = [
        'tanggal_publikasi' => 'date',
    ];

    /**
     * Relasi ke user (petugas) yang membuat pengumuman.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot a new slug before saving the model.
     * Atau Anda bisa menggunakan package seperti Eloquent Sluggable.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pengumuman) {
            if (empty($pengumuman->slug)) {
                $pengumuman->slug = Str::slug($pengumuman->judul);
                // Pastikan slug unik
                $originalSlug = $pengumuman->slug;
                $count = 1;
                while (static::whereSlug($pengumuman->slug)->exists()) {
                    $pengumuman->slug = "{$originalSlug}-" . $count++;
                }
            }
        });

        static::updating(function ($pengumuman) {
            if ($pengumuman->isDirty('judul') && empty($pengumuman->slug)) { // Hanya update slug jika judul berubah & slug kosong
                $pengumuman->slug = Str::slug($pengumuman->judul);
                $originalSlug = $pengumuman->slug;
                $count = 1;
                // Cek slug unik, kecuali untuk model itu sendiri
                while (static::whereSlug($pengumuman->slug)->where('id', '!=', $pengumuman->id)->exists()) {
                    $pengumuman->slug = "{$originalSlug}-" . $count++;
                }
            }
        });
    }

    /**
     * Scope untuk mengambil pengumuman yang dipublikasikan.
     */
    public function scopeDipublikasikan($query)
    {
        return $query->where('status_publikasi', 'dipublikasikan')
                     ->where('tanggal_publikasi', '<=', now());
    }
}
