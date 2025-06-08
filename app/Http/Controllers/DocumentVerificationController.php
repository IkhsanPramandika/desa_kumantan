<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermohonananSKKelahiran;

class DocumentVerificationController extends Controller
    {
        public function verify($id)
        {
            $permohonan = PermohonananSKKelahiran::find($id);

            if (!$permohonan) {
                return view('verification.not_found'); // Tampilkan halaman dokumen tidak ditemukan
            }

            // Tampilkan detail permohonan atau status verifikasi
            return view('verification.show', compact('permohonan'));
        }
    }