<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKegiatanLevel2Kegiatan;
use App\Models\MstKegiatanLevel1Output;

class KegiatanLevel2Controller extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = MstKegiatanLevel2Kegiatan::with('output');

        if ($search) {
            $query->where('nama_kegiatan', 'like', "%{$search}%");
        }

        $kegiatan = $query->get();
        $outputs = MstKegiatanLevel1Output::all();
        
        return view('admin.kegiatan.level1.level2', compact('kegiatan', 'outputs', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_output' => 'required|exists:mst_kegiatan_level1_output,id_output',
            'nama_kegiatan' => 'required|string|max:255',
        ]);

        MstKegiatanLevel2Kegiatan::create($request->all());

        return redirect()->route('admin.level2.index')->with('success', 'Kegiatan Level 2 berhasil ditambahkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'id_output' => 'required|exists:mst_kegiatan_level1_output,id_output',
            'nama_kegiatan' => 'required|string|max:255',
        ]);

        $kegiatan = MstKegiatanLevel2Kegiatan::findOrFail($id);
        $kegiatan->update($request->all());

        return redirect()->route('admin.level2.index')->with('success', 'Kegiatan Level 2 berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        $kegiatan = MstKegiatanLevel2Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return redirect()->route('admin.level2.index')->with('success', 'Kegiatan Level 2 berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        MstKegiatanLevel2Kegiatan::whereIn('id_kegiatan', $request->ids)->delete();

        return redirect()->route('admin.level2.index')->with('success', count($request->ids) . ' data berhasil dihapus secara massal.');
    }
}