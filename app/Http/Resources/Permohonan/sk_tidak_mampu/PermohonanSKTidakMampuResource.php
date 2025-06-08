<?php

namespace App\Http\Resources\Permohonan\sk_tidak_mampu;

use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanSKTidakMampuResource extends JsonResource
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
            'nama_pemohon' => $this->nama_pemohon,
            'keperluan_surat' => $this->keperluan_surat,
            'status' => $this->status,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->toIso8601String(),
            'tanggal_selesai' => optional($this->tanggal_selesai_proses)->toIso8601String(),
            'file_hasil_akhir_url' => $this->when(
                $this->status === 'selesai' && $this->file_hasil_akhir,
                // Menggunakan route API yang aman untuk download
                route('api.permohonan.sk-tidak-mampu.download', $this->id)
            ),
        ];
    }
}