<?php

namespace App\Http\Resources\Permohonan\sk_usaha;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PermohonanSKUsahaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nama_usaha' => $this->nama_usaha,
            'status' => $this->status,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->toIso8601String(),
            'tanggal_selesai' => optional($this->tanggal_selesai_proses)->toIso8601String(),
            
            // PERBAIKAN FINAL: Nama route disesuaikan dengan output `route:list`
            'file_hasil_akhir_url' => $this->when(
                $this->status === 'selesai' && $this->file_hasil_akhir && Storage::disk('public')->exists($this->file_hasil_akhir),
                // Nama route yang benar sekarang adalah 'api.masyarakat.auth.permohonan-sk-usaha.download'
                route('api.masyarakat.auth.permohonan-sk-usaha.download', $this->id)
            ),
        ];
    }
}
