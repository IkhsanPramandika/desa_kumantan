<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NomorSuratCounter extends Model
{
    use HasFactory;

    protected $table = 'nomor_surat_counters';
    protected $primaryKey = 'tahun';
    public $incrementing = false;
    protected $fillable = ['tahun', 'nomor_terakhir'];

    /**
     * Mengambil nomor urut berikutnya untuk tahun yang diberikan.
     * Metode ini akan mencari nomor terakhir di tahun ini, menaikkannya satu,
     * atau memulai dari 1 jika ini adalah nomor pertama di tahun ini.
     *
     * @param int $tahun
     * @return int
     */
    public static function getNextNomor(int $tahun): int
    {
        // Menggunakan DB::transaction untuk memastikan tidak ada duplikasi nomor (race condition)
        return DB::transaction(function () use ($tahun) {
            // Mengunci baris untuk tahun ini agar tidak ada proses lain yang bisa mengubahnya
            // sampai transaksi ini selesai.
            $counter = self::lockForUpdate()->firstOrCreate(
                ['tahun' => $tahun],
                ['nomor_terakhir' => 0]
            );

            // Menaikkan nomor terakhir
            $counter->nomor_terakhir++;
            $counter->save();

            return $counter->nomor_terakhir;
        });
    }
}
