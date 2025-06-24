<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class PermohonanBaru extends Notification implements ShouldQueue
{
    use Queueable;

    // [REFACTOR] Properti sekarang menyimpan data mentah, bukan objek
    public string $title;
    public string $message;
    public string $url;
    public int $permohonanId;

    /**
     * [REFACTOR] Constructor sekarang menerima data yang sudah jadi.
     */
    public function __construct(string $title, string $message, string $url, int $permohonanId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->permohonanId = $permohonanId;
    }

    /**
     * Method via() tidak perlu diubah.
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
     * [REFACTOR] Method toArray() sekarang hanya menggunakan data yang sudah ada.
     * Tidak ada lagi query ke database.
     */
    public function toArray($notifiable): array
    {
       
    $dataToSave = [
        'pesan' => $this->message,
        'permohonan_id' => $this->permohonanId,
        'url' => $this->url,
    ];

    // [TES DEBUGGING FINAL]
    // Kita log data ini untuk melihat isinya persis sebelum disimpan
    \Illuminate\Support\Facades\Log::info('DATA YANG AKAN DISIMPAN KE DB NOTIFIKASI:', $dataToSave);

    return $dataToSave;
    }

    /**
     * [REFACTOR] Method toFcm() juga hanya menggunakan data yang sudah ada.
     */
    public function toFcm($notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->setNotification([
                'title' => $this->title,
                'body' => $this->message,
            ])
            ->setData([
                'permohonan_id' => (string) $this->permohonanId,
                'jenis_notifikasi' => str_replace(' ', '_', strtolower($this->title)),
                'url_webview' => $this->url,
            ]);
    }
}