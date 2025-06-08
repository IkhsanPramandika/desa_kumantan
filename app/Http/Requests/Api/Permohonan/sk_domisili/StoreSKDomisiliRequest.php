<?php
namespace App\Http\Requests\Api\Permohonan\sk_domisili;

use Illuminate\Foundation\Http\FormRequest;

class StoreSKDomisiliRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Izinkan semua pengguna yang terotentikasi untuk membuat request ini
        return true;
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
            'nama_pemohon_atau_lembaga' => 'required|string|max:255',
            'nik_pemohon' => 'required|string|size:16',
            'jenis_kelamin_pemohon' => 'required|string|max:20',
            'tempat_lahir_pemohon' => 'required|string|max:100',
            'tanggal_lahir_pemohon' => 'required|date',
            'pekerjaan_pemohon' => 'required|string|max:100',
            'alamat_lengkap_domisili' => 'required|string|max:500',
            'rt_domisili' => 'required|string|max:3',
            'rw_domisili' => 'required|string|max:3',
            'dusun_domisili' => 'required|string|max:100',
            'keperluan_domisili' => 'required|string|max:1000',
            'catatan_pemohon' => 'nullable|string|max:1000',

            // Lampiran File (wajib diisi, maksimal 2MB per file)
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_surat_pengantar_rt_rw' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}