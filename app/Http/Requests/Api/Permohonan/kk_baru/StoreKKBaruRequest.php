<?php

namespace App\Http\Requests\Api\Permohonan\kk_baru;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreKKBaruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'surat_pengantar_rt_rw' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'buku_nikah_akta_cerai' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_pindah_datang' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'ijazah_terakhir' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
            'surat_pengantar_rt_rw.mimes' => 'Format Surat Pengantar harus PDF, JPG, atau PNG.',
            'surat_pengantar_rt_rw.max' => 'Ukuran Surat Pengantar maksimal 2MB.',
            'buku_nikah_akta_cerai.mimes' => 'Format Buku Nikah/Akta Cerai harus PDF, JPG, atau PNG.',
            'buku_nikah_akta_cerai.max' => 'Ukuran Buku Nikah/Akta Cerai maksimal 2MB.',
            'file_kk.mimes' => 'Format File Kartu Keluarga harus PDF, JPG, atau PNG.',
            'file_kk.max' => 'Ukuran File Kartu Keluarga maksimal 2MB.',
            'file_ktp.mimes' => 'Format File KTP harus PDF, JPG, atau PNG.',
            'file_ktp.max' => 'Ukuran File KTP maksimal 2MB.',
            'surat_pindah_datang.mimes' => 'Format Surat Pindah Datang harus PDF, JPG, atau PNG.',
            'surat_pindah_datang.max' => 'Ukuran Surat Pindah Datang maksimal 2MB.',
            'ijazah_terakhir.mimes' => 'Format Ijazah Terakhir harus PDF, JPG, atau PNG.',
            'ijazah_terakhir.max' => 'Ukuran Ijazah Terakhir maksimal 2MB.',
        ];
    }
}