<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxLaporanProgres;

class VerifikasiLaporanController extends Controller
{
    public function index()
    {
        $laporans = TrxLaporanProgres::with(['targetWilayah.proses', 'targetWilayah.wilayah', 'pelapor', 'verifikator'])->latest()->get();
        return view('admin.verifikasi.index', compact('laporans'));
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'status_laporan' => 'required|string',
            'catatan_verifikator' => 'nullable|string' // Nama input dari form modal
        ]);

        $laporan = TrxLaporanProgres::findOrFail($id);
        
        $laporan->status_laporan = $request->status_laporan;
        $laporan->catatan_verifikasi = $request->catatan_verifikator; // Disimpan ke kolom asli database: catatan_verifikasi
        
        if (auth()->check()) {
            $laporan->id_user_verifikator = auth()->id(); // Disimpan ke kolom asli database: id_user_verifikator
        }
        
        $laporan->save();

        return redirect()->route('admin.verifikasi.index')->with('success', 'Verifikasi laporan berhasil disimpan.');
    }
}