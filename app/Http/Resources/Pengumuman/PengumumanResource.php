<?php

namespace App\Http\Resources\Pengumuman;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PengumumanResource extends JsonResource
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
            'judul' => $this->judul,
            'slug' => $this->slug,
            'ringkasan' => Str::limit(strip_tags($this->isi), 100, '...'), // Ringkasan untuk daftar berita
            'isi_lengkap' => $this->isi, // Isi lengkap untuk halaman detail
            'url_gambar' => $this->gambar_pengumuman ? asset('storage/' . $this->gambar_pengumuman) : null,
            'tanggal' => $this->tanggal_publikasi->translatedFormat('d F Y'), // Format tanggal
            'penulis' => $this->whenLoaded('user', function () {
                return $this->user->nama; // Asumsi model User punya field 'nama'
            }, 'Pemerintah Desa'),
        ];
    }
}