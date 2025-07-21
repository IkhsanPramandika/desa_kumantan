<?php

namespace App\Http\Requests\Api\Permohonan\sk_kelahiran;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSKKelahiranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'nama_anak' => 'required|string|max:255',
            'tempat_lahir_anak' => 'required|string|max:255',
            'tanggal_lahir_anak' => 'required|date',
            'jenis_kelamin_anak' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'agama_anak' => ['required', Rule::in(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])],
            'alamat_anak' => 'required|string',
            'nama_ayah' => 'required|string|max:255',
            'nik_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'nik_ibu' => 'nullable|string|max:255',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_pengantar_rt_rw' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_nikah_orangtua' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_keterangan_kelahiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
            'nama_anak.required' => 'Nama anak wajib diisi.',
            'tempat_lahir_anak.required' => 'Tempat lahir anak wajib diisi.',
            'tanggal_lahir_anak.required' => 'Tanggal lahir anak wajib diisi.',
            'jenis_kelamin_anak.required' => 'Jenis kelamin anak wajib diisi.',
            'agama_anak.required' => 'Agama anak wajib diisi.',
            'alamat_anak.required' => 'Alamat anak wajib diisi.',
            'nama_ayah.required' => 'Nama ayah wajib diisi.',
            'nama_ibu.required' => 'Nama ibu wajib diisi.',
            'file_kk.mimes' => 'Format File KK harus PDF, JPG, atau PNG.',
            'file_kk.max' => 'Ukuran File KK maksimal 2MB.',
            'file_ktp.mimes' => 'Format File KTP harus PDF, JPG, atau PNG.',
            'file_ktp.max' => 'Ukuran File KTP maksimal 2MB.',
            'surat_pengantar_rt_rw.mimes' => 'Format Surat Pengantar harus PDF, JPG, atau PNG.',
            'surat_pengantar_rt_rw.max' => 'Ukuran Surat Pengantar maksimal 2MB.',
            'surat_nikah_orangtua.mimes' => 'Format Surat Nikah harus PDF, JPG, atau PNG.',
            'surat_nikah_orangtua.max' => 'Ukuran Surat Nikah maksimal 2MB.',
            'surat_keterangan_kelahiran.mimes' => 'Format Surat Keterangan Kelahiran harus PDF, JPG, atau PNG.',
            'surat_keterangan_kelahiran.max' => 'Ukuran Surat Keterangan Kelahiran maksimal 2MB.',
        ];
    }
}