<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Pastikan ini ada

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

        $user = $request->user();

        if ($user) {
            try {
                $user->update(['fcm_token' => $request->fcm_token]);
                Log::info('[NotificationController] FCM token berhasil diperbarui untuk user ID: ' . $user->id . ' (Tabel: ' . $user->getTable() . ')');
                return response()->json(['message' => 'FCM token updated successfully.']);
            } catch (\Exception $e) {
                Log::error('[NotificationController] Gagal memperbarui FCM token untuk user ID: ' . $user->id . ' (Tabel: ' . $user->getTable() . '): ' . $e->getMessage());
                return response()->json(['message' => 'Failed to update FCM token.', 'error' => $e->getMessage()], 500);
            }
        } else {
            Log::warning('[NotificationController] Percobaan update FCM token oleh user tidak terautentikasi.');
            return response()->json(['message' => 'User not authenticated.'], 401);
        }
    }


}