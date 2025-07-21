<?php
// Lokasi: app/Notifications/PermohonanBaru.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PermohonanBaru extends Notification implements ShouldQueue
{
    use Queueable;

    protected Model $permohonan;

    public function __construct(Model $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    public function via($notifiable): array
    {
        // Untuk contoh ini, kita fokus pada 'database' untuk web
        return ['database'];
    }

    /**
     * [PERBAIKAN] Mengembalikan data notifikasi yang lebih kaya dan terstruktur.
     */
    public function toArray($notifiable): array
    {
        // Asumsi model permohonan Anda memiliki method-method ini
        // Jika tidak, sesuaikan dengan cara Anda mendapatkan data ini.
        $jenisSurat = method_exists($this->permohonan, 'getJenisSurat') ? $this->permohonan->getJenisSurat() : 'Permohonan Baru';
        $namaPemohon = $this->permohonan->masyarakat->nama_lengkap ?? 'Masyarakat';
        $icon = method_exists($this->permohonan, 'getIcon') ? $this->permohonan->getIcon() : 'fas fa-file-alt';
        
        // URL tujuan ketika notifikasi diklik
        // Ganti 'nama.route.show' dengan nama route detail permohonan yang sesuai
        $url = route('petugas.permohonan-kk-baru.show', $this->permohonan->id); 

        return [
            'judul'     => $jenisSurat,
            'sub_judul' => 'dari ' . Str::words($namaPemohon, 2, '...'),
            'pesan'     => "Ada {$jenisSurat} baru dari {$namaPemohon}",
            'ikon'      => $icon,
            'url'       => $url,
        ];
    }
}