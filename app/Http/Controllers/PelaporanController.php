<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrxLaporanProgres;
use App\Models\TrxTargetWilayah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelaporanController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::user()->id_user ?? Auth::id();
        
        $perPage = $request->input('perPage', 10);
        
        // Mengambil laporan terbaru (berdasarkan id_laporan terbesar / paling akhir) per id_target_wilayah
        $laporanGrouped = TrxLaporanProgres::with(['targetWilayah.proses.detail', 'targetWilayah.wilayah'])
            ->where('id_user_pelapor', $user_id)
            ->whereIn('id_laporan', function($query) use ($user_id) {
                $query->select(DB::raw('MAX(id_laporan)'))
                      ->from('trx_laporan_progres')
                      ->where('id_user_pelapor', $user_id)
                      ->groupBy('id_target_wilayah');
            })
            ->orderBy('tanggal_lapor', 'desc')
            ->paginate($perPage)
            ->withQueryString();

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