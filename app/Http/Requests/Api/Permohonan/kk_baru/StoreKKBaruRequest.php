<?php

// Lokasi file: app/Http/Requests/Api/Permohonan/KkBaru/StoreKKBaruRequest.php
namespace App\Http\Requests\Api\Permohonan\kk_baru; // Namespace sesuai struktur folder Anda

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Untuk Sanctum guard

class StoreKKBaruRequest extends FormRequest 
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Izinkan jika pengguna terautentikasi melalui guard 'sanctum' (untuk API masyarakat)
        return Auth::guard('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validasi berdasarkan field di model PermohonananKKBaru
            // Sesuaikan 'required' atau 'nullable' berdasarkan kebutuhan form pengajuan KK Baru
            'file_kk'                 => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // KK lama atau KK orang tua (jika pisah KK)
            'file_ktp'                => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // KTP Pemohon (Kepala Keluarga baru)
            'surat_pengantar_rt_rw'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'buku_nikah_akta_cerai'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Opsional, tergantung kasus
            'surat_pindah_datang'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Opsional, jika ada anggota yang pindah masuk
            'ijazah_terakhir'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Opsional, mungkin untuk data pendidikan anggota keluarga
            'catatan'                 => 'nullable|string|max:1000', // Catatan dari pemohon

            // Tambahkan validasi untuk field data teks lain yang mungkin dikirim dari Flutter, contoh:
            // 'nama_kepala_keluarga' => 'required|string|max:255',
            // 'alamat_lengkap' => 'required|string|max:500',
            // 'rt' => 'required|string|max:3',
            // 'rw' => 'required|string|max:3',
            // 'kode_pos' => 'nullable|string|max:10',
            // 'alasan_permohonan' => 'nullable|string|max:255', // Jika ada field ini di form
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'file_kk.required' => 'File Kartu Keluarga wajib diunggah.',
            'file_kk.mimes' => 'Format File Kartu Keluarga harus PDF, JPG, JPEG, atau PNG.',
            'file_kk.max' => 'Ukuran File Kartu Keluarga maksimal 2MB.',
            'file_ktp.required' => 'File KTP Pemohon wajib diunggah.',
            'file_ktp.mimes' => 'Format File KTP Pemohon harus PDF, JPG, JPEG, atau PNG.',
            'file_ktp.max' => 'Ukuran File KTP Pemohon maksimal 2MB.',
            'surat_pengantar_rt_rw.required' => 'Surat Pengantar RT/RW wajib diunggah.',
            'surat_pengantar_rt_rw.mimes' => 'Format Surat Pengantar RT/RW harus PDF, JPG, JPEG, atau PNG.',
            'surat_pengantar_rt_rw.max' => 'Ukuran Surat Pengantar RT/RW maksimal 2MB.',
            // Tambahkan pesan kustom untuk validasi field lain jika perlu
        ];
    }
}
