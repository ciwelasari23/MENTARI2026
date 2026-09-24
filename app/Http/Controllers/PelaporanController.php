<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrxLaporanProgres;
use App\Models\TrxTargetWilayah;
use Illuminate\Support\Facades\Auth;

class PelaporanController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id_user ?? Auth::id();
        
        // PERBAIKAN: Tambahkan '.detail' pada targetWilayah.proses
        $laporanGrouped = TrxLaporanProgres::with(['targetWilayah.proses.detail', 'targetWilayah.wilayah'])
            ->where('id_user_pelapor', $user_id)
            ->select('id_target_wilayah')
            ->selectRaw('MAX(tanggal_lapor) as tanggal_terakhir')
            ->selectRaw('SUM(realisasi_saat_ini) as total_capaian')
            ->groupBy('id_target_wilayah')
            ->orderBy('tanggal_terakhir', 'desc')
            ->get();

        // PERBAIKAN: Tambahkan juga '.detail' pada riwayat history
        $laporanHistory = TrxLaporanProgres::with(['targetWilayah.proses.detail', 'targetWilayah.wilayah'])
            ->where('id_user_pelapor', $user_id)
            ->orderBy('tanggal_lapor', 'desc')
            ->get();
             
        $targets = TrxTargetWilayah::with(['proses.detail', 'wilayah'])->get();
        
        return view('pelaporan.pelaporan', compact('laporanGrouped', 'laporanHistory', 'targets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_target_wilayah' => 'required|exists:trx_target_wilayah,id_target_wilayah',
            'tanggal_lapor' => 'required|date',
            'realisasi_kuantiti' => 'required|integer|min:1|max:9223372036854775807',
            'link_bukti' => 'nullable|url|max:255',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1024',
        ]);

        $userId = Auth::user()->id_user ?? Auth::id();
        abort_unless($userId, 403);

        $dataSimpan = [
            'id_target_wilayah' => $request->id_target_wilayah,
            'id_user_pelapor' => $userId,
            'tanggal_lapor' => $request->tanggal_lapor,
            'realisasi_saat_ini' => $request->realisasi_kuantiti,
            'link_bukti' => $request->link_bukti,
            'status_laporan' => 'pending',
        ];

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $filename = time() . '_' . $file->hashName();
            $path = $file->storeAs('uploads/bukti', $filename, 'public');

            abort_unless($path, 500, 'File bukti gagal disimpan.');

            $dataSimpan['path_bukti_dukung'] = $path;
            $dataSimpan['file_bukti'] = basename($path);
        }

        TrxLaporanProgres::create($dataSimpan);

        return redirect()->route('pelaporan.index')->with('success', 'Laporan berhasil dikirim dan menunggu verifikasi.');
    }
}