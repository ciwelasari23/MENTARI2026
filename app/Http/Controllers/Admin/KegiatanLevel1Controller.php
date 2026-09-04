<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKegiatanLevel1Output;
use App\Models\MstTeam;

class KegiatanLevel1Controller extends Controller
{
public function index(Request $request)
    {
        $search = $request->input('search');
        $query = MstKegiatanLevel1Output::with('team');

        if ($search) {
            $query->where('nama_output', 'like', "%{$search}%");
        }

        $outputs = $query->get();
        $teams = MstTeam::all();
        
        // Ubah titik di sini agar mengarah ke folder level1/index.blade.php
        return view('admin.kegiatan.level1.index', compact('outputs', 'teams', 'search'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_output' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'id_team' => 'nullable|exists:mst_team,id_team',
        ]);

        MstKegiatanLevel1Output::create($request->all());

        return redirect()->route('admin.level1.index')->with('success', 'Output kegiatan berhasil ditambahkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'nama_output' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'id_team' => 'nullable|exists:mst_team,id_team',
        ]);

        $output = MstKegiatanLevel1Output::findOrFail($id);
        $output->update($request->all());

        return redirect()->route('admin.level1.index')->with('success', 'Output kegiatan berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        $output = MstKegiatanLevel1Output::findOrFail($id);
        $output->delete();

        return redirect()->route('admin.level1.index')->with('success', 'Output kegiatan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        MstKegiatanLevel1Output::whereIn('id_output', $request->ids)->delete();

        return redirect()->route('admin.level1.index')->with('success', count($request->ids) . ' data berhasil dihapus secara massal.');
    }
}