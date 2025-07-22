<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanLainnya extends Model
{
    use HasFactory;

    protected $fillable = [
        'masyarakat_id',
        'judul_permohonan',
        'keperluan',
        'rincian_pemohon',
        'lampiran',
        'nomor_surat',
        'judul_surat_final',
        'konten_final_html',
        'file_hasil_akhir',
        'catatan_penolakan',
        'tanggal_selesai_proses',
        'status',
    ];

    protected $casts = [
        'tanggal_selesai_proses' => 'datetime',
    ];

    public function masyarakat(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Masyarakat::class, 'masyarakat_id');
    }

    // Helper untuk notifikasi (sesuaikan jika perlu)
    public function getJudulNotifikasi(): string
    {
        return 'Permohonan Surat Lainnya';
    }

    public function getId(): int
    {
        return $this->id;
    }

    // URL Tujuan untuk Notifikasi di Admin Panel
    public function getRouteTujuan(): string
    {
        return route('petugas.permohonan-lainnya.show', $this->id);
    }

}