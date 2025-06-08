<?php

namespace App\Http\Requests\Api\Permohonan\sk_usaha;

use Illuminate\Foundation\Http\FormRequest;

class StoreSKUsahaRequest extends FormRequest
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
            // Data Pemohon
            'nama_pemohon' => 'required|string|max:255',
            'nik_pemohon' => 'required|string|size:16',
            'jenis_kelamin' => 'required|string|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'warganegara_agama' => 'required|string|max:100',
            'pekerjaan' => 'required|string|max:100',
            'alamat_pemohon' => 'required|string|max:500',
            
            // Data Usaha
            'nama_usaha' => 'required|string|max:255',
            'alamat_usaha' => 'required|string|max:500',
            
            // Catatan
            'catatan_pemohon' => 'nullable|string|max:1000',

            // Lampiran File
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}