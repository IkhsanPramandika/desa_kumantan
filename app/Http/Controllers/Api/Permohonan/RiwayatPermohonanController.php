<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Jangan lupa import Carbon

// Import semua model permohonan Anda
use App\Models\PermohonanKKBaru;
use App\Models\PermohonanKKHilang;
use App\Models\PermohonanKKPerubahanData;
use App\Models\PermohonanSKAhliWaris;
use App\Models\PermohonanSKDomisili;
use App\Models\PermohonanSKKelahiran;
use App\Models\PermohonanSKPerkawinan;
use App\Models\PermohonanSKTidakMampu;
use App\Models\PermohonanSKUsaha;

class RiwayatPermohonanController extends Controller
{
    /**
     * Mengambil, menggabungkan, dan mengurutkan semua riwayat permohonan
     * dari semua jenis surat untuk pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();


        // Daftar semua model dan nama jenis suratnya untuk diseragamkan
        $permohonanTypes = [
            PermohonanKKBaru::class => 'Permohonan KK Baru',
            PermohonanKKHilang::class => 'Permohonan KK Hilang',
            PermohonanKKPerubahanData::class => 'Perubahan Data KK',
            PermohonanSKAhliWaris::class => 'SK Ahli Waris',
            PermohonanSKDomisili::class => 'SK Domisili',
            PermohonanSKKelahiran::class => 'SK Kelahiran',
            PermohonanSKPerkawinan::class => 'SK Perkawinan',
            PermohonanSKTidakMampu::class => 'SK Tidak Mampu',
            PermohonanSKUsaha::class => 'SK Usaha',
        ];

        $baseQuery = null;  

        foreach ($permohonanTypes as $modelClass => $jenisSurat) {
            $model = new $modelClass();
            $modelTable = $model->getTable(); // Mengambil nama tabel secara dinamis

            // [PERBAIKAN] Menambahkan JOIN ke tabel masyarakat
            $query = $modelClass::query()
                ->join('masyarakat', "{$modelTable}.masyarakat_id", '=', 'masyarakat.id')
                ->select(
                    "{$modelTable}.id",
                    DB::raw("'$jenisSurat' as jenis_surat"),
                    "{$modelTable}.created_at as tanggal",
                    "{$modelTable}.status",
                    'masyarakat.nama_lengkap as nama_pemohon' // <-- Menambahkan nama pemohon
                )
                ->where("{$modelTable}.masyarakat_id", $user->id);

            if ($baseQuery === null) {
                $baseQuery = $query;
            } else {
                $baseQuery->unionAll($query);
            }
        }

        if ($baseQuery === null) {
            return response()->json(['data' => []]);
        }

        $riwayat = $baseQuery
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        $riwayat->getCollection()->transform(function ($item) {
        $tanggalPengajuan = Carbon::parse($item->tanggal);

        // [PERUBAHAN] Menambahkan data baru ke response
        $item->tanggal = $tanggalPengajuan->isoFormat('D MMMM YYYY, HH:mm'); // Format tanggal sekarang termasuk jam
        $item->estimasi_selesai = $tanggalPengajuan->addWeekdays(1)->isoFormat('D MMMM YYYY'); // Tambah 3 hari kerja sebagai estimasi

        return $item;
        });

        return response()->json($riwayat);

        
    }
    public function show(Request $request, $jenis_surat_slug, $id)
{
    $user = $request->user();

    // Mapping dari slug URL ke nama Model Class yang sebenarnya
    $modelMap = [
        'permohonan-kk-baru' => PermohonanKKBaru::class,
        'perubahan-data-kk' => PermohonanKKPerubahanData::class,
        'permohonan-kk-hilang' =>PermohonanKKHilang::class,
        'sk-ahli-waris' => PermohonanSKAhliWaris::class,
        'sk-kelahiran' =>PermohonanSKKelahiran::class,
        'sk-domisili' => PermohonanSKDomisili::class,
        'sk-perkawinan' =>PermohonanSKPerkawinan::class,
        'sk-tidak-mampu' =>PermohonanSKTidakMampu::class,
        'sk-usaha' => PermohonanSKUsaha::class,
    ];

    // Cek apakah jenis surat valid
    if (!isset($modelMap[$jenis_surat_slug])) {
        return response()->json(['message' => 'Jenis permohonan tidak valid.'], 404);
    }

    $modelClass = $modelMap[$jenis_surat_slug];
    
    // Ambil data permohonan lengkap dengan data masyarakat (pemohon)
    $permohonan = $modelClass::with('masyarakat')->find($id);

    // Validasi: Cek apakah permohonan ada dan milik user yang sedang login
    if (!$permohonan || $permohonan->masyarakat_id !== $user->id) {
        return response()->json(['message' => 'Permohonan tidak ditemukan.'], 404);
    }

    // Jika Anda punya API Resource, itu lebih baik. Untuk sekarang, kita kirim langsung.
    return response()->json($permohonan);
}
}