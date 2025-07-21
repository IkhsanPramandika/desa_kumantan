<?php

namespace App\Traits;

use App\Models\NomorSuratCounter;
use Carbon\Carbon;

trait HasNomorSurat
{
    /**
     * Generate nomor surat lengkap berdasarkan kode klasifikasi.
     * Fungsi ini dipanggil secara manual dari Controller.
     *
     * @param string $kodeKlasifikasi
     */
    public function generateNomorSurat(string $kodeKlasifikasi)
    {
        $tahun = Carbon::now()->year;
        $bulanAngka = $this->getBulanAngka(Carbon::now()->month);
        $kodeDesa = 'Desa Kumantan'; // Anda bisa ganti atau ambil dari setting

        // Ambil nomor urut berikutnya dari tabel counter menggunakan metode canggih Anda
        $nomorUrut = NomorSuratCounter::getNextNomor($tahun);

        // Format nomor urut (misal: 001, 043, 123)
        $nomorUrutFormatted = str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
        
        // Buat nomor surat lengkap dan simpan ke properti 'nomor_surat' model ini
        // Tidak ada lagi penyimpanan ke 'nomor_urut'
        $this->nomor_surat = "{$kodeKlasifikasi}/{$nomorUrutFormatted}/{$kodeDesa}/{$bulanAngka}/{$tahun}";
    }

    /**
     * Mengubah angka bulan menjadi format dua digit (01, 02, ..., 12).
     */
    private function getBulanAngka($monthNumber)
    {
        return str_pad($monthNumber, 2, '0', STR_PAD_LEFT);
    }
}
