<?php

namespace App\Http\Controllers\Petugas\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\PermohonanSKKelahiran;

class DocumentVerificationController extends Controller
    {
        public function verify($id)
        {
            $permohonan = PermohonanSKKelahiran::find($id);

            if (!$permohonan) {
                return view('verification.not_found'); // Tampilkan halaman dokumen tidak ditemukan
            }

            // Tampilkan detail permohonan atau status verifikasi
            return view('verification.show', compact('permohonan'));
        }
    }