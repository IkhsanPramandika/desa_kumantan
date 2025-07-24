<?php
// Lokasi: app/Notifications/StatusPermohonanDiperbarui.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmMessage;
use Kreait\Firebase\Messaging\AndroidConfig;

class StatusPermohonanDiperbarui extends Notification implements ShouldQueue
{
    use Queueable;

    protected Model $permohonan;
    public string $judulNotifikasi;
    public string $pesanNotifikasi;

    public function __construct(Model $permohonan)
    {
        $this->permohonan = $permohonan;
        $this->setNotificationMessages();
    }

    /**
     * Menentukan judul dan pesan notifikasi berdasarkan status permohonan.
     */
    protected function setNotificationMessages(): void
    {
        $jenisSurat = method_exists($this->permohonan, 'getJudulNotifikasi')
                        ? $this->permohonan->getJudulNotifikasi()
                        : 'Permohonan Surat';

        switch ($this->permohonan->status) {
            // [KODE BARU] Menangani status saat permohonan perlu direvisi.
            case 'membutuhkan_revisi':
                $this->judulNotifikasi = "Permohonan Perlu Direvisi";
                $this->pesanNotifikasi = "{$jenisSurat} Anda perlu diperbaiki. Mohon periksa catatan dari petugas.";
                break;
            
            case 'diterima':
                $this->judulNotifikasi = "Permohonan Anda Diterima";
                $this->pesanNotifikasi = "Verifikasi berhasil! {$jenisSurat} Anda telah diterima.";
                break;
            case 'selesai':
                $this->judulNotifikasi = "Permohonan Anda Telah Selesai";
                $this->pesanNotifikasi = "Selamat! Dokumen untuk {$jenisSurat} Anda telah selesai diproses.";
                break;
            default: // Ini akan menangani status 'pending' atau lainnya
                $this->judulNotifikasi = "Permohonan Anda Telah Diajukan";
                $this->pesanNotifikasi = "Terima kasih! {$jenisSurat} Anda telah berhasil kami terima.";
                break;
        }
    }

    /**
     * Menentukan channel pengiriman notifikasi (database untuk web, fcm untuk mobile).
     */
    public function via($notifiable): array
    {
        Log::info('[DEBUG Notif] via() method called. Notifiable ID: ' . $notifiable->id . ', FCM Token: ' . ($notifiable->fcm_token ?? 'NULL')); // <-- Tambahkan ini
        $channels = ['database'];
        if ($notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    /**
     * Mengubah data notifikasi menjadi format array untuk disimpan di database.
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'judul' => $this->judulNotifikasi,
            'pesan' => $this->pesanNotifikasi,
            'permohonan_id' => $this->permohonan->id,
            'status' => $this->permohonan->status,
            'jenis_permohonan' => $this->permohonan->getTable(),
            // [PERBAIKAN] Menambahkan ikon agar bisa ditampilkan di UI notifikasi web
            'ikon' => method_exists($this->permohonan, 'getIcon') ? $this->permohonan->getIcon() : 'fas fa-file-alt',
            // [PERBAIKAN] Menambahkan URL tujuan agar notifikasi bisa diklik
            'url' => method_exists($this->permohonan, 'getRouteTujuan') ? $this->permohonan->getRouteTujuan() : '#',
        ];

        // [KODE BARU] Sertakan catatan penolakan jika statusnya adalah 'membutuhkan_revisi'.
        // Ini penting agar pengguna di aplikasi mobile tahu apa yang harus diperbaiki.
        if ($this->permohonan->status === 'membutuhkan_revisi') {
            $data['catatan_penolakan'] = $this->permohonan->catatan_penolakan;
        }

        return $data;
    }

    /**
     * Mengubah data notifikasi menjadi format untuk dikirim via Firebase Cloud Messaging (FCM).
     */
   public function toFcm($notifiable): FcmMessage
{
    Log::info("[HYBRID FCM] Mengirim notifikasi ke User ID: " . $notifiable->id . " - Tipe: " . get_class($this));

    $androidConfig = AndroidConfig::fromArray([
        'priority' => 'high',
        'notification' => [
            'channel_id' => 'high_importance_channel',
            'sound' => 'default',
        ],
    ]);

    $payloadData = $this->toArray($notifiable);
    $payloadData['click_action'] = 'FLUTTER_NOTIFICATION_CLICK';

    // Memastikan semua value adalah string
    foreach ($payloadData as $key => $value) {
        $payloadData[$key] = (string) $value;
    }

    $fcmMessage = FcmMessage::create()
        ->setNotification([
            'title' => $this->title ?? $this->judulNotifikasi, // Sesuaikan dengan nama properti yang benar
            'body' => $this->message ?? $this->pesanNotifikasi, // Sesuaikan
        ])
        ->setData($payloadData)
        ->setAndroid($androidConfig);

    // --- TAMBAHKAN LOG INI ---
    Log::info("[HYBRID FCM] Payload yang akan dikirim ke FCM:");
    Log::info(json_encode($fcmMessage->toArray(), JSON_PRETTY_PRINT));
    // --- AKHIR TAMBAHAN LOG ---

    return $fcmMessage;
}

}
