<?php

namespace App\Http\Requests\Api\Permohonan\sk_usaha;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSKUsahaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'nama_pemohon' => 'nullable|string|max:255',
            'nik_pemohon' => 'nullable|string|max:255',
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'warganegara_agama' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat_pemohon' => 'nullable|string',
            'nama_usaha' => 'nullable|string|max:255',
            'alamat_usaha' => 'nullable|string',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
        ];
    }
}