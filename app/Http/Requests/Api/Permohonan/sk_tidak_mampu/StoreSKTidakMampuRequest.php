<?php

namespace App\Http\Requests\Api\Permohonan\sk_tidak_mampu;

use Illuminate\Foundation\Http\FormRequest;

class StoreSKTidakMampuRequest extends FormRequest
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
            'tempat_lahir_pemohon' => 'required|string|max:100',
            'tanggal_lahir_pemohon' => 'required|date',
            'jenis_kelamin_pemohon' => 'required|string|max:20',
            'agama_pemohon' => 'nullable|string|max:50',
            'kewarganegaraan_pemohon' => 'required|string|max:50',
            'pekerjaan_pemohon' => 'required|string|max:100',
            'alamat_pemohon' => 'required|string|max:500',

            // Data Pihak Terkait (opsional)
            'nama_terkait' => 'nullable|string|max:255',
            'nik_terkait' => 'nullable|string|size:16',
            'tempat_lahir_terkait' => 'nullable|string|max:100',
            'tanggal_lahir_terkait' => 'nullable|date',
            'jenis_kelamin_terkait' => 'nullable|string|max:20',
            'agama_terkait' => 'nullable|string|max:50',
            'kewarganegaraan_terkait' => 'nullable|string|max:50',
            'pekerjaan_atau_sekolah_terkait' => 'nullable|string|max:100',
            'alamat_terkait' => 'nullable|string|max:500',

            // Keperluan & Catatan
            'keperluan_surat' => 'required|string|max:1000',
            'catatan_pemohon' => 'nullable|string|max:1000',

            // Lampiran File
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_pendukung_lain' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}