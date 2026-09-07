<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstTeam;

class TimKerjaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = MstTeam::query();

        if ($search) {
            $query->where('nama_team', 'like', "%{$search}%");
        }

        $teams = $query->get();
        return view('admin.master.timkerja', compact('teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_team' => 'required|string|max:100',
        ]);

        // Simpan data spesifik, jangan gunakan $request->all()
        MstTeam::create([
            'nama_team' => $request->nama_team
        ]);

        return redirect()->route('admin.timkerja.index')->with('success', 'Tim Kerja berhasil ditambahkan.');
    }

    public function update(Request $request, string|int $id)
    {
        $request->validate([
            'nama_team' => 'required|string|max:100',
        ]);

        // Update data spesifik
        MstTeam::findOrFail($id)->update([
            'nama_team' => $request->nama_team
        ]);

        return redirect()->route('admin.timkerja.index')->with('success', 'Tim Kerja berhasil diperbarui.');
    }

    public function destroy(string|int $id)
    {
        MstTeam::findOrFail($id)->delete();
        return redirect()->route('admin.timkerja.index')->with('success', 'Tim Kerja berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        
        // Ambil nama primary key secara dinamis
        $pk = (new MstTeam)->getKeyName(); 
        MstTeam::whereIn($pk, $request->ids)->delete();
        
        return redirect()->route('admin.timkerja.index')->with('success', 'Tim Kerja terpilih berhasil dihapus.');
    }
}