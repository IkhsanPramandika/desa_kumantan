<?php
// Lokasi: app/Http/Controllers/Petugas/Dashboard/PetugasController.php

namespace App\Http\Controllers\Petugas\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Masyarakat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Collection;

class PetugasController extends Controller
{
    public function dashboard()
    {
        // Daftar semua model permohonan yang ada di sistem
        $permohonanModels = [
            'kkBaru'          => \App\Models\PermohonanKKBaru::class,
            'kkHilang'        => \App\Models\PermohonanKKHilang::class,
            'kkPerubahanData' => \App\Models\PermohonanKKPerubahanData::class,
            'skDomisili'      => \App\Models\PermohonanSKDomisili::class,
            'skKelahiran'     => \App\Models\PermohonanSKKelahiran::class,
            'skPerkawinan'    => \App\Models\PermohonanSKPerkawinan::class,
            'skTidakMampu'    => \App\Models\PermohonanSKTidakMampu::class,
            'skUsaha'         => \App\Models\PermohonanSKUsaha::class,
            'skAhliWaris'     => \App\Models\PermohonanSKAhliWaris::class,
            'lainnya'         => \App\Models\PermohonanLainnya::class, // <-- Ditambahkan
        ];

        $stats = [];
        $recentPermohonan = new Collection();

        foreach ($permohonanModels as $key => $modelClass) {
            if (class_exists($modelClass)) {
                // Menghitung statistik per jenis surat
                $stats[$key]['total']   = $modelClass::count();
                $stats[$key]['pending'] = $modelClass::where('status', 'pending')->count();
                $stats[$key]['revisi']  = $modelClass::where('status', 'membutuhkan_revisi')->count(); // <-- BARU
                $stats[$key]['diterima']= $modelClass::where('status', 'diterima')->count();
                $stats[$key]['selesai'] = $modelClass::where('status', 'selesai')->count();
                
                // Mengambil 5 permohonan terbaru dari setiap jenis untuk "Aktivitas Terbaru"
                $latest = $modelClass::with('masyarakat')->latest()->take(5)->get();
                $recentPermohonan = $recentPermohonan->merge($latest);

            } else {
                $stats[$key] = ['total' => 0, 'pending' => 0, 'revisi' => 0, 'diterima' => 0, 'selesai' => 0];
            }
        }
        
        // Mengurutkan semua permohonan yang terkumpul berdasarkan tanggal terbaru, dan ambil 5 teratas
        $recentPermohonan = $recentPermohonan->sortByDesc('created_at')->take(5);

        // Konfigurasi untuk card menu "Akses Cepat"
        $permohonanDetails = [
            'kkBaru'          => ['title' => 'KK Baru', 'icon' => 'fas fa-id-card-alt', 'route' => 'petugas.permohonan-kk-baru.index'],
            'kkPerubahanData' => ['title' => 'Perubahan Data KK', 'icon' => 'fas fa-edit', 'route' => 'petugas.permohonan-kk-perubahan.index'],
            'kkHilang'        => ['title' => 'KK Hilang', 'icon' => 'fas fa-search', 'route' => 'petugas.permohonan-kk-hilang.index'],
            'skKelahiran'     => ['title' => 'SK Kelahiran', 'icon' => 'fas fa-baby', 'route' => 'petugas.permohonan-sk-kelahiran.index'],
            'skAhliWaris'     => ['title' => 'SK Ahli Waris', 'icon' => 'fas fa-users', 'route' => 'petugas.permohonan-sk-ahli-waris.index'],
            'skPerkawinan'    => ['title' => 'SK Pengantar Nikah', 'icon' => 'fas fa-ring', 'route' => 'petugas.permohonan-sk-perkawinan.index'],
            'skUsaha'         => ['title' => 'SK Usaha', 'icon' => 'fas fa-briefcase', 'route' => 'petugas.permohonan-sk-usaha.index'],
            'skDomisili'      => ['title' => 'SK Domisili', 'icon' => 'fas fa-home', 'route' => 'petugas.permohonan-sk-domisili.index'],
            'skTidakMampu'    => ['title' => 'SK Tidak Mampu', 'icon' => 'fas fa-hand-holding-heart', 'route' => 'petugas.permohonan-sk-tidak-mampu.index'],
            'lainnya'         => ['title' => 'SK Lainnya', 'icon' => 'fas fa-file-invoice', 'route' => 'petugas.permohonan-lainnya.index'],
        ];

        // Hitung total keseluruhan untuk kartu statistik di atas
        $overallTotalPending   = array_sum(array_column($stats, 'pending'));
        $overallTotalRevisi    = array_sum(array_column($stats, 'revisi')); // <-- BARU
        $overallTotalInProcess = array_sum(array_column($stats, 'diterima'));
        $overallTotalAccepted  = array_sum(array_column($stats, 'selesai'));

        // Hitung total pengguna
        $totalUsers = Masyarakat::count();

        return view('petugas.dashboard', compact(
            'stats',
            'permohonanDetails',
            'totalUsers',
            'recentPermohonan', // <-- BARU
            'overallTotalPending',
            'overallTotalRevisi', // <-- BARU
            'overallTotalAccepted',
            'overallTotalInProcess'
        ));
    }

   public function masyarakatIndex(Request $request)
    {
        $query = Masyarakat::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nomor_hp', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_akun')) {
            $query->where('status_akun', $request->status_akun);
        }

        // [FITUR BARU] Menambahkan fitur jumlah data per halaman
        $perPage = $request->input('per_page', 10);
        $masyarakat = $query->paginate($perPage)->withQueryString();

        return view('petugas.masyarakat.index', compact('masyarakat'));
    }

    public function masyarakatShow(Masyarakat $masyarakat)
    {
        return view('petugas.masyarakat.show', compact('masyarakat'));
    }

    public function updateStatus(Request $request, Masyarakat $masyarakat)
    {
        $request->validate([
            'status_akun' => 'required|in:active,rejected,inactive',
            'catatan_verifikasi' => 'required_if:status_akun,rejected,inactive|nullable|string|max:500',
        ]);

        $masyarakat->status_akun = $request->status_akun;
        $masyarakat->catatan_verifikasi = $request->catatan_verifikasi;
        
        if ($request->status_akun === 'active') {
            $masyarakat->catatan_verifikasi = null;
        }
        
        $masyarakat->save();
        
        // Anda bisa menambahkan notifikasi di sini jika diperlukan
        // Notification::send($masyarakat, new AkunStatusUpdatedNotification($masyarakat));

        // [PERBAIKAN UX] Kembali ke halaman detail setelah update
        return redirect()->route('petugas.masyarakat.show', $masyarakat->id)->with('success', 'Status akun berhasil diperbarui.');
    }

    public function showResetPasswordFormByPetugas(Masyarakat $masyarakat)
    {
        return view('petugas.masyarakat.reset_password_form', compact('masyarakat'));
    }

    public function resetPasswordByPetugas(Request $request, Masyarakat $masyarakat)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $masyarakat->password = Hash::make($request->password);
        $masyarakat->save();

        // [PERBAIKAN UX] Kembali ke halaman detail setelah update
        return redirect()->route('petugas.masyarakat.show', $masyarakat->id)->with('success', 'Password untuk akun ' . $masyarakat->nama_lengkap . ' berhasil direset.');
    }
}