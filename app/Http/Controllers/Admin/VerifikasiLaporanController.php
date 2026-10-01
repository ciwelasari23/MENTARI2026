<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxLaporanProgres;
use Illuminate\Support\Facades\DB;

class VerifikasiLaporanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai perPage dari request, default 10 jika tidak diisi
        $perPage = $request->input('perPage', 10);

        // 1. Ambil ID laporan terbaru untuk baris utama tabel
        $latestIds = TrxLaporanProgres::select(DB::raw('MAX(id_laporan) as id'))
            ->groupBy('id_target_wilayah', 'id_user_pelapor')
            ->pluck('id');

        // 2. Memuat relasi lengkap untuk baris utama tabel dengan pagination
        $laporans = TrxLaporanProgres::with([
            'targetWilayah.proses.detail', 
            'targetWilayah.wilayah', 
            'pelapor', 
            'verifikator'
        ])
        ->whereIn('id_laporan', $latestIds)
        ->latest()
        ->paginate($perPage)
        ->withQueryString();
        
        // 3. Ambil SELURUH riwayat laporan dari database tanpa terkecuali, 
        // lalu kelompokkan berdasarkan kombinasi id_target_wilayah & id_user_pelapor
        $allLaporans = TrxLaporanProgres::with(['verifikator'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->id_target_wilayah . '-' . ($item->id_user_pelapor ?? 0);
            });
        
        return view('admin.verifikasi.index', compact('laporans', 'allLaporans'));
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