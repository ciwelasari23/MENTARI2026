<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxLaporanProgres;
use Illuminate\Support\Facades\Auth;

class VerifikasiLaporanController extends Controller
{
    public function index()
    {
        // Menggunakan TrxLaporanProgres dan relasi yang sesuai (targetWilayah, pelapor)
        $laporans = TrxLaporanProgres::with(['targetWilayah.proses', 'targetWilayah.wilayah', 'pelapor'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.verifikasi.index', compact('laporans'));
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'status_laporan' => 'required|in:pending,approved,revision,rejected',
            'catatan_verifikator' => 'nullable|string|max:255',
        ]);

        $userId = Auth::user()->id_user ?? Auth::id();

        $laporan = TrxLaporanProgres::findOrFail($id);
        
        // Menyesuaikan dengan nama kolom di database
        $laporan->update([
            'status_laporan' => $request->status_laporan,
            'id_user_verifikator' => $userId,
            'catatan_verifikasi' => $request->catatan_verifikator,
        ]);

        return redirect()->route('admin.verifikasi.index')->with('success', 'Status laporan berhasil diperbarui.');
    }
}