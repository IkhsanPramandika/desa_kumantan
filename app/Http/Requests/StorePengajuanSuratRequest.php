<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanSuratRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ubah menjadi true agar request bisa diproses
    }

    public function rules()
    {
        return [
            'jenis_layanan' => 'required|in:' . implode(',', array_keys(\App\Models\PengajuanSurat::LAYANAN)),
            'data_tambahan' => 'required|array'
        ];
    }
}