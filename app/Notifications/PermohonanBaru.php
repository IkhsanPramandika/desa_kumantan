<?php

// ===================================================================
// File: app/Notifications/PermohonanBaru.php (Final - Hybrid Payload)
// ===================================================================

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmMessage;
use Kreait\Firebase\Messaging\AndroidConfig;

class PermohonanBaru extends Notification implements ShouldQueue
{
    use Queueable;

    public string $title;
    public string $message;
    public string $url;
    public int $permohonanId;

    public function __construct(string $title, string $message, string $url, int $permohonanId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->permohonanId = $permohonanId;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];
        if ($notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toArray($notifiable): array
    {
        return [
            'judul' => $this->title,
            'pesan' => $this->message,
            'permohonan_id' => $this->permohonanId,
            'url' => $this->url,
        ];
    }

    /**
     * [PERBAIKAN FINAL] Menggunakan payload hybrid (notification + data)
     * untuk kompatibilitas maksimal di semua perangkat.
     */
    public function toFcm($notifiable): FcmMessage
    {
        Log::info("[HYBRID FCM - PermohonanBaru] Mengirim notifikasi ke user ID: " . $notifiable->id);

        $androidConfig = AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'channel_id' => 'high_importance_channel',
                'sound' => 'default',
            ],
        ]);

        return FcmMessage::create()
            // Bagian 1: setNotification() untuk ditampilkan langsung oleh OS
            ->setNotification([
                'title' => $this->title,
                'body' => $this->message,
            ])
            // Bagian 2: setData() untuk membawa data tambahan
            ->setData([
                'title' => $this->title,
                'body' => $this->message,
                'permohonan_id' => (string) $this->permohonanId,
                'jenis_notifikasi' => 'permohonan_baru_untuk_petugas',
                'url_webview' => $this->url,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ])
            ->setAndroid($androidConfig);
    }
}