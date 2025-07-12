<?php

namespace App\Models;

use App\Interfaces\PermohonanInterface;
use App\Traits\NomorSuratGenerator; // 1. Pastikan ini di-include kembali
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSKUsaha extends Model implements PermohonanInterface
{
    // 2. Aktifkan kembali generator dengan baris ini
    use HasFactory, NomorSuratGenerator;

    /**
     * 3. Aktifkan kembali "saklar" ini agar generator berjalan.
     * Ganti '503' dengan kode klasifikasi yang benar jika perlu.
     */
    public string $kodeKlasifikasiSurat = '503';

    protected $table = 'permohonan_sk_usaha';

    protected $fillable = [
        'masyarakat_id', 'file_kk', 'file_ktp', 'nama_pemohon', 'nik_pemohon',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'warganegara_agama',
        'pekerjaan', 'alamat_pemohon', 'nama_usaha', 'alamat_usaha',
        'catatan_pemohon', 'status', 'catatan_penolakan',
        'nomor_urut',
        'nomor_surat', 'file_hasil_akhir', 'tanggal_selesai_proses',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_selesai_proses' => 'datetime',
    ];

    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class);
    }

    public function getJudulNotifikasi(): string
    {
        return "Permohonan SK Usaha";
    }

    public function getPemohon(): \App\Models\Masyarakat
    {
        return $this->masyarakat;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRouteTujuan(): string
    {
        return route('petugas.permohonan-sk-usaha.show', $this->id);
    }
}
