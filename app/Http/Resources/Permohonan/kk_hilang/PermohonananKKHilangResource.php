<?php

namespace App\Http\Resources\Permohonan\kk_hilang; // Sesuai struktur folder

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PermohonananKKHilangResource extends JsonResource
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
            'catatan_pemohon' => $this->catatan,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->isoFormat('D MMMM YYYY, HH:mm:ss'),
            'tanggal_selesai_proses' => $this->tanggal_selesai_proses ? Carbon::parse($this->tanggal_selesai_proses)->isoFormat('D MMMM YYYY, HH:mm:ss') : null,
            
            'surat_pengantar_rt_rw_url' => $this->surat_pengantar_rt_rw ? Storage::disk('public')->url($this->surat_pengantar_rt_rw) : null,
            'surat_keterangan_hilang_kepolisian_url' => $this->surat_keterangan_hilang_kepolisian ? Storage::disk('public')->url($this->surat_keterangan_hilang_kepolisian) : null,
            
            'file_hasil_akhir_url' => $this->file_hasil_akhir ? Storage::disk('public')->url($this->file_hasil_akhir) : null,
            
            // Contoh jika ada relasi ke masyarakat dan ingin menampilkannya
            // 'pemohon' => new \App\Http\Resources\MasyarakatResource($this->whenLoaded('masyarakat')),
            // Anda perlu membuat MasyarakatResource jika belum ada
        ];
    }
}
