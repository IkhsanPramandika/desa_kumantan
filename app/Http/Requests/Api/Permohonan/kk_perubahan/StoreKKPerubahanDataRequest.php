<?php

namespace App\Http\Requests\Api\Permohonan\kk_perubahan; // Namespace sesuai struktur folder

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreKKPerubahanDataRequest extends FormRequest
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
            'file_kk'                       => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // KK Lama yang akan diubah
            'file_ktp'                      => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // KTP Pemohon (salah satu anggota KK)
            'surat_pengantar_rt_rw'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_keterangan_pendukung'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Dokumen pendukung perubahan
            'catatan'                       => 'nullable|string|max:1000', // Atau 'catatan_pemohon'
            // Tambahkan validasi untuk field data teks lain jika ada, misal:
            // 'detail_perubahan_yang_diinginkan' => 'required|string|max:1000', 
        ];
    }

    public function messages(): array
    {
        return [
            'file_kk.required' => 'File Kartu Keluarga (lama) wajib diunggah.',
            'file_ktp.required' => 'File KTP Pemohon wajib diunggah.',
            'surat_pengantar_rt_rw.required' => 'Surat Pengantar RT/RW wajib diunggah.',
            'surat_keterangan_pendukung.required' => 'Surat Keterangan Pendukung perubahan data wajib diunggah.',
            '*.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            '*.max' => 'Ukuran file maksimal 2MB.',
        ];
    }
}
