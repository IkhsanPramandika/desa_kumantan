<?php

namespace App\Http\Resources\Permohonan\sk_tidak_mampu;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanSKTidakMampuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            // Menambahkan lebih banyak data agar detail di Flutter lebih lengkap
            'nama_pemohon' => $this->whenLoaded('masyarakat', function () {
                return $this->masyarakat->nama_lengkap;
            }),
            'nama_terkait' => $this->nama_terkait,
            'keperluan_surat' => $this->keperluan_surat,
            'status' => $this->status,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->toIso8601String(),
            'tanggal_selesai' => optional($this->tanggal_selesai_proses)->toIso8601String(),
            
            'file_hasil_akhir_url' => $this->when(
                $this->status === 'selesai' && $this->file_hasil_akhir,
                function () {
                    // --- PERBAIKAN KUNCI ADA DI SINI ---
                    // Menggunakan nama rute yang benar sesuai dengan file api.php
                   return route('api.masyarakat.auth.permohonan-sk-tidak-mampu.download', $this->id);
                }
            ),
        ];
    }
}
