<?php
// Lokasi: app/Http/Controllers/Petugas/Dashboard/SearchController.php

namespace App\Http\Controllers\Petugas\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $results = new Collection();

        if ($query) {
            $searchableModels = [
                \App\Models\PermohonanKKBaru::class => ['catatan_pemohon'],
                \App\Models\PermohonanKKHilang::class => ['catatan_pemohon'],
                \App\Models\PermohonanKKPerubahanData::class => ['catatan_pemohon'],
                \App\Models\PermohonanSKDomisili::class => ['nama_pemohon_atau_lembaga', 'keperluan_domisili'],
                \App\Models\PermohonanSKKelahiran::class => ['nama_anak', 'nama_ayah', 'nama_ibu'],
                \App\Models\PermohonanSKPerkawinan::class => ['nama_pria', 'nama_wanita'],
                \App\Models\PermohonanSKTidakMampu::class => ['nama_terkait', 'keperluan_surat'],
                \App\Models\PermohonanSKUsaha::class => ['nama_usaha', 'alamat_usaha', 'keperluan_surat'],
                \App\Models\PermohonanSKAhliWaris::class => ['nama_pewaris'],
                \App\Models\PermohonanLainnya::class => ['judul_permohonan', 'keperluan', 'rincian_pemohon'],
            ];

            foreach ($searchableModels as $modelClass => $fields) {
                $modelQuery = $modelClass::query()->with('masyarakat');

                $modelQuery->where(function ($q) use ($fields, $query) {
                    // Mencari di dalam data permohonan itu sendiri
                    foreach ($fields as $field) {
                        $q->orWhere($field, 'like', '%' . $query . '%');
                    }
                   
                    $q->orWhereHas('masyarakat', function ($subQ) use ($query) {
                        $subQ->where('nama_lengkap', 'like', '%' . $query . '%')
                             ->orWhere('nik', 'like', '%' . $query . '%');
                    });
                });
                
                $results = $results->merge($modelQuery->get());
            }
            
            $results = $results->sortByDesc('created_at');
        }

        return view('search.results', compact('query', 'results'));
    }
}
