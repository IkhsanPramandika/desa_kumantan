<?php

namespace App\Http\Resources\Permohonan\sk_domisili;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PermohonanSKDomisiliResource extends JsonResource
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
            'nama_pemohon_atau_lembaga' => $this->nama_pemohon_atau_lembaga,
            'keperluan_domisili' => $this->keperluan_domisili,
            'status' => $this->status,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->toIso8601String(),
            'tanggal_selesai' => optional($this->tanggal_selesai_proses)->toIso8601String(),
            'file_hasil_akhir_url' => $this->when($this->status === 'selesai' && $this->file_hasil_akhir, 
                // Jika ingin link download aman, arahkan ke route API, bukan URL storage langsung
                route('api.permohonan.sk-domisili.download', $this->id)
            ),
        ];
    }
}