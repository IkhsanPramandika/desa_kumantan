<?php

namespace App\Http\Requests\Api\Permohonan\kk_perubahan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKKPerubahanDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Sesuaikan dengan logika otorisasi Anda
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_kk' => 'sometimes|nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_ktp' => 'sometimes|nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_pengantar_rt_rw' => 'sometimes|nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_keterangan_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'catatan' => 'nullable|string|max:1000',
            'status' => 'sometimes|required|string|in:pending,diproses,disetujui,ditolak,selesai', // Sesuaikan status yang valid
            'file_hasil_akhir' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tanggal_selesai_proses' => 'nullable|date',
            'catatan_penolakan' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // Anda bisa menambahkan pesan kustom di sini jika diperlukan,
        // mirip dengan StoreKKPerubahanDataRequest
        return [
            'status.in' => 'Nilai status tidak valid.',
            // ... pesan lainnya
        ];
    }
}
