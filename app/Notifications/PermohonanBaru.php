<?php
// Lokasi: app/Notifications/PermohonanBaru.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use Kreait\Firebase\Messaging\AndroidConfig;

class PermohonanBaru extends Notification implements ShouldQueue
{
    use Queueable;

    // Properti untuk menyimpan data dari constructor cara lama
    public string $title;
    public string $message;
    public string $url;
    public int $permohonanId;

    /**
     * Constructor menggunakan cara lama, menerima parameter terpisah.
     */
    public function __construct(string $title, string $message, string $url, int $permohonanId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->permohonanId = $permohonanId;
    }

    /**
     * Menentukan channel pengiriman notifikasi.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];
        if ($notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    /**
     * Mengubah data notifikasi menjadi format array terstruktur.
     */
    public function toArray($notifiable): array
    {
        // Mencoba membuat sub-judul dengan mengambil teks setelah kata "dari "
        $sub_judul = 'Permohonan baru';
        if (Str::contains($this->message, ' dari ')) {
            $sub_judul = 'dari ' . Str::after($this->message, ' dari ');
        }
        
        return [
            'judul'     => $this->title,
            'sub_judul' => Str::words($sub_judul, 3, '...'),
            'pesan'     => $this->message,
            'url'       => $this->url,
            // Penting: Ikon akan selalu default karena tidak ada informasi ikon yang dikirim
            'ikon'      => 'fas fa-file-alt', 
        ];
    }

    /**
     * Mengubah data notifikasi menjadi format untuk dikirim via FCM.
     */
    public function toFcm($notifiable): FcmMessage
    {
        Log::info("[HYBRID FCM - PermohonanBaru] Mengirim notifikasi ke petugas ID: " . $notifiable->id);

        $androidConfig = AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'channel_id' => 'high_importance_channel',
                'sound' => 'default',
            ],
        ]);
        
        $payloadData = $this->toArray($notifiable);
        $payloadData['permohonan_id'] = (string) $this->permohonanId;
        $payloadData['jenis_notifikasi'] = 'permohonan_baru_untuk_petugas';
        $payloadData['url_webview'] = $this->url;
        $payloadData['click_action'] = 'FLUTTER_NOTIFICATION_CLICK';

        // Memastikan semua value adalah string
        foreach ($payloadData as $key => $value) {
            $payloadData[$key] = (string) $value;
        }

        return FcmMessage::create()
            ->setNotification([
                'title' => $this->title,
                'body' => $this->message,
            ])
            ->setData($payloadData)
            ->setAndroid($androidConfig);
    }
}