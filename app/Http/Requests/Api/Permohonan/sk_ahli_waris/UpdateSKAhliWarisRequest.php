<?php

namespace App\Http\Requests\Api\Permohonan\sk_ahli_waris;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSKAhliWarisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_pewaris' => 'required|string|max:255',
            'nik_pewaris' => 'required|string|digits:16',
            'tempat_lahir_pewaris' => 'required|string|max:100',
            'tanggal_lahir_pewaris' => 'required|date',
            'tanggal_meninggal_pewaris' => 'required|date',
            'alamat_pewaris' => 'required|string',
            'daftar_ahli_waris' => 'required|json',
            'file_kk_pemohon' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp_pemohon' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_kk_ahli_waris' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp_ahli_waris' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_pengantar_rt_rw' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_keterangan_kematian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
}
