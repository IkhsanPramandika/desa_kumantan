<?php

namespace App\Http\Requests\Api\Permohonan\kk_perubahan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreKKPerubahanDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_pengantar_rt_rw' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_keterangan_pendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan_pemohon' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Data yang diberikan tidak valid.',
            'errors' => $validator->errors(),
        ], 422));
    }

    public function messages(): array
    {
        return [
            'file_kk.mimes' => 'Format File Kartu Keluarga harus PDF, JPG, atau PNG.',
            'file_kk.max' => 'Ukuran File Kartu Keluarga maksimal 2MB.',
            'file_ktp.mimes' => 'Format File KTP harus PDF, JPG, atau PNG.',
            'file_ktp.max' => 'Ukuran File KTP maksimal 2MB.',
            'surat_pengantar_rt_rw.mimes' => 'Format Surat Pengantar harus PDF, JPG, atau PNG.',
            'surat_pengantar_rt_rw.max' => 'Ukuran Surat Pengantar maksimal 2MB.',
            'surat_keterangan_pendukung.mimes' => 'Format Surat Keterangan Pendukung harus PDF, JPG, atau PNG.',
            'surat_keterangan_pendukung.max' => 'Ukuran Surat Keterangan Pendukung maksimal 2MB.',
        ];
    }
}