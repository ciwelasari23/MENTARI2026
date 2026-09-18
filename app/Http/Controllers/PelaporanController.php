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
        
        $laporan = TrxLaporanProgres::with(['targetWilayah.proses', 'targetWilayah.wilayah'])
            ->where('id_user_pelapor', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();
             
        $targets = TrxTargetWilayah::with(['proses', 'wilayah'])->get();
        
        return view('pelaporan.pelaporan', compact('laporan', 'targets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_target_wilayah' => 'required|exists:trx_target_wilayah,id_target_wilayah',
            'tanggal_lapor' => 'required|date',
            'realisasi_kuantiti' => 'required|integer|min:1',
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
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/bukti'), $filename);
            $dataSimpan['path_bukti_dukung'] = 'uploads/bukti/' . $filename;
            $dataSimpan['file_bukti'] = $filename;
        }

        TrxLaporanProgres::create($dataSimpan);

        return redirect()->route('pelaporan.index')->with('success', 'Laporan berhasil dikirim dan menunggu verifikasi.');
    }
}