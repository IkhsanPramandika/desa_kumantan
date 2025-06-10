<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermohonanMasuk implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notifikasi;

    public function __construct($dataNotifikasi)
    {
        $this->notifikasi = $dataNotifikasi;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Menyiarkan ke channel privat, hanya untuk petugas yang login
        return new PrivateChannel('notifikasi-petugas');
    }

    /**
     * Nama event yang akan didengarkan oleh JavaScript.
     */
    public function broadcastAs()
    {
        return 'permohonan.baru';
    }
}