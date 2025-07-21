<?php

namespace App\Http\Requests\Api\Permohonan\sk_perkawinan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreSKPerkawinanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'nama_pria' => 'required|string|max:255',
            'nik_pria' => 'required|string|max:16',
            'tempat_lahir_pria' => 'required|string|max:255',
            'tanggal_lahir_pria' => 'required|date',
            'alamat_pria' => 'required|string',
            'nama_wanita' => 'required|string|max:255',
            'nik_wanita' => 'required|string|max:16',
            'tempat_lahir_wanita' => 'required|string|max:255',
            'tanggal_lahir_wanita' => 'required|date',
            'alamat_wanita' => 'required|string',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp_mempelai' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_nikah_orang_tua' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'kartu_imunisasi_catin' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sertifikat_elsimil' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'akta_penceraian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
            'nama_pria.required' => 'Nama mempelai pria wajib diisi.',
            'nik_pria.required' => 'NIK mempelai pria wajib diisi.',
            'tempat_lahir_pria.required' => 'Tempat lahir mempelai pria wajib diisi.',
            'tanggal_lahir_pria.required' => 'Tanggal lahir mempelai pria wajib diisi.',
            'alamat_pria.required' => 'Alamat mempelai pria wajib diisi.',
            'nama_wanita.required' => 'Nama mempelai wanita wajib diisi.',
            'nik_wanita.required' => 'NIK mempelai wanita wajib diisi.',
            'tempat_lahir_wanita.required' => 'Tempat lahir mempelai wanita wajib diisi.',
            'tanggal_lahir_wanita.required' => 'Tanggal lahir mempelai wanita wajib diisi.',
            'alamat_wanita.required' => 'Alamat mempelai wanita wajib diisi.',
            'file_kk.mimes' => 'Format file KK harus PDF, JPG, atau PNG.',
            'file_ktp_mempelai.mimes' => 'Format file KTP harus PDF, JPG, atau PNG.',
            'surat_nikah_orang_tua.mimes' => 'Format surat nikah orang tua harus PDF, JPG, atau PNG.',
            'kartu_imunisasi_catin.mimes' => 'Format kartu imunisasi harus PDF, JPG, atau PNG.',
            'sertifikat_elsimil.mimes' => 'Format sertifikat elsimil harus PDF, JPG, atau PNG.',
            'akta_penceraian.mimes' => 'Format akta penceraian harus PDF, JPG, atau PNG.',
        ];
    }
}