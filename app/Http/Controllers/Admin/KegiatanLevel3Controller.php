<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKegiatanLevel3Detail;
use App\Models\MstKegiatanLevel2Kegiatan;

class KegiatanLevel3Controller extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = MstKegiatanLevel3Detail::with('kegiatan');

        if ($search) {
            $query->where('nama_keg_detail', 'like', "%{$search}%");
        }

        $details = $query->get();
        $kegiatanList = MstKegiatanLevel2Kegiatan::all();
        
        return view('admin.kegiatan.level1.level3', compact('details', 'kegiatanList', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kegiatan' => 'required|exists:mst_kegiatan_level2_kegiatan,id_kegiatan',
            'nama_keg_detail' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_total' => 'required|integer|min:1',
            'satuan_target' => 'required|string|max:50',
        ]);

        MstKegiatanLevel3Detail::create($request->all());

        return redirect()->route('admin.level3.index')->with('success', 'Detail Kegiatan Level 3 berhasil ditambahkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'id_kegiatan' => 'required|exists:mst_kegiatan_level2_kegiatan,id_kegiatan',
            'nama_keg_detail' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_total' => 'required|integer|min:1',
            'satuan_target' => 'required|string|max:50',
        ]);

        $detail = MstKegiatanLevel3Detail::findOrFail($id);
        $detail->update($request->all());

        return redirect()->route('admin.level3.index')->with('success', 'Detail Kegiatan Level 3 berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        $detail = MstKegiatanLevel3Detail::findOrFail($id);
        $detail->delete();

        return redirect()->route('admin.level3.index')->with('success', 'Detail Kegiatan Level 3 berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        MstKegiatanLevel3Detail::whereIn('id_keg_detail', $request->ids)->delete();

        return redirect()->route('admin.level3.index')->with('success', count($request->ids) . ' data berhasil dihapus secara massal.');
    }
}