<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKegiatanLevel4Proses;
use App\Models\MstKegiatanLevel3Detail;

class KegiatanLevel4Controller extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = MstKegiatanLevel4Proses::with('detail');

        if ($search) {
            $query->where('nama_proses', 'like', "%{$search}%");
        }

        $prosesList = $query->get();
        $details = MstKegiatanLevel3Detail::all();
        
        return view('admin.kegiatan.level1.level4', compact('prosesList', 'details', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_keg_detail' => 'required|exists:mst_kegiatan_level3_detail,id_keg_detail',
            'nama_proses' => 'required|string|max:255',
            'satuan_target' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_total_provinsi' => 'required|integer|min:1',
        ]);

        MstKegiatanLevel4Proses::create($request->all());

        return redirect()->route('admin.level4.index')->with('success', 'Proses Kegiatan Level 4 berhasil ditambahkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'id_keg_detail' => 'required|exists:mst_kegiatan_level3_detail,id_keg_detail',
            'nama_proses' => 'required|string|max:255',
            'satuan_target' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_total_provinsi' => 'required|integer|min:1',
        ]);

        $proses = MstKegiatanLevel4Proses::findOrFail($id);
        $proses->update($request->all());

        return redirect()->route('admin.level4.index')->with('success', 'Proses Kegiatan Level 4 berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        $proses = MstKegiatanLevel4Proses::findOrFail($id);
        $proses->delete();

        return redirect()->route('admin.level4.index')->with('success', 'Proses Kegiatan Level 4 berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        MstKegiatanLevel4Proses::whereIn('id_proses', $request->ids)->delete();

        return redirect()->route('admin.level4.index')->with('success', count($request->ids) . ' data berhasil dihapus secara massal.');
    }
}