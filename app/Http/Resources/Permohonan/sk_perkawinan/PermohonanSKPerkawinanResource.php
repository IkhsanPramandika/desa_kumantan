<?php

namespace App\Http\Resources\Permohonan\sk_perkawinan;

use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanSKPerkawinanResource extends JsonResource
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
            'pemohon_surat' => $this->pemohon_surat,
            'nama_pria' => $this->nama_pria,
            'nama_wanita' => $this->nama_wanita,
            'status' => $this->status,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->toIso8601String(),
            'tanggal_selesai' => optional($this->tanggal_selesai_proses)->toIso8601String(),
            'file_hasil_akhir_url' => $this->when(
                $this->status === 'selesai' && $this->file_hasil_akhir,
                route('api.permohonan.sk-perkawinan.download', $this->id)
            ),
        ];
    }
}