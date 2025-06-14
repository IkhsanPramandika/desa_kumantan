<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Memeriksa notifikasi baru untuk pengguna yang sedang login.
     * Fungsi ini sekarang akan dipanggil dari route web, bukan api.
     */
    public function check(Request $request)
    {
        // Menggunakan Auth::user() yang bekerja dengan baik untuk sesi web
        $user = Auth::user();

        if (!$user) {
            // Seharusnya tidak terjadi jika middleware 'auth' di web.php aktif
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $unreadCount = $user->unreadNotifications()->count();

        $notifications = $user->unreadNotifications()->take(5)->get()->map(function ($notif) {
            // Pastikan route 'petugas.notifikasi.read' ada di web.php
            return [
                'id' => $notif->id,
                'pesan' => Str::limit($notif->data['pesan'] ?? 'Notifikasi baru.', 40),
                'waktu' => \Carbon\Carbon::parse($notif->created_at)->diffForHumans(),
                'url' => route('petugas.notifikasi.read', $notif->id)
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Menampilkan halaman semua notifikasi (Untuk Web)
     */
    public function index()
  {
        // Opsi jenis surat
        $jenisSuratOptions = [
            'KK Baru',
            'KK Hilang',
            'Perubahan Data KK',
            'SK Ahli Waris',
            'SK Kelahiran',
            'SK Domisili',
            'SK Perkawinan',
            'SK Tidak Mampu',
            'SK Usaha'
        ];

        // Ambil semua notifikasi user yang sedang login, bisa ditambahkan filter jika diperlukan
        $semuaNotifikasi = auth()->user()->notifications()->paginate(10);

        return view('notifikasi.index', [
            'semuaNotifikasi' => $semuaNotifikasi,
            'jenisSuratOptions' => $jenisSuratOptions,
        ]);
    }


    /**
     * Menandai notifikasi sebagai sudah dibaca (Untuk Web)
     */
   public function markAsRead($id)
    {
        // 1. Temukan notifikasi berdasarkan ID yang diklik,
        //    pastikan notifikasi ini milik user yang sedang login.
        $notification = Auth::user()->notifications()->findOrFail($id);

        // 2. Jika notifikasi ini belum dibaca (untuk efisiensi),
        //    maka tandai sebagai sudah dibaca.
        if ($notification->unread()) {
            $notification->markAsRead(); // Ini akan mengisi kolom 'read_at' di database
        }

        // 3. Arahkan pengguna ke URL sebenarnya dari permohonan tersebut,
        //    yang sudah kita simpan di dalam data notifikasi.
        return redirect($notification->data['url'] ?? route('petugas.dashboard'));
    }
}
