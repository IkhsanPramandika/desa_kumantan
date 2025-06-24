<?php

namespace App\Notifications;

use App\Models\Pengumuman;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengumumanBaru extends Notification
{
    use Queueable;

    protected $pengumuman;

    /**
     * Create a new notification instance.
     */
    public function __construct(Pengumuman $pengumuman)
    {
        $this->pengumuman = $pengumuman;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Kita akan menyimpannya ke database
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Ini adalah struktur data yang akan diterima oleh aplikasi Flutter
        return [
            'title' => 'Pengumuman Baru Desa',
            'message' => $this->pengumuman->judul,
            'link' => '/pengumuman/' . $this->pengumuman->slug, // Opsional, untuk navigasi
        ];
    }
}
