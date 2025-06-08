<?php

namespace App\Http\Resources\Permohonan\kk_perubahan; // Namespace sesuai struktur folder

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PermohonananKKPerubahanDataResource extends JsonResource
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
            'status' => $this->status,
            'catatan_pemohon' => $this->catatan, // atau $this->catatan_pemohon
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->isoFormat('D MMMM YYYY, HH:mm:ss'),
            'tanggal_selesai_proses' => $this->tanggal_selesai_proses ? Carbon::parse($this->tanggal_selesai_proses)->isoFormat('D MMMM YYYY, HH:mm:ss') : null,
            
            'file_kk_url' => $this->file_kk ? Storage::disk('public')->url($this->file_kk) : null,
            'file_ktp_url' => $this->file_ktp ? Storage::disk('public')->url($this->file_ktp) : null,
            'surat_pengantar_rt_rw_url' => $this->surat_pengantar_rt_rw ? Storage::disk('public')->url($this->surat_pengantar_rt_rw) : null,
            'surat_keterangan_pendukung_url' => $this->surat_keterangan_pendukung ? Storage::disk('public')->url($this->surat_keterangan_pendukung) : null,
            
            'file_hasil_akhir_url' => $this->file_hasil_akhir ? Storage::url($this->file_hasil_akhir) : null,
            
            // 'pemohon' => new \App\Http\Resources\MasyarakatResource($this->whenLoaded('masyarakat')),
        ];
    }
}
