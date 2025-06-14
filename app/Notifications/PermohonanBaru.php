<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermohonanBaru extends Notification
{
    use Queueable;

    protected $permohonan;
    protected $jenisSurat;
    protected $routeName;

    /**
     * Buat instance notifikasi baru.
     *
     * @param Model $permohonan Model dari permohonan yang dibuat (misal: PermohonanKKBaru)
     * @param string $jenisSurat Nama dari jenis surat (misal: "KK Baru")
     * @param string $routeName Nama route untuk halaman detail (misal: "petugas.permohonan-kk-baru.show")
     */
    public function __construct(Model $permohonan, string $jenisSurat, string $routeName)
    {
        $this->permohonan = $permohonan;
        $this->jenisSurat = $jenisSurat;
        $this->routeName = $routeName;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Kita hanya butuh channel database untuk sistem polling
        return ['database'];
    }

    /**
     * Dapatkan representasi notifikasi dalam format array untuk disimpan di database.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Mengambil nama dari relasi 'masyarakat' yang sudah kita perbaiki sebelumnya.
        // Ini akan berfungsi selama semua model permohonan Anda punya relasi `masyarakat()`.
        $namaPemohon = $this->permohonan->masyarakat->nama_lengkap ?? 'Seorang Warga';

        return [
            'permohonan_id' => $this->permohonan->id,
            'nama_pemohon' => $namaPemohon,
            'jenis_surat' => $this->jenisSurat,
            'status' => 'pending', // Status awal selalu pending
            'pesan' => 'Permohonan ' . $this->jenisSurat . ' telah diajukan oleh ' . $namaPemohon,
            // Membuat URL secara dinamis menggunakan routeName yang kita kirim
            'url' => route($this->routeName, $this->permohonan->id),
            'waktu' => now()->toDateTimeString(), // Ini akan di-parse oleh Carbon di frontend/controller
        ];
    }
}
