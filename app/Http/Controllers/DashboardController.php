<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Arahkan ke folder visualisasi, lalu panggil file dashboard.blade.php
        return view('visualisasi.dashboard'); 
    }
}