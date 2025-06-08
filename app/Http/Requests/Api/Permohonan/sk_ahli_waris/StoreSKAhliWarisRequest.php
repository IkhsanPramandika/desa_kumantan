<?php

namespace App\Http\Requests\Api\Permohonan\sk_ahli_waris;

use Illuminate\Foundation\Http\FormRequest;

class StoreSKAhliWarisRequest extends FormRequest
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
            // Data Pewaris
            'nama_pewaris' => 'required|string|max:255',
            'nik_pewaris' => 'required|string|size:16',
            'tempat_lahir_pewaris' => 'required|string|max:100',
            'tanggal_lahir_pewaris' => 'required|date',
            'tanggal_meninggal_pewaris' => 'required|date',
            'alamat_pewaris' => 'required|string|max:500',
            
            // Daftar Ahli Waris (harus berupa array)
            'daftar_ahli_waris' => 'required|array|min:1',
            'daftar_ahli_waris.*.nama' => 'required|string|max:255',
            'daftar_ahli_waris.*.nik' => 'required|string|size:16',
            'daftar_ahli_waris.*.hubungan' => 'required|string|max:100',
            'daftar_ahli_waris.*.alamat' => 'required|string|max:500',

            // Catatan
            'catatan_pemohon' => 'nullable|string|max:1000',

            // Lampiran File
            'file_ktp_pemohon' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_kk_pemohon' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp_ahli_waris' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_kk_ahli_waris' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'surat_keterangan_kematian' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_pengantar_rt_rw' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}