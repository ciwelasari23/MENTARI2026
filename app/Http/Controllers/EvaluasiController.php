<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EvaluasiController extends Controller
{
    public function index()
    {
        // Memanggil file resources/views/evaluasi/evaluasi.blade.php
        return view('evaluasi.evaluasi');
    }
}