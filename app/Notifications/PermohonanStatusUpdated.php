<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Interfaces\PermohonanInterface;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class PermohonanStatusUpdated extends Notification
{
    use Queueable;

    protected $permohonan;
    
    public function __construct(PermohonanInterface $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     * Logika ini sudah benar, tidak perlu diubah.
     */
    public function via($notifiable)
    {
        $channels = ['database'];
        if ($notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    /**
     * [PERBAIKAN 3] Ubah cara pengambilan data agar menggunakan method dari Interface.
     * Format notifikasi untuk disimpan di database (untuk website).
     */
    public function toArray($notifiable)
    {
        return [
            'pesan' => 'Ada ' . $this->permohonan->getJudulNotifikasi() . ' baru dari ' . $this->permohonan->getPemohon()->nama_lengkap, // Asumsi di model Masyarakat ada kolom 'nama_lengkap'
            'permohonan_id' => $this->permohonan->getId(),
            'url' => $this->permohonan->getRouteTujuan(),
        ];
    }

    /**
     * [PERBAIKAN 4] Ubah juga cara pengambilan data di sini agar menggunakan method dari Interface.
     * Format notifikasi untuk dikirim via FCM (untuk mobile).
     */
    public function toFcm($notifiable)
{
    return FcmMessage::create()
        ->setNotification([
            'title' => $this->permohonan->getJudulNotifikasi(),
            'body' => 'Ada permohonan baru dari ' . $this->permohonan->getPemohon()->nama_lengkap, // Asumsi nama kolomnya 'nama_lengkap'
            // 'image' => 'URL_GAMBAR_JIKA_ADA' // Anda bisa menambahkan URL gambar di sini jika perlu
        ])
        ->setData([ // Bagian ini tidak perlu diubah
            'permohonan_id' => (string) $this->permohonan->getId(),
            'jenis_notifikasi' => str_replace(' ', '_', strtolower($this->permohonan->getJudulNotifikasi())),
            'url_webview' => $this->permohonan->getRouteTujuan(),
        ]);
}
}