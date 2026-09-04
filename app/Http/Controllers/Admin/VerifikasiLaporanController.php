<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxLaporan;

class VerifikasiLaporanController extends Controller
{
    public function index()
    {
        $laporans = TrxLaporan::with(['target.proses', 'target.wilayah', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.verifikasi.index', compact('laporans'));
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'status_laporan' => 'required|in:approved,rejected',
            'catatan_verifikator' => 'nullable|string|max:255',
        ]);

        $laporan = TrxLaporan::findOrFail($id);
        $laporan->update([
            'status_laporan' => $request->status_laporan,
            'catatan_verifikator' => $request->catatan_verifikator,
        ]);

        return redirect()->route('admin.verifikasi.index')->with('success', 'Status laporan berhasil diperbarui.');
    }
}