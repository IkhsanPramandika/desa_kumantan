<?php

// ===================================================================
// File: app/Notifications/StatusPermohonanDiperbarui.php (Final - Hybrid Payload)
// ===================================================================

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

    protected function setNotificationMessages(): void
    {
        $jenisSurat = method_exists($this->permohonan, 'getJudulNotifikasi')
                        ? $this->permohonan->getJudulNotifikasi()
                        : 'Permohonan Surat';

        switch ($this->permohonan->status) {
            case 'diterima':
                $this->judulNotifikasi = "Permohonan Anda Diterima";
                $this->pesanNotifikasi = "Verifikasi berhasil! {$jenisSurat} Anda telah diterima.";
                break;
            case 'selesai':
                $this->judulNotifikasi = "Permohonan Anda Telah Selesai";
                $this->pesanNotifikasi = "Selamat! Dokumen untuk {$jenisSurat} Anda telah selesai diproses.";
                break;
            default:
                $this->judulNotifikasi = "Permohonan Anda Telah Diajukan";
                $this->pesanNotifikasi = "Terima kasih! {$jenisSurat} Anda telah berhasil kami terima.";
                break;
        }
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
            'judul' => $this->judulNotifikasi,
            'pesan' => $this->pesanNotifikasi,
            'permohonan_id' => $this->permohonan->id,
            'status' => $this->permohonan->status,
            'jenis_permohonan' => $this->permohonan->getTable(),
        ];
    }

    /**
     * [PERBAIKAN FINAL] Menggunakan payload hybrid (notification + data)
     * untuk kompatibilitas maksimal di semua perangkat.
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        Log::info("[HYBRID FCM - StatusPermohonanDiperbarui] Mengirim notifikasi ke User ID: {$notifiable->id}");

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
                'title' => $this->judulNotifikasi,
                'body' => $this->pesanNotifikasi,
            ])
            // Bagian 2: setData() untuk membawa data tambahan
            ->setData([
                'title' => $this->judulNotifikasi,
                'body' => $this->pesanNotifikasi,
                'permohonan_id' => (string) $this->permohonan->id,
                'status' => $this->permohonan->status,
                'jenis_permohonan' => $this->permohonan->getTable(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ])
            ->setAndroid($androidConfig);
    }
}