<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermohonanMasuk implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $notifikasi;

    /**
     * Create a new event instance.
     */
    public function __construct(array $dataNotifikasi)
    {
        $this->notifikasi = $dataNotifikasi;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        /**
         * [PENTING] Menggunakan Channel publik untuk menghindari semua masalah otorisasi
         * yang telah kita alami. Ini adalah cara paling pasti.
         */
        return [
            new Channel('notifikasi-publik-petugas'),
        ];
    }

    /**
     * Nama event yang akan didengarkan oleh JavaScript.
     */
    public function broadcastAs(): string
    {
        return 'permohonan.baru';
    }
}
