<?php

namespace App\Http\Resources\Permohonan\sk_kelahiran;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PermohonananSKKelahiranResource extends JsonResource
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
            'catatan' => $this->catatan,
            'catatan_penolakan' => $this->catatan_penolakan,
            'tanggal_pengajuan' => $this->created_at->isoFormat('D MMMM YYYY, HH:mm:ss'),
            'tanggal_selesai_proses' => $this->tanggal_selesai_proses ? Carbon::parse($this->tanggal_selesai_proses)->isoFormat('D MMMM YYYY, HH:mm:ss') : null,

            'data_anak' => [
                'nama' => $this->nama_anak,
                'tempat_lahir' => $this->tempat_lahir_anak,
                'tanggal_lahir' => $this->tanggal_lahir_anak ? Carbon::parse($this->tanggal_lahir_anak)->isoFormat('D MMMM YYYY') : null,
                'jenis_kelamin' => $this->jenis_kelamin_anak,
                'agama' => $this->agama_anak,
                'alamat' => $this->alamat_anak,
            ],
            'data_orang_tua' => [
                'nama_ayah' => $this->nama_ayah,
                'nik_ayah' => $this->nik_ayah,
                'nama_ibu' => $this->nama_ibu,
                'nik_ibu' => $this->nik_ibu,
                'no_buku_nikah' => $this->no_buku_nikah,
            ],

            'lampiran' => [
                'file_kk_url' => $this->file_kk ? Storage::disk('public')->url($this->file_kk) : null,
                'file_ktp_url' => $this->file_ktp ? Storage::disk('public')->url($this->file_ktp) : null,
                'surat_pengantar_rt_rw_url' => $this->surat_pengantar_rt_rw ? Storage::disk('public')->url($this->surat_pengantar_rt_rw) : null,
                'surat_nikah_orangtua_url' => $this->surat_nikah_orangtua ? Storage::disk('public')->url($this->surat_nikah_orangtua) : null,
                'surat_keterangan_kelahiran_url' => $this->surat_keterangan_kelahiran ? Storage::disk('public')->url($this->surat_keterangan_kelahiran) : null,
            ],

            'file_hasil_akhir_url' => $this->file_hasil_akhir ? Storage::url($this->file_hasil_akhir) : null,
        ];
    }
}
