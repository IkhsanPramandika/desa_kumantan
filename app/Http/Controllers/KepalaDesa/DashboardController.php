<?php

namespace App\Http\Controllers\KepalaDesa;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk Kepala Desa.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $statistics = $this->getDashboardStatistics();
        return view('kepala_desa.dashboard', $statistics);
    }

    /**
     * Menampilkan halaman statistik detail untuk Kepala Desa.
     *
     * @return \Illuminate\View\View
     */
    public function statistics()
    {
        $statistics = $this->getDashboardStatistics();
        return view('kepala_desa.statistics', $statistics); // Asumsi ada view statistics.blade.php
    }

    /**
     * Mengambil dan memproses semua statistik yang dibutuhkan untuk dashboard
     * dari satu query gabungan yang efisien.
     *
     * @return array
     */
  private function getDashboardStatistics(): array
{
    // 1. Dapatkan semua data permohonan dalam satu kali panggilan ke database
    $allPermohonan = $this->getBasePermohonanQuery()->get();

    // 2. Lakukan kalkulasi menggunakan Laravel Collections
    $permohonanSelesaiCollection = $allPermohonan->where('status', 'selesai')->whereNotNull('tanggal_selesai_proses');
    
    // Inisialisasi data bulanan
    $monthlyData = [];
    for ($i = 1; $i <= 12; $i++) {
        $monthName = Carbon::create(null, $i, 1)->translatedFormat('F');
        $monthlyData[$monthName] = 0;
    }

    // Proses data bulanan dari koleksi
    $allPermohonan->groupBy(function ($item) {
        return Carbon::parse($item->created_at)->format('F');
    })->each(function ($group, $monthName) use (&$monthlyData) {
        $monthlyData[$monthName] = $group->count();
    });

    // Hitung rata-rata waktu proses KESELURUHAN
    $totalDuration = $permohonanSelesaiCollection->sum(function ($item) {
        if ($item->tanggal_selesai_proses && $item->created_at) {
            $start = Carbon::parse($item->created_at);
            $end = Carbon::parse($item->tanggal_selesai_proses);
            $days = $end->diffInDays($start);
            return $days === 0 ? 1 : $days;
        }
        return 0;
    });
    
    $completedCount = $permohonanSelesaiCollection->count();
    $averageProcessingTime = $completedCount > 0 ? "~" . round($totalDuration / $completedCount) . " Hari" : "N/A";

    // [KODE BARU] Hitung rata-rata waktu proses PER JENIS LAYANAN
    $waktuProsesByJenis = $permohonanSelesaiCollection
        ->groupBy('jenis_permohonan')
        ->map(function ($group) {
            // Untuk setiap grup layanan, hitung rata-rata durasinya
            $avgDuration = $group->avg(function ($item) {
                $start = Carbon::parse($item->created_at);
                $end = Carbon::parse($item->tanggal_selesai_proses);
                $days = $end->diffInDays($start);
                return $days === 0 ? 1 : $days;
            });
            // Kita bulatkan hasilnya agar rapi di grafik
            return round($avgDuration, 1);
        })
        ->sort(); // Urutkan dari yang tercepat ke yang terlama

    // 3. Kembalikan semua data dalam satu array, TERMASUK DATA BARU
    return [
        'totalPermohonan' => $allPermohonan->count(),
        'permohonanSelesai' => $completedCount,
        'permohonanPending' => $allPermohonan->where('status', 'pending')->count(),
        'permohonanByJenis' => $allPermohonan->groupBy('jenis_permohonan')->map->count()->sortDesc(),
        'permohonanBulanan' => $monthlyData,
        'rataRataProses' => $averageProcessingTime,
        'waktuProsesByJenis' => $waktuProsesByJenis, // <-- DATA BARU DITAMBAHKAN DI SINI
    ];
}    private function getBasePermohonanQuery(): Builder
    {
        $tableMap = $this->getPermohonanTableMap();
        $baseQuery = null;

        foreach ($tableMap as $tableName => $displayName) {
            // [PERBAIKAN] Memeriksa kolom 'tanggal_selesai_proses'
            $hasCompletedDate = Schema::hasColumn($tableName, 'tanggal_selesai_proses');

            // Bangun query select secara dinamis
            $selectColumns = [
                'status',
                'created_at',
                // [PERBAIKAN] Menggunakan kolom 'tanggal_selesai_proses' jika ada
                $hasCompletedDate ? 'tanggal_selesai_proses' : DB::raw('NULL as tanggal_selesai_proses'),
                DB::raw("'$displayName' as jenis_permohonan")
            ];

            $query = DB::table($tableName)->select($selectColumns);

            if ($baseQuery === null) {
                $baseQuery = $query;
            } else {
                $baseQuery->unionAll($query);
            }
        }

        return $baseQuery;
    }

    /**
     * Peta nama tabel permohonan ke nama yang akan ditampilkan (Display Name).
     *
     * @return array
     */
    private function getPermohonanTableMap(): array
    {
        return [
            'permohonan_kk_baru' => 'Permohonan KK Baru',
            'permohonan_kk_hilang' => 'Permohonan KK Hilang',
            'permohonan_kk_perubahan_data' => 'Permohonan KK Perubahan Data',
            'permohonan_lainnyas' => 'Permohonan Lainnya',
            'permohonan_sk_ahli_waris' => 'Permohonan SK Ahli Waris',
            'permohonan_sk_domisili' => 'Permohonan SK Domisili',
            'permohonan_sk_kelahiran' => 'Permohonan SK Kelahiran',
            'permohonan_sk_perkawinan' => 'Permohonan SK Perkawinan',
            'permohonan_sk_tidak_mampu' => 'Permohonan SK Tidak Mampu',
            'permohonan_sk_usaha' => 'Permohonan SK Usaha',
        ];
    }
}