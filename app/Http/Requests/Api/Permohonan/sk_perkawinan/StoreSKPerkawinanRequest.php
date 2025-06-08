<?php

namespace App\Http\Requests\Api\Permohonan\sk_perkawinan;

use Illuminate\Foundation\Http\FormRequest;

class StoreSKPerkawinanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Izinkan semua pengguna yang terotentikasi
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Data Mempelai Pria
            'nama_pria' => 'required|string|max:255',
            'nik_pria' => 'required|string|size:16',
            'tempat_lahir_pria' => 'required|string|max:100',
            'tanggal_lahir_pria' => 'required|date',
            'alamat_pria' => 'required|string|max:500',

            // Data Mempelai Wanita
            'nama_wanita' => 'required|string|max:255',
            'nik_wanita' => 'required|string|size:16',
            'tempat_lahir_wanita' => 'required|string|max:100',
            'tanggal_lahir_wanita' => 'required|date',
            'alamat_wanita' => 'required|string|max:500',

            // Detail Akad
            'tanggal_akad_nikah' => 'required|date',
            'tempat_akad_nikah' => 'required|string|max:255',
            'pemohon_surat' => 'required|in:pria,wanita',

            // Catatan
            'catatan_pemohon' => 'nullable|string|max:1000',

            // Lampiran File
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp_mempelai' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'surat_nikah_orang_tua' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'kartu_imunisasi_catin' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'sertifikat_elsimil' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'akta_penceraian' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}