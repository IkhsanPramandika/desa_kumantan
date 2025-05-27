<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('petugas.dashboard'); // Pastikan ini sesuai dengan nama file blade yang kamu inginkan
    }
}
