<?php

namespace App\Http\Requests\Api\Permohonan\sk_kelahiran;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSKKelahiranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Data Anak
            'nama_anak' => 'required|string|max:255',
            'tempat_lahir_anak' => 'required|string|max:100',
            'tanggal_lahir_anak' => 'required|date',
            'jenis_kelamin_anak' => 'required|string|in:LAKI-LAKI,PEREMPUAN',
            'agama_anak' => 'required|string|max:50',
            'alamat_anak' => 'required|string|max:500',

            // Data Orang Tua
            'nama_ayah' => 'required|string|max:255',
            'nik_ayah' => 'nullable|string|digits:16',
            'nama_ibu' => 'required|string|max:255',
            'nik_ibu' => 'nullable|string|digits:16',
            'no_buku_nikah' => 'nullable|string|max:100',

            // File Lampiran Wajib
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Bisa jadi KTP kedua ortu digabung
            'surat_pengantar_rt_rw' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_nikah_orangtua' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_keterangan_kelahiran' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // dari Bidan/RS
            
            'catatan' => 'nullable|string|max:1000',
        ];
    }
}
