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
        return view('admin.master.timkerja', compact('teams', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_team' => 'required|string|max:255',
        ]);

        MstTeam::create($request->all());

        return redirect()->route('admin.timkerja.index')->with('success', 'Tim Kerja berhasil ditambahkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'nama_team' => 'required|string|max:255',
        ]);

        $team = MstTeam::findOrFail($id);
        $team->update($request->all());

        return redirect()->route('admin.timkerja.index')->with('success', 'Tim Kerja berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        $team = MstTeam::findOrFail($id);
        $team->delete();

        return redirect()->route('admin.timkerja.index')->with('success', 'Tim Kerja berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        MstTeam::whereIn('id_team', $request->ids)->delete();

        return redirect()->route('admin.timkerja.index')->with('success', count($request->ids) . ' Tim Kerja berhasil dihapus secara massal.');
    }
}