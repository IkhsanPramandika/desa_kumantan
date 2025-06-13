<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PermohonanKKBaru; // Import model permohonan Anda

class PermohonanKKBaruMasuk extends Notification
{
    use Queueable;

    protected $permohonan;

    /**
     * Buat instance notifikasi baru.
     * Kita akan mengirimkan data permohonan yang baru dibuat ke sini.
     *
     * @param PermohonanKKBaru $permohonan
     */
    public function __construct(PermohonanKKBaru $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     * Kita akan menggunakan 'database' agar tersimpan di tabel notifications.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Ubah notifikasi menjadi format array untuk disimpan di database.
     * Data inilah yang akan kita tampilkan di website petugas.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'nama_pemohon' => $this->permohonan->nama_lengkap, // Ambil dari model PermohonanKKBaru
            'jenis_surat' => 'Permohonan KK Baru',
            'pesan' => 'Permohonan KK Baru telah diajukan oleh ' . $this->permohonan->nama_lengkap,
            // Pastikan nama route ini ada di file web.php untuk petugas
            'url' => route('petugas.permohonan-kk-baru.show', $this->permohonan->id),
            'waktu' => now()->toDateTimeString(),
        ];
    }
}
