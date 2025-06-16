<?php

namespace App\Traits;

use App\Models\NomorSuratCounter; // Model yang akan kita buat
use Carbon\Carbon;

trait NomorSuratGenerator
{
    /**
     * Generate nomor surat lengkap berdasarkan kode klasifikasi.
     * @param string $kodeKlasifikasi
     */
    public function generateNomorSurat(string $kodeKlasifikasi)
    {
        $tahun = Carbon::now()->year;
        $bulanRomawi = $this->getBulanRomawi(Carbon::now()->month);
        
        // Ambil nomor urut berikutnya dari tabel counter
        $nomorUrut = NomorSuratCounter::getNextNomor($tahun);

        $this->nomor_urut = $nomorUrut;
        $this->nomor_surat = "{$kodeKlasifikasi}/" . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT) . "/KMT/{$bulanRomawi}/{$tahun}";
    }

    private function getBulanRomawi($monthNumber)
    {
        $map = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[intval($monthNumber) - 1] ?? $monthNumber;
    }
}
