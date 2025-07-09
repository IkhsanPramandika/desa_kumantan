<?php

namespace App\Http\Controllers\Petugas\Dashboard;
use App\Models\User;

use App\Models\Masyarakat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PetugasController extends Controller
{
    public function dashboard()
{
    // Screenshot Anda mengkonfirmasi nama model ini sudah benar.
    // Tidak ada perubahan di sini.
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
    ];

    $stats = [];
    foreach ($permohonanModels as $key => $modelClass) {
        if (class_exists($modelClass)) {
            $stats[$key]['total']    = $modelClass::count();
            $stats[$key]['pending']  = $modelClass::where('status', 'pending')->count();
            $stats[$key]['diterima'] = $modelClass::where('status', 'diterima')->count();
            $stats[$key]['diproses'] = $modelClass::where('status', 'diproses')->count(); // <-- BARIS BARU DITAMBAHKAN
            $stats[$key]['ditolak']  = $modelClass::where('status', 'ditolak')->count();
        } else {
            $stats[$key] = ['total' => 0, 'pending' => 0, 'diterima' => 0, 'diproses' => 0, 'ditolak' => 0];
        }
    }
    
    // Konfigurasi untuk card menu, tidak perlu diubah.
    $permohonanDetails = [
        'kkBaru'          => ['title' => 'Kartu Keluarga Baru', 'icon' => 'fas fa-id-card-alt', 'route' => 'petugas.permohonan-kk-baru.index', 'color' => 'primary'],
        'kkPerubahanData' => ['title' => 'KK Perubahan Data', 'icon' => 'fas fa-edit', 'route' => 'petugas.permohonan-kk-perubahan.index', 'color' => 'success'],
        'kkHilang'        => ['title' => 'Kartu Keluarga Hilang', 'icon' => 'fas fa-id-card', 'route' => 'petugas.permohonan-kk-hilang.index', 'color' => 'info'],
        'skKelahiran'     => ['title' => 'SK Kelahiran & Akta', 'icon' => 'fas fa-baby', 'route' => 'petugas.permohonan-sk-kelahiran.index', 'color' => 'warning'],
        'skAhliWaris'     => ['title' => 'SK Ahli Waris', 'icon' => 'fas fa-gavel', 'route' => 'petugas.permohonan-sk-ahli-waris.index', 'color' => 'danger'],
        'skPerkawinan'    => ['title' => 'Surat Pengantar Nikah', 'icon' => 'fas fa-ring', 'route' => 'petugas.permohonan-sk-perkawinan.index', 'color' => 'dark'],
        'skUsaha'         => ['title' => 'Surat Keterangan Usaha', 'icon' => 'fas fa-briefcase', 'route' => 'petugas.permohonan-sk-usaha.index', 'color' => 'secondary'],
        'skDomisili'      => ['title' => 'Surat Keterangan Domisili', 'icon' => 'fas fa-home', 'route' => 'petugas.permohonan-sk-domisili.index', 'color' => 'primary'],
        'skTidakMampu'    => ['title' => 'SK Tidak Mampu', 'icon' => 'fas fa-hand-holding-heart', 'route' => 'petugas.permohonan-sk-tidak-mampu.index', 'color' => 'info'],
    ];

    // Hitung total keseluruhan
    $overallTotalPending  = array_sum(array_column($stats, 'pending'));
    $overallTotalAccepted = array_sum(array_column($stats, 'diterima'));
    $overallTotalInProcess = array_sum(array_column($stats, 'diproses')); // <-- BARIS BARU DITAMBAHKAN
    $overallTotalRejected = array_sum(array_column($stats, 'ditolak'));

    // Hitung total pengguna
    $totalUsers = User::count();

    return view('petugas.dashboard', compact(
        'stats',
        'permohonanDetails',
        'totalUsers',
        'overallTotalPending',
        'overallTotalAccepted',
        'overallTotalInProcess', // <-- BARU, DIKIRIM KE VIEW
        'overallTotalRejected'
    ));

    
}
public function masyarakatIndex(Request $request)
    {
        // Memulai query dengan urutan terbaru
        $query = Masyarakat::latest();

        // Terapkan filter pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nomor_hp', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Terapkan filter status akun jika ada
        if ($request->filled('status_akun')) {
            $query->where('status_akun', $request->status_akun);
        }

        // Ambil data dengan pagination dan sertakan query string (filter) saat berpindah halaman
        $masyarakat = $query->paginate(10)->withQueryString();

        return view('petugas.masyarakat.index', compact('masyarakat'));
    }

    /**
     * Menampilkan detail satu akun masyarakat.
     */
    public function masyarakatShow(Masyarakat $masyarakat)
    {
        // Menggunakan Route-Model Binding, Laravel otomatis mencari user berdasarkan ID.
        // Jika tidak ditemukan, akan menampilkan halaman 404.
        return view('petugas.masyarakat.show', compact('masyarakat'));
    }

    /**
     * Mengupdate status akun (verifikasi/aktifkan, tolak, nonaktifkan).
     * Satu method untuk menangani semua aksi perubahan status.
     */
    public function updateStatus(Request $request, Masyarakat $masyarakat)
    {
        // Validasi input
        $request->validate([
            'status_akun' => 'required|in:active,rejected,inactive',
            // Catatan wajib diisi hanya jika statusnya 'rejected' atau 'inactive'
            'catatan_verifikasi' => 'required_if:status_akun,rejected,inactive|nullable|string|max:500',
        ]);

        // Update status dan catatan
        $masyarakat->status_akun = $request->status_akun;
        $masyarakat->catatan_verifikasi = $request->catatan_verifikasi;
        
        // Jika diaktifkan, hapus catatan verifikasi lama (jika ada)
        if ($request->status_akun === 'active') {
            $masyarakat->catatan_verifikasi = null;
        }
        
        $masyarakat->save();
        
        // Kirim notifikasi ke masyarakat (Sangat disarankan)
        // Anda bisa membuat class Notifikasi khusus untuk ini.
        // Notification::send($masyarakat, new AkunStatusUpdatedNotification($masyarakat));

        return redirect()->route('petugas.masyarakat.index')->with('success', 'Status akun berhasil diperbarui.');
    }

    /**
     * Menampilkan form untuk mereset password oleh petugas.
     */
    public function showResetPasswordFormByPetugas(Masyarakat $masyarakat)
    {
        return view('petugas.masyarakat.reset_password_form', compact('masyarakat'));
    }

    /**
     * Memproses reset password oleh petugas.
     */
    public function resetPasswordByPetugas(Request $request, Masyarakat $masyarakat)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $masyarakat->password = Hash::make($request->password);
        $masyarakat->save();

        return redirect()->route('petugas.masyarakat.index')->with('success', 'Password untuk akun ' . $masyarakat->nama_lengkap . ' berhasil direset.');
    }
}
