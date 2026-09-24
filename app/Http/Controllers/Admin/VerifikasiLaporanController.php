<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxLaporanProgres;

class VerifikasiLaporanController extends Controller
{
    public function index()
    {
        // Ubah 'kegiatanLevel3' menjadi 'detail' agar sesuai dengan model
        $laporans = TrxLaporanProgres::with([
            'targetWilayah.proses.detail', 
            'targetWilayah.wilayah', 
            'pelapor', 
            'verifikator'
        ])->latest()->get();
        
        return view('admin.verifikasi.index', compact('laporans'));
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'status_laporan' => 'required|string',
            'catatan_verifikator' => 'nullable|string'
        ]);

        $laporan = TrxLaporanProgres::findOrFail($id);
        
        $laporan->status_laporan = $request->status_laporan;
        $laporan->catatan_verifikasi = $request->catatan_verifikator;
        
        if (auth()->check()) {
            $laporan->id_user_verifikator = auth()->id();
        }
        
        $laporan->save();

        return redirect()->route('admin.verifikasi.index')->with('success', 'Verifikasi laporan berhasil disimpan.');
    }
}