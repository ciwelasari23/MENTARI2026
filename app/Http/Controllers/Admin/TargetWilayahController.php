<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxTarget;
use App\Models\MstKegiatanLevel4Proses;
use App\Models\MstWilayah;

class TargetWilayahController extends Controller
{
    public function index()
    {
        $targets = TrxTarget::with(['proses', 'wilayah'])->get();
        $prosesList = MstKegiatanLevel4Proses::all();
        $wilayahList = MstWilayah::all();
        return view('admin.target.index', compact('targets', 'prosesList', 'wilayahList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_proses' => 'required|exists:mst_kegiatan_level4_proses,id_proses',
            'id_wilayah' => 'required|exists:mst_wilayah,id_wilayah',
            'target_kuantiti' => 'required|integer|min:1',
        ]);

        TrxTarget::create($request->all());

        return redirect()->route('admin.target.index')->with('success', 'Target wilayah berhasil ditetapkan.');
    }

    public function destroy(int|string $id)
    {
        $target = TrxTarget::findOrFail($id);
        $target->delete();

        return redirect()->route('admin.target.index')->with('success', 'Target wilayah berhasil dihapus.');
    }
}