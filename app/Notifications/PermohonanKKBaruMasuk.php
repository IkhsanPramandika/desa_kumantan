<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PermohonanKKBaru; 

class PermohonanKKBaruMasuk extends Notification
{
    use Queueable;

    protected $permohonan;

    public function __construct(PermohonanKKBaru $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    public function via($notifiable)
    {
        // Untuk sistem polling, 'database' saja sudah cukup.
        // 'broadcast' tidak lagi digunakan.
        return ['database']; 
    }

    /**
     * Mendapatkan representasi notifikasi dalam format array.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // PERBAIKAN: Ambil nama dari relasi 'masyarakat' yang terhubung dengan permohonan.
        // Ini lebih aman dan sesuai dengan praktik terbaik Laravel.
        // Kita juga tambahkan nilai default 'Seorang Warga' jika relasi/nama tidak ditemukan.
        $namaPemohon = $this->permohonan->masyarakat->nama_lengkap ?? 'Seorang Warga';

        return [
            'permohonan_id' => $this->permohonan->id,
            'nama_pemohon' => $namaPemohon, // Menggunakan variabel yang sudah kita siapkan
            'jenis_surat' => 'Permohonan KK Baru',
            'pesan' => 'Permohonan KK Baru telah diajukan oleh ' . $namaPemohon,
            'url' => route('petugas.permohonan-kk-baru.show', $this->permohonan->id),
            'waktu' => now()->toDateTimeString(),
        ];
    }
}
