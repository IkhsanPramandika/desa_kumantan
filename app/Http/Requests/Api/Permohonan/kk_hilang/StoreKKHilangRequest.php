<?php

namespace App\Http\Requests\Api\Permohonan\kk_hilang;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreKKHilangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'surat_pengantar_rt_rw' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_keterangan_hilang_kepolisian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_kk_lama' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp_pemohon' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan_pemohon' => 'nullable|string',
        ];
    }
/*************  ✨ Windsurf Command ⭐  *************/
/**

/*******  3306db6b-9da0-4e47-a10e-8744deb4f0b7  *******/
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
            'surat_pengantar_rt_rw.mimes' => 'Format Surat Pengantar harus PDF, JPG, atau PNG.',
            'surat_pengantar_rt_rw.max' => 'Ukuran Surat Pengantar maksimal 2MB.',
            'surat_keterangan_hilang_kepolisian.mimes' => 'Format Surat Keterangan Hilang harus PDF, JPG, atau PNG.',
            'surat_keterangan_hilang_kepolisian.max' => 'Ukuran Surat Keterangan Hilang maksimal 2MB.',
            'file_kk_lama.mimes' => 'Format File KK Lama harus PDF, JPG, atau PNG.',
            'file_kk_lama.max' => 'Ukuran File KK Lama maksimal 2MB.',
            'file_ktp_pemohon.mimes' => 'Format File KTP Pemohon harus PDF, JPG, atau PNG.',
            'file_ktp_pemohon.max' => 'Ukuran File KTP Pemohon maksimal 2MB.',
        ];
    }
}