<?php

// Lokasi file: app/Http/Resources/Permohonan/KkBaru/PermohonananKKBaruResource.php
namespace App\Http\Resources\Permohonan\kk_baru; // Namespace sesuai struktur folder Anda

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon; // Pastikan Carbon di-import

class PermohonananKKBaruResource extends JsonResource // Nama class sesuai dengan model Anda
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
            // Tambahkan field data teks yang relevan dari model Anda di sini jika ada
            // 'nama_kepala_keluarga' => $this->nama_kepala_keluarga, // Contoh
            // 'alamat' => $this->alamat, // Contoh
            'status' => $this->status,
            'catatan_pemohon' => $this->catatan,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->isoFormat('D MMMM YYYY, HH:mm:ss'), // Format tanggal Indonesia
            'tanggal_selesai_proses' => $this->tanggal_selesai_proses ? Carbon::parse($this->tanggal_selesai_proses)->isoFormat('D MMMM YYYY, HH:mm:ss') : null,
            
            // URL untuk file yang diupload (attachment)
            'file_kk_url' => $this->file_kk ? Storage::disk('public')->url($this->file_kk) : null,
            'file_ktp_url' => $this->file_ktp ? Storage::disk('public')->url($this->file_ktp) : null,
            'surat_pengantar_rt_rw_url' => $this->surat_pengantar_rt_rw ? Storage::disk('public')->url($this->surat_pengantar_rt_rw) : null,
            'buku_nikah_akta_cerai_url' => $this->buku_nikah_akta_cerai ? Storage::disk('public')->url($this->buku_nikah_akta_cerai) : null,
            'surat_pindah_datang_url' => $this->surat_pindah_datang ? Storage::disk('public')->url($this->surat_pindah_datang) : null,
            'ijazah_terakhir_url' => $this->ijazah_terakhir ? Storage::disk('public')->url($this->ijazah_terakhir) : null,
            
            // URL untuk file hasil akhir (PDF yang digenerate petugas)
            'file_hasil_akhir_url' => $this->file_hasil_akhir ? Storage::url($this->file_hasil_akhir) : null,
            
            // Contoh menyertakan data pemohon (masyarakat) jika ada relasi
            // 'pemohon' => new \App\Http\Resources\MasyarakatResource($this->whenLoaded('masyarakat')),
        ];
    }
}
