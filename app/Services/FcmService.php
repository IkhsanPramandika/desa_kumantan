<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging; // Import kontrak Messaging
use Kreait\Firebase\Messaging\CloudMessage; // Import CloudMessage
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Mengirim notifikasi FCM ke perangkat.
     *
     * @param string $deviceToken Token FCM perangkat tujuan.
     * @param string $title Judul notifikasi.
     * @param string $body Isi notifikasi.
     * @param array $data Data kustom yang akan disertakan dalam notifikasi.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function sendNotification(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        try {
            $message = CloudMessage::new()
                ->withChangedTarget('token', $deviceToken) // Menggunakan withChangedTarget
                ->withNotification(\Kreait\Firebase\Messaging\Notification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);

            Log::info("FCM Notification sent successfully to token: {$deviceToken}");
            return true;
        } catch (\Throwable $e) { // Menggunakan Throwable untuk menangkap semua error dan exception
            Log::error("Failed to send FCM Notification to token {$deviceToken}: " . $e->getMessage());
            return false;
        }
    }
}