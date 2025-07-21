<?php

namespace App\Http\Resources\Permohonan\sk_perkawinan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PermohonanSKPerkawinanResource extends JsonResource
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
            
            // Data Pria
            'nama_pria' => $this->nama_pria,
            'nik_pria' => $this->nik_pria,
            'tempat_lahir_pria' => $this->tempat_lahir_pria,
            'tanggal_lahir_pria' => $this->tanggal_lahir_pria,
            'alamat_pria' => $this->alamat_pria,

            // Data Wanita
            'nama_wanita' => $this->nama_wanita,
            'nik_wanita' => $this->nik_wanita,
            'tempat_lahir_wanita' => $this->tempat_lahir_wanita,
            'tanggal_lahir_wanita' => $this->tanggal_lahir_wanita,
            'alamat_wanita' => $this->alamat_wanita,

                   // Info Tambahan
            'catatan_pemohon' => $this->catatan_pemohon,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // --- PERBAIKAN KUNCI ADA DI SINI ---
            // Menghasilkan URL download jika file sudah ada
            'file_hasil_akhir_url' => $this->when($this->file_hasil_akhir, function () {
                // Menggunakan nama rute yang benar sesuai dengan struktur api.php Anda
                return route('api.masyarakat.auth.permohonan-sk-perkawinan.download', $this->id);
            }),
        ];
    }
}
