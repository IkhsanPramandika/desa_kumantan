<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mengambil daftar notifikasi untuk pengguna API (Mobile App).
     */
    public function index(Request $request)
    {
        $notifications = $request->user() // Menggunakan user dari token API (Sanctum/Passport)
                                 ->notifications()
                                 ->paginate(15); // Mobile biasanya menggunakan "infinite scroll"

        return response()->json($notifications);
    }

    /**
     * Menandai SEMUA notifikasi sebagai sudah dibaca untuk pengguna API.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Semua notifikasi ditandai sebagai sudah dibaca.']);
    }

    /**
     * Menandai SATU notifikasi sebagai sudah dibaca untuk pengguna API.
     * TIDAK melakukan redirect, hanya mengembalikan status.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notifikasi ditandai sebagai sudah dibaca.']);
        }
        return response()->json(['message' => 'Notifikasi tidak ditemukan.'], 404);
    }

    public function updateFcmToken(Request $request)
        {
            $request->validate(['fcm_token' => 'required|string']);
            $request->user()->update(['fcm_token' => $request->fcm_token]);
            return response()->json(['message' => 'FCM token updated successfully.']);
        }

}