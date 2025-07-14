<?php

namespace App\Notifications;

use App\Interfaces\PermohonanInterface; // Pastikan Anda memiliki interface ini
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

/**
 * Class PermohonanStatusUpdated
 *
 * Notifikasi ini dikirim kepada MASYARAKAT ketika petugas mengubah status permohonan mereka
 * (misalnya: diverifikasi, ditolak, selesai).
 *
 * Menggunakan payload FCM "data-only" untuk memastikan kontrol penuh
 * pada aplikasi Flutter untuk menampilkan notifikasi pop-up (heads-up).
 */
class PermohonanStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    // Properti untuk menyimpan data notifikasi
    protected PermohonanInterface $permohonan;
    public string $title;
    public string $message;
    public string $url;

    /**
     * Create a new notification instance.
     *
     * [PERBAIKAN 1] Constructor diubah agar sesuai dengan cara pemanggilannya di Controller.
     * Sekarang menerima $title dan $message secara langsung dari Controller.
     *
     * @param PermohonanInterface $permohonan Instance dari model permohonan.
     * @param string $title Judul notifikasi yang sudah disiapkan.
     * @param string $message Isi pesan notifikasi yang sudah disiapkan.
     * @param string $url URL tujuan jika notifikasi di-klik (opsional).
     */
    public function __construct(PermohonanInterface $permohonan, string $title, string $message, string $url = '#')
    {
        $this->permohonan = $permohonan;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * Logika ini sudah benar, tidak perlu diubah.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    /**
     * Get the array representation of the notification.
     *
     * [PERBAIKAN 2] Data yang disimpan ke database disesuaikan agar menggunakan
     * $title dan $message yang diterima dari Controller.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'judul' => $this->title,
            'pesan' => $this->message,
            'permohonan_id' => $this->permohonan->getId(),
            'status' => $this->permohonan->status,
            'jenis_permohonan' => $this->permohonan->getTable(),
            'url' => $this->url,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     *
     * [PERBAIKAN KUNCI] Method ini diubah total untuk mengirim payload "data-only".
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        return FcmMessage::create()
            // HAPUS ->setNotification([...]) agar tidak ditangani otomatis oleh sistem Android.
            // Semua data, termasuk judul dan isi pesan, dimasukkan ke dalam 'data'.
            ->setData([
                // Flutter akan membaca 'title' dan 'body' dari sini untuk menampilkan notifikasi.
                'title' => $this->title,
                'body' => $this->message,

                // Data tambahan lain yang Anda perlukan di aplikasi Flutter.
                'permohonan_id' => (string) $this->permohonan->getId(),
                'status' => $this->permohonan->status,
                'jenis_permohonan' => $this->permohonan->getTable(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // Standar untuk Flutter
            ])
            // PENTING: Atur prioritas ke 'high' untuk memicu notifikasi pop-up (heads-up).
            ->setPriority('high');
    }
}
