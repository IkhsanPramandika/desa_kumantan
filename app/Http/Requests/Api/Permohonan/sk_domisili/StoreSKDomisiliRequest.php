<?php

namespace App\Http\Requests\Api\Permohonan\sk_domisili;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreSKDomisiliRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'nama_pemohon_atau_lembaga' => 'required|string|max:255',
            'nik_pemohon' => 'nullable|string|max:255',
            'alamat_lengkap_domisili' => 'required|string',
            'rt_domisili' => 'nullable|string|max:5',
            'rw_domisili' => 'nullable|string|max:5',
            'dusun_domisili' => 'nullable|string|max:255',
            'keperluan_domisili' => 'required|string',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_surat_pengantar_rt_rw' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
            'nama_pemohon_atau_lembaga.required' => 'Nama pemohon atau lembaga wajib diisi.',
            'alamat_lengkap_domisili.required' => 'Alamat lengkap domisili wajib diisi.',
            'keperluan_domisili.required' => 'Keperluan domisili wajib diisi.',
            'file_kk.mimes' => 'Format File KK harus PDF, JPG, atau PNG.',
            'file_kk.max' => 'Ukuran File KK maksimal 2MB.',
            'file_ktp.mimes' => 'Format File KTP harus PDF, JPG, atau PNG.',
            'file_ktp.max' => 'Ukuran File KTP maksimal 2MB.',
            'file_surat_pengantar_rt_rw.mimes' => 'Format Surat Pengantar harus PDF, JPG, atau PNG.',
            'file_surat_pengantar_rt_rw.max' => 'Ukuran Surat Pengantar maksimal 2MB.',
        ];
    }
}