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
    public $timestamps = false;
    protected $fillable = ['tahun', 'nomor_terakhir'];

    /**
     * Dapatkan nomor urut berikutnya secara aman (transactional).
     * @param int $tahun
     * @return int
     */
    public static function getNextNomor(int $tahun): int
    {
        return DB::transaction(function () use ($tahun) {
            $counter = self::lockForUpdate()->firstOrCreate(
                ['tahun' => $tahun],
                ['nomor_terakhir' => 0]
            );

            $counter->nomor_terakhir++;
            $counter->save();

            return $counter->nomor_terakhir;
        });
    }
}
