<?php

namespace App\Traits;

use App\Models\NomorSuratCounter; // Pastikan model ini ada
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

trait NomorSuratGenerator
{
    /**
     * Metode 'boot' ini akan secara otomatis dijalankan oleh Laravel
     * untuk setiap model yang menggunakan trait ini.
     */
    protected static function bootNomorSuratGenerator()
    {
        // Mendaftarkan sebuah 'event listener' yang berjalan TEPAT SEBELUM
        // sebuah data baru disimpan ke database ('creating' event).
        static::creating(function (Model $model) {
            // Memeriksa apakah model yang bersangkutan memiliki properti 'kodeKlasifikasiSurat'.
            // Ini membuat trait menjadi fleksibel.
            if (property_exists($model, 'kodeKlasifikasiSurat')) {
                // Jika ada, panggil fungsi generator dengan kode yang sesuai.
                $model->generateNomorSurat($model->kodeKlasifikasiSurat);
            }
        });
    }

    /**
     * Generate nomor surat lengkap berdasarkan kode klasifikasi.
     * @param string $kodeKlasifikasi
     */
    public function generateNomorSurat(string $kodeKlasifikasi)
    {
        $tahun = Carbon::now()->year;
        $bulanRomawi = $this->getBulanRomawi(Carbon::now()->month);
        
        // Ambil nomor urut berikutnya dari tabel counter
        // Pastikan model NomorSuratCounter dan metodenya sudah benar.
        $nomorUrut = NomorSuratCounter::getNextNomor($tahun);

        // Mengisi properti pada model yang sedang dibuat
        $this->nomor_urut = $nomorUrut;
        $this->nomor_surat = "{$kodeKlasifikasi}/" . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT) . "/KMT/{$bulanRomawi}/{$tahun}";
    }

    /**
     * Mengubah angka bulan menjadi angka Romawi.
     */
    private function getBulanRomawi($monthNumber)
    {
        $map = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[intval($monthNumber) - 1] ?? $monthNumber;
    }
}
