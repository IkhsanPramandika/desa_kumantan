<?php

namespace App\Http\Requests\Api\Permohonan\kk_hilang; // Sesuai struktur folder

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreKkHilangRequest extends FormRequest
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
            'surat_pengantar_rt_rw'             => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_keterangan_hilang_kepolisian'=> 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'catatan'                           => 'nullable|string|max:1000',
            // Tambahkan validasi untuk field lain yang mungkin dikirim dari form Flutter jika ada
            // misalnya, jika ada KTP atau KK lama yang perlu di-upload sebagai bukti tambahan:
            // 'file_ktp_pemohon' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // 'file_kk_lama' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'surat_pengantar_rt_rw.required' => 'Surat Pengantar RT/RW wajib diunggah.',
            'surat_pengantar_rt_rw.mimes' => 'Format Surat Pengantar RT/RW harus PDF, JPG, JPEG, atau PNG.',
            'surat_pengantar_rt_rw.max' => 'Ukuran Surat Pengantar RT/RW maksimal 2MB.',
            
            'surat_keterangan_hilang_kepolisian.required' => 'Surat Keterangan Hilang dari Kepolisian wajib diunggah.',
            'surat_keterangan_hilang_kepolisian.mimes' => 'Format Surat Keterangan Hilang harus PDF, JPG, JPEG, atau PNG.',
            'surat_keterangan_hilang_kepolisian.max' => 'Ukuran Surat Keterangan Hilang maksimal 2MB.',
        ];
    }
}
