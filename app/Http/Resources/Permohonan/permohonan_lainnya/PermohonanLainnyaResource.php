<?php

namespace App\Http\Resources\Permohonan\permohonan_lainnya;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PermohonanLainnyaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'masyarakat_id' => $this->masyarakat_id,
            'judul_permohonan' => $this->judul_permohonan,
            'keperluan' => $this->keperluan,
            'rincian_pemohon' => $this->rincian_pemohon,
            'lampiran_url' => $this->lampiran ? Storage::disk('public')->url($this->lampiran) : null,
            'nomor_surat' => $this->nomor_surat,
            'judul_surat_final' => $this->judul_surat_final,
            'file_hasil_akhir_url' => $this->file_hasil_akhir ? Storage::disk('public')->url($this->file_hasil_akhir) : null,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_selesai_proses' => $this->tanggal_selesai_proses,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}