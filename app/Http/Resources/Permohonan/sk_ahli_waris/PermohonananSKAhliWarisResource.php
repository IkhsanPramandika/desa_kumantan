<?php

namespace App\Http\Resources\Permohonan\sk_ahli_waris;

use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanSKAhliWarisResource extends JsonResource
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
            'nama_pewaris' => $this->nama_pewaris,
            'status' => $this->status,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->toIso8601String(),
            'tanggal_selesai' => optional($this->tanggal_selesai_proses)->toIso8601String(),
            'file_hasil_akhir_url' => $this->when(
                $this->status === 'selesai' && $this->file_hasil_akhir,
                route('api.permohonan.sk-ahli-waris.download', $this->id)
            ),
        ];
    }
}