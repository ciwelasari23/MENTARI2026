<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrxLaporan;
use App\Models\TrxTarget;
use Illuminate\Support\Facades\Auth;

class PelaporanController extends Controller
{
    public function index()
    {
        // Hanya menampilkan laporan milik user yang sedang login
        $user_id = Auth::user()->id_user ?? Auth::id(); // Sesuaikan dengan PK tabel user Anda
        
        $laporan = TrxLaporan::with(['target.proses', 'target.wilayah'])
            ->where('id_user', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $targets = TrxTarget::with(['proses', 'wilayah'])->get();
        
        // UBAH BARIS INI: dari pelaporan.index menjadi pelaporan.pelaporan
        return view('pelaporan.pelaporan', compact('laporan', 'targets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_target' => 'required|exists:trx_target,id_target',
            'tanggal_lapor' => 'required|date',
            'realisasi_kuantiti' => 'required|integer|min:1',
            'link_bukti' => 'nullable|url|max:255',
        ]);

        $data = $request->all();
        $data['id_user'] = Auth::user()->id_user ?? Auth::id(); // Ambil ID user otomatis
        $data['status_laporan'] = 'pending'; // Default saat pertama kali lapor

        TrxLaporan::create($data);

        return redirect()->route('pelaporan.index')->with('success', 'Laporan berhasil dikirim dan menunggu verifikasi.');
    }
}