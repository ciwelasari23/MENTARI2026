<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxLaporanProgres;

class VerifikasiLaporanController extends Controller
{
    public function index()
    {
        // Memuat relasi lengkap termasuk targetWilayah agar data skor/penilaian terbaca
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

        // Jika request dikirim melalui AJAX (saat klik "Ya, Lanjutkan" pada persetujuan)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status verifikasi berhasil diperbarui.',
                'data' => $laporan
            ]);
        }

        return redirect()->route('admin.verifikasi.index')->with('success', 'Verifikasi laporan berhasil disimpan.');
    }
}