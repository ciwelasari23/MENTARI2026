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
        $validated = $request->validate([
            'id_target' => 'required|exists:trx_target,id_target',
            'tanggal_lapor' => 'required|date',
            'realisasi_kuantiti' => 'required|integer|min:1',
            'link_bukti' => 'nullable|url|max:255',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1024',
        ]);

        $userId = Auth::id();
        abort_unless($userId, 403);

        $validated['id_user'] = $userId;
        $validated['status_laporan'] = 'pending';

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/bukti'), $filename);
            $validated['file_bukti'] = 'uploads/bukti/' . $filename;
        }

        TrxLaporan::create($validated);

        return redirect()->route('pelaporan.index')->with('success', 'Laporan berhasil dikirim dan menunggu verifikasi.');
    }
}