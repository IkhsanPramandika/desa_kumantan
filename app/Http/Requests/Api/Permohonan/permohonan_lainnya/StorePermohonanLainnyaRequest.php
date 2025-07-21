<?php

namespace App\Http\Requests\Api\Permohonan\permohonan_lainnya;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StorePermohonanLainnyaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'judul_permohonan' => 'required|string|max:255',
            'keperluan'         => 'required|string|max:1000',
            'rincian_pemohon'   => 'required|string|max:5000',
            'lampiran'   => 'nullable|array', // Pastikan 'lampiran' adalah sebuah array
            'lampiran.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048', // Validasi setiap item di dalam array
            'draft_id'          => 'nullable|exists:permohonan_lainnyas,id',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Data yang diberikan tidak valid.',
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'lampiran.*.mimes' => 'Format setiap file lampiran harus PDF, JPG, atau PNG.',
            'lampiran.*.max'   => 'Ukuran setiap file lampiran maksimal 2MB.',
        ];
    }
}