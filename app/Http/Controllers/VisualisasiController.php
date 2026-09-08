<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VisualisasiController extends Controller
{
    public function index()
    {
        // Nantinya data grafik dan tabel diambil dari database di sini
        return view('visualisasi.index');
    }
}