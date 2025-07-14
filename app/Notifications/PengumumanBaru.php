<?php

// ===================================================================
// File: app/Notifications/PengumumanBaru.php (Final - Hybrid Payload)
// ===================================================================

namespace App\Notifications;

use App\Models\Pengumuman;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmMessage;
use Kreait\Firebase\Messaging\AndroidConfig;

class PengumumanBaru extends Notification implements ShouldQueue
{
    use Queueable;

    protected Pengumuman $pengumuman;

    public function __construct(Pengumuman $pengumuman)
    {
        $this->pengumuman = $pengumuman;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'judul' => 'Pengumuman Baru Desa',
            'pesan' => $this->pengumuman->judul,
            'pengumuman_id' => $this->pengumuman->id,
            'pengumuman_slug' => $this->pengumuman->slug,
            'url' => '/pengumuman/' . $this->pengumuman->slug,
        ];
    }

    /**
     * [PERBAIKAN FINAL] Menggunakan payload hybrid (notification + data)
     * untuk kompatibilitas maksimal di semua perangkat.
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        Log::info("[HYBRID FCM - PengumumanBaru] Mengirim notifikasi ke user ID: " . $notifiable->id);

        $title = 'Pengumuman Baru Desa';
        $body = $this->pengumuman->judul;

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
                'title' => $title,
                'body' => $body,
            ])
            // Bagian 2: setData() untuk membawa data tambahan
            ->setData([
                'title' => $title,
                'body' => $body,
                'pengumuman_id' => (string) $this->pengumuman->id,
                'pengumuman_slug' => $this->pengumuman->slug,
                'jenis_notifikasi' => 'pengumuman_baru',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ])
            ->setAndroid($androidConfig);
    }
}
