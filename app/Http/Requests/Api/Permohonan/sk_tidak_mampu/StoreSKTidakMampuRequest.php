<?php

namespace App\Http\Requests\Api\Permohonan\sk_tidak_mampu;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSKTidakMampuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'nama_terkait' => 'nullable|string|max:255',
            'nik_terkait' => 'nullable|string|max:20',
            'tempat_lahir_terkait' => 'nullable|string|max:100',
            'tanggal_lahir_terkait' => 'nullable|date',
            'jenis_kelamin_terkait' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'agama_terkait' => ['nullable', Rule::in(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])],
            'pekerjaan_atau_sekolah_terkait' => 'nullable|string|max:100',
            'alamat_terkait' => 'nullable|string',
            'keperluan_surat' => 'required|string',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_pendukung_lain' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
            'keperluan_surat.required' => 'Keperluan surat wajib diisi.',
            'file_kk.mimes' => 'Format File KK harus PDF, JPG, atau PNG.',
            'file_kk.max' => 'Ukuran File KK maksimal 2MB.',
            'file_ktp.mimes' => 'Format File KTP harus PDF, JPG, atau PNG.',
            'file_ktp.max' => 'Ukuran File KTP maksimal 2MB.',
            'file_pendukung_lain.mimes' => 'Format file pendukung lain harus PDF, JPG, atau PNG.',
            'file_pendukung_lain.max' => 'Ukuran file pendukung lain maksimal 2MB.',
        ];
    }
}