<?php

namespace App\Http\Requests\Api\Permohonan\sk_ahli_waris;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreSKAhliWarisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'nama_pewaris' => 'required|string|max:255',
            'nik_pewaris' => 'required|string|max:16',
            'tempat_lahir_pewaris' => 'required|string|max:255',
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

    public function messages(): array
    {
        return [
            'nama_pewaris.required' => 'Nama pewaris wajib diisi.',
            'nik_pewaris.required' => 'NIK pewaris wajib diisi.',
            'tempat_lahir_pewaris.required' => 'Tempat lahir pewaris wajib diisi.',
            'tanggal_lahir_pewaris.required' => 'Tanggal lahir pewaris wajib diisi.',
            'tanggal_meninggal_pewaris.required' => 'Tanggal meninggal pewaris wajib diisi.',
            'alamat_pewaris.required' => 'Alamat pewaris wajib diisi.',
            'daftar_ahli_waris.required' => 'Daftar ahli waris wajib diisi.',
            'daftar_ahli_waris.json' => 'Daftar ahli waris harus dalam format JSON.',
            'file_kk_pemohon.mimes' => 'Format File KK Pemohon harus PDF, JPG, atau PNG.',
            'file_ktp_pemohon.mimes' => 'Format File KTP Pemohon harus PDF, JPG, atau PNG.',
            'file_kk_ahli_waris.mimes' => 'Format File KK Ahli Waris harus PDF, JPG, atau PNG.',
            'file_ktp_ahli_waris.mimes' => 'Format File KTP Ahli Waris harus PDF, JPG, atau PNG.',
            'surat_pengantar_rt_rw.mimes' => 'Format Surat Pengantar harus PDF, JPG, atau PNG.',
            'surat_keterangan_kematian.mimes' => 'Format Surat Kematian harus PDF, JPG, atau PNG.',
        ];
    }
}