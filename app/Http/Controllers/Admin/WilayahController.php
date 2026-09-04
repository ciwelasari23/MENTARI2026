<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MstWilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = MstWilayah::query();

        if ($search) {
            $query->where('nama_wilayah', 'like', "%{$search}%")
                  ->orWhere('kode_wilayah', 'like', "%{$search}%")
                  ->orWhere('id_wilayah', 'like', "%{$search}%");
        }

        $wilayahs = $query->orderBy('id_wilayah', 'desc')->get();

        return view('admin.master.wilayah', compact('wilayahs', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_wilayah' => 'required|string|max:16|unique:mst_wilayah,id_wilayah',
            'kode_wilayah' => 'required|string|max:50',
            'nama_wilayah' => 'required|string|max:100',
            'level_wilayah' => 'required|integer',
        ]);

        MstWilayah::create($request->all());

        return redirect()->route('admin.wilayah.index')->with('success', 'Data wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $wilayah = MstWilayah::findOrFail($id);

        $request->validate([
            'id_wilayah' => 'required|string|max:16|unique:mst_wilayah,id_wilayah,' . $id . ',id_wilayah',
            'kode_wilayah' => 'required|string|max:50',
            'nama_wilayah' => 'required|string|max:100',
            'level_wilayah' => 'required|integer',
        ]);

        $wilayah->update($request->all());

        return redirect()->route('admin.wilayah.index')->with('success', 'Data wilayah berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        $wilayah = MstWilayah::findOrFail($id);
        $wilayah->delete();

        return redirect()->route('admin.wilayah.index')->with('success', 'Data wilayah berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');

        if ($ids) {
            MstWilayah::whereIn('id_wilayah', $ids)->delete();
            return redirect()->route('admin.wilayah.index')->with('success', 'Data wilayah terpilih berhasil dihapus.');
        }

        return redirect()->route('admin.wilayah.index')->with('error', 'Tidak ada data yang dipilih.');
    }
}