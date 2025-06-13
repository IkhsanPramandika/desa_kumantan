<?php

namespace App\Http\Controllers\Petugas\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Menandai notifikasi sebagai sudah dibaca dan redirect ke URL terkait
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        // Tandai sebagai sudah dibaca jika masih unread
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        // Redirect ke URL tujuan yang ada di dalam notifikasi
        return redirect($notification->data['url']);
    }

    /**
     * Menampilkan halaman semua notifikasi
     */
    public function index(Request $request)
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
}
