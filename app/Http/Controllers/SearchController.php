<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Import model-model yang mungkin ingin Anda cari
// use App\Models\PermohonananKKBaru;
// use App\Models\PermohonananSKUsaha;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query'); // Mengambil nilai dari input pencarian

        // Lakukan logika pencarian Anda di sini
        // Contoh: Mencari permohonan KK Baru berdasarkan catatan atau nama file
        // $results = PermohonananKKBaru::where('catatan', 'like', '%' . $query . '%')
        //                             ->orWhere('file_kk', 'like', '%' . $query . '%')
        //                             ->get();

        // Atau Anda bisa mencari di beberapa model dan menggabungkan hasilnya
        $results = collect(); // Koleksi kosong untuk menampung hasil

        if ($query) {
            // Contoh pencarian di PermohonananKKBaru
            $kkBaruResults = \App\Models\PermohonananKKBaru::where('catatan', 'like', '%' . $query . '%')
                                ->orWhere('file_kk', 'like', '%' . $query . '%')
                                ->get();
            $results = $results->merge($kkBaruResults);

            // Contoh pencarian di PermohonananSKUsaha
            $skUsahaResults = \App\Models\PermohonananSKUsaha::where('nama_usaha', 'like', '%' . $query . '%')
                                ->orWhere('alamat_usaha', 'like', '%' . $query . '%')
                                ->get();
            $results = $results->merge($skUsahaResults);

            // Anda bisa menambahkan logika pencarian untuk model lain di sini
        }


        // Kemudian, tampilkan hasilnya di view
        return view('search.results', compact('query', 'results')); // Anda perlu membuat view ini
    }
}