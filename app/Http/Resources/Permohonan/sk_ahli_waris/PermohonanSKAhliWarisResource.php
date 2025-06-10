<?php

namespace App\Http\Resources\Permohonan\sk_ahli_waris;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PermohonanSKAhliWarisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        // [PERBAIKAN UTAMA] Gunakan nama rute yang benar dan lengkap
        $downloadUrl = $this->when(
            $this->status === 'selesai' && !empty($this->file_hasil_akhir) && Storage::disk('public')->exists($this->file_hasil_akhir),
            function () {
                // Pastikan untuk menggunakan nama rute yang benar dari file api.php Anda
                return route('api.masyarakat.auth.permohonan-sk-ahli-waris.download', ['id' => $this->id]);
            }
        );

        return [
            'id' => $this->id,
            // [PENINGKATAN] Ganti 'nama_pewaris' dengan field yang sesuai di model Anda.
            // Jika tidak ada, Anda bisa hapus atau ganti dengan 'nama_pemohon'
            'nama_pemohon_atau_pewaris' => $this->nama_pemohon ?? $this->nama_pewaris ?? 'Tidak ada data',
            'status' => $this->status,
            'catatan_penolakan' => $this->catatan_penolakan,
            
            // [PENINGKATAN] Menggunakan format tanggal yang lebih standar dan jelas
            'tanggal_pengajuan' => $this->created_at->toIso8601String(),
            
            // [PENINGKATAN] Menggunakan optional() untuk menghindari error jika tanggal selesai masih null
            'tanggal_selesai' => optional($this->tanggal_selesai_proses)->toIso8601String(),
            
            // [PERBAIKAN] Menggunakan variabel yang sudah kita siapkan di atas
            'url_download_hasil' => $downloadUrl,

            // [PENINGKATAN] Jika Anda ingin menampilkan detail lain dari model
            // 'detail_permohonan' => [
            //      'nik_pemohon' => $this->nik_pemohon,
            //      'daftar_ahli_waris' => $this->daftar_ahli_waris,
            // ]
        ];
    }
}