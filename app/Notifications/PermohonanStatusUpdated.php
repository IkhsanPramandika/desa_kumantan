<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermohonanStatusUpdated extends Notification
{
    use Queueable;

    protected $permohonan;
    protected $title;
    protected $message;
    protected $url;

    /**
     * Buat instance notifikasi baru.
     *
     * @param Model  $permohonan  Objek permohonan yang statusnya berubah.
     * @param string $title       Judul notifikasi (cth: "Permohonan Selesai").
     * @param string $message     Pesan detail notifikasi.
     * @param string $url         URL yang akan dituju saat notifikasi diklik.
     */
    public function __construct(Model $permohonan, string $title, string $message, string $url)
    {
        $this->permohonan = $permohonan;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     */
    public function via($notifiable)
    {
        return ['database']; // Kirim ke database untuk ditampilkan di aplikasi mobile
    }

    /**
     * Dapatkan representasi array dari notifikasi.
     */
    public function toArray($notifiable)
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'waktu' => now()->toDateTimeString(),
        ];
    }
}
