<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstWilayah;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = MstWilayah::query();

        if ($search) {
            $query->where('nama_wilayah', 'like', "%{$search}%")
                  ->orWhere('id_wilayah', 'like', "%{$search}%");
        }

        $wilayahs = $query->get();
        return view('admin.master.wilayah', compact('wilayahs'));
    }

    public function store(Request $request)
    {
        // 1. Validasi tanpa kode_wilayah
        $request->validate([
            'id_wilayah' => 'required|string|max:16|unique:mst_wilayah,id_wilayah',
            'nama_wilayah' => 'required|string|max:100',
            'level_wilayah' => 'required|integer',
        ]);

        // 2. Simpan ke database
        MstWilayah::create([
            'id_wilayah' => $request->id_wilayah,
            'nama_wilayah' => $request->nama_wilayah,
            'level_wilayah' => $request->level_wilayah,
        ]);

        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_wilayah' => 'required|string|max:100',
            'level_wilayah' => 'required|integer',
        ]);

        MstWilayah::findOrFail($id)->update([
            'nama_wilayah' => $request->nama_wilayah,
            'level_wilayah' => $request->level_wilayah,
        ]);

        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        MstWilayah::findOrFail($id)->delete();
        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        MstWilayah::whereIn('id_wilayah', $request->ids)->delete();
        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah terpilih berhasil dihapus.');
    }
}