<?php

namespace App\Http\Controllers\Petugas\Dashboard;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
// Tambahkan semua model permohonan Anda di sini
use App\Models\PermohonanKKBaru;
use App\Models\PermohonanKKHilang;
use App\Models\PermohonanKKPerubahanData;
use App\Models\PermohonanSKAhliWaris;
use App\Models\PermohonanSKKelahiran;
use App\Models\PermohonanSKDomisili;
use App\Models\PermohonanSKPerkawinan;
use App\Models\PermohonanSKTidakMampu;
use App\Models\PermohonanSKUsaha;

use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        return view('petugas.dashboard'); // Pastikan ini sesuai dengan nama file blade yang kamu inginkan
    }

    public function getNotifikasiBaru(): JsonResponse
    {
        $notifikasi = [];

        // Daftar konfigurasi untuk setiap jenis permohonan
        $permohonanTypes = [
            PermohonanKKBaru::class => ['jenis_surat' => 'KK Baru', 'nama_pemohon_field' => 'nama_kepala_keluarga', 'icon' => 'fas fa-id-card text-white', 'bg_color' => 'bg-success', 'route_name' => 'petugas.permohonan-kk-baru.show'],
            PermohonanKKHilang::class => ['jenis_surat' => 'KK Hilang', 'nama_pemohon_field' => 'nama_pemohon', 'icon' => 'fas fa-search-minus text-white', 'bg_color' => 'bg-warning', 'route_name' => 'petugas.permohonan-kk-hilang.show'],
            PermohonanKKPerubahanData::class => ['jenis_surat' => 'Perubahan Data KK', 'nama_pemohon_field' => 'nama_kepala_keluarga', 'icon' => 'fas fa-edit text-white', 'bg_color' => 'bg-info', 'route_name' => 'petugas.permohonan-kk-perubahan.show'],
            PermohonanSKAhliWaris::class => ['jenis_surat' => 'SK Ahli Waris', 'nama_pemohon_field' => 'nama_pemohon', 'icon' => 'fas fa-users text-white', 'bg_color' => 'bg-secondary', 'route_name' => 'petugas.permohonan-sk-ahli-waris.show'],
            PermohonanSKDomisili::class => ['jenis_surat' => 'SK Domisili', 'nama_pemohon_field' => 'nama_pemohon_atau_lembaga', 'icon' => 'fas fa-home text-white', 'bg_color' => 'bg-primary', 'route_name' => 'petugas.permohonan-sk-domisili.show'],
            PermohonanSKKelahiran::class => ['jenis_surat' => 'SK Kelahiran', 'nama_pemohon_field' => 'nama_bayi', 'icon' => 'fas fa-baby text-white', 'bg_color' => 'bg-calm-4', 'route_name' => 'petugas.permohonan-sk-kelahiran.show'],
            PermohonanSKPerkawinan::class => ['jenis_surat' => 'SK Perkawinan', 'nama_pemohon_field' => 'nama_calon_suami', 'icon' => 'fas fa-ring text-white', 'bg_color' => 'bg-calm-6', 'route_name' => 'petugas.permohonan-sk-perkawinan.show'],
            PermohonanSKTidakMampu::class => ['jenis_surat' => 'SK Tidak Mampu', 'nama_pemohon_field' => 'nama_pemohon', 'icon' => 'fas fa-hand-holding-heart text-white', 'bg_color' => 'bg-calm-9', 'route_name' => 'petugas.permohonan-sk-tidak-mampu.show'],
            PermohonanSKUsaha::class => ['jenis_surat' => 'SK Usaha', 'nama_pemohon_field' => 'nama_pemohon', 'icon' => 'fas fa-briefcase text-white', 'bg_color' => 'bg-calm-7', 'route_name' => 'petugas.permohonan-sk-usaha.show'],
        ];

        foreach ($permohonanTypes as $modelClass => $config) {
            // Ambil semua permohonan dengan status 'pending' yang belum dinotifikasi
            $items = $modelClass::where('telah_dinotifikasi', false)
                                ->where('status', 'pending')->latest()->get();

            foreach ($items as $item) {
                $notifikasi[] = [
                    'jenis_surat' => $config['jenis_surat'],
                    'nama_pemohon' => $item->{$config['nama_pemohon_field']},
                    'waktu' => $item->created_at->diffForHumans(),
                    'icon' => $config['icon'],
                    'bg_color' => $config['bg_color'],
                    'url' => route($config['route_name'], $item->id),
                ];
                // Tandai sudah dinotifikasi agar tidak muncul lagi di panggilan berikutnya
                $item->telah_dinotifikasi = true;
                $item->save();
            }
        }

        // Kembalikan data sebagai JSON, bahkan jika kosong
        return response()->json($notifikasi);
    }
    public function dashboard()
{
    // Ambil data notifikasi yang belum dibaca untuk petugas yang sedang login
    $notifikasiBelumDibaca = auth()->user()->unreadNotifications;

    return view('petugas.dashboard', [
        'notifikasi' => $notifikasiBelumDibaca
    ]);
}

}
