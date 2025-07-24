<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

use App\Models\PermohonanKKBaru;
use App\Models\PermohonanKKHilang;
use App\Models\PermohonanKKPerubahanData;
use App\Models\PermohonanSKAhliWaris;
use App\Models\PermohonanSKDomisili;
use App\Models\PermohonanSKKelahiran;
use App\Models\PermohonanSKPerkawinan;
use App\Models\PermohonanSKTidakMampu;
use App\Models\PermohonanSKUsaha;
use App\Models\PermohonanLainnya;

class RiwayatPermohonanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $permohonanTypes = [
            'permohonan-kk-baru' => ['model' => PermohonanKKBaru::class, 'nama' => 'Permohonan KK Baru'],
            'permohonan-kk-hilang' => ['model' => PermohonanKKHilang::class, 'nama' => 'Permohonan KK Hilang'],
            'permohonan-kk-perubahan-data' => ['model' => PermohonanKKPerubahanData::class, 'nama' => 'Perubahan Data KK'],
            'permohonan-sk-ahli-waris' => ['model' => PermohonanSKAhliWaris::class, 'nama' => 'SK Ahli Waris'],
            'permohonan-sk-domisili' => ['model' => PermohonanSKDomisili::class, 'nama' => 'SK Domisili'],
            'permohonan-sk-kelahiran' => ['model' => PermohonanSKKelahiran::class, 'nama' => 'SK Kelahiran'],
            'permohonan-sk-perkawinan' => ['model' => PermohonanSKPerkawinan::class, 'nama' => 'SK Perkawinan'],
            'permohonan-sk-tidak-mampu' => ['model' => PermohonanSKTidakMampu::class, 'nama' => 'SK Tidak Mampu'],
            'permohonan-sk-usaha' => ['model' => PermohonanSKUsaha::class, 'nama' => 'SK Usaha'],
            'permohonan-sk-lainnya' => ['model' => PermohonanLainnya::class, 'nama' => 'SK Lainnya (Khusus)'],
        ];

        $allPermohonan = new Collection();

        foreach ($permohonanTypes as $slug => $details) {
            $modelClass = $details['model'];
            $permohonan = $modelClass::where('masyarakat_id', $user->id)->get();

            $permohonan->transform(function ($item) use ($slug, $details) {
                $item->jenis_surat = $details['nama'];
                $item->jenis_surat_slug = $slug;
                return $item;
            });

            $allPermohonan = $allPermohonan->merge($permohonan);
        }

        $sorted = $allPermohonan->sortByDesc('created_at');

        $formatted = $sorted->map(function ($item) use ($user) {
            $tanggalPengajuan = Carbon::parse($item->created_at);
            
            $data = $item->toArray();
            $data['jenis_surat'] = $item->jenis_surat;
            $data['jenis_surat_slug'] = $item->jenis_surat_slug;
            $data['tanggal'] = $tanggalPengajuan->isoFormat('D MMMM YYYY, HH:mm');
            $data['estimasi_selesai'] = $tanggalPengajuan->addWeekdays(1)->isoFormat('D MMMM YYYY');
            $data['nama_pemohon'] = $item->nama_pemohon ?? $user->nama_lengkap;

            return $data;
        });

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $formatted->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($formatted), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return response()->json($paginatedItems);
    }

   public function show(Request $request, $jenis_surat_slug, $id)
{
    $user = $request->user();

    // [1. UBAH STRUKTUR MAP INI]
    // Samakan strukturnya seperti di method index(), tambahkan key 'nama'
    $modelMap = [
        'permohonan-kk-baru'          => ['model' => PermohonanKKBaru::class, 'nama' => 'Permohonan KK Baru'],
        'permohonan-kk-hilang'        => ['model' => PermohonanKKHilang::class, 'nama' => 'Permohonan KK Hilang'],
        'permohonan-kk-perubahan-data'=> ['model' => PermohonanKKPerubahanData::class, 'nama' => 'Perubahan Data KK'],
        'permohonan-sk-ahli-waris'    => ['model' => PermohonanSKAhliWaris::class, 'nama' => 'SK Ahli Waris'],
        'permohonan-sk-domisili'      => ['model' => PermohonanSKDomisili::class, 'nama' => 'SK Domisili'],
        'permohonan-sk-kelahiran'     => ['model' => PermohonanSKKelahiran::class, 'nama' => 'SK Kelahiran'],
        'permohonan-sk-perkawinan'    => ['model' => PermohonanSKPerkawinan::class, 'nama' => 'SK Perkawinan'],
        'permohonan-sk-tidak-mampu'   => ['model' => PermohonanSKTidakMampu::class, 'nama' => 'SK Tidak Mampu'],
        'permohonan-sk-usaha'         => ['model' => PermohonanSKUsaha::class, 'nama' => 'SK Usaha'],
        'permohonan-sk-lainnya'       => ['model' => PermohonanLainnya::class, 'nama' => 'SK Lainnya (Khusus)'],
    ];

    if (!isset($modelMap[$jenis_surat_slug])) {
        return response()->json(['message' => 'Jenis permohonan tidak valid.'], 404);
    }

    // [2. AMBIL MODEL DARI MAP]
    $modelClass = $modelMap[$jenis_surat_slug]['model'];
    
    $permohonan = $modelClass::with('masyarakat')->find($id);

    if (!$permohonan || $permohonan->masyarakat_id !== $user->id) {
        return response()->json(['message' => 'Permohonan tidak ditemukan.'], 404);
    }
    
    // [3. AMBIL NAMA SURAT DARI MAP, BUKAN NAMA MODEL]
    $permohonan->jenis_surat = $modelMap[$jenis_surat_slug]['nama'];

    return response()->json($permohonan);
}
}
