<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxTargetWilayah;
use App\Models\MstWilayah;
use App\Models\MstKegiatanLevel4Proses;

class TargetWilayahController extends Controller
{
    public function index()
    {
        $targets = TrxTargetWilayah::with(['wilayah', 'proses', 'pembuat'])->get();
        $wilayahs = MstWilayah::all();
        $prosesList = MstKegiatanLevel4Proses::all();

        return view('admin.target.index', compact('targets', 'wilayahs', 'prosesList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_wilayah' => 'required',
            'id_proses' => 'required|integer',
            'target_daerah' => 'required|integer|min:1',
        ]);

        TrxTargetWilayah::create([
            'id_wilayah' => $request->id_wilayah,
            'id_proses' => $request->id_proses,
            'target_daerah' => $request->target_daerah,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.target.index')->with('success', 'Target wilayah berhasil ditetapkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'id_wilayah' => 'required',
            'id_proses' => 'required|integer',
            'target_daerah' => 'required|integer|min:1',
        ]);

        $target = TrxTargetWilayah::findOrFail($id);
        $target->update([
            'id_wilayah' => $request->id_wilayah,
            'id_proses' => $request->id_proses,
            'target_daerah' => $request->target_daerah,
        ]);

        return redirect()->route('admin.target.index')->with('success', 'Target wilayah berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        TrxTargetWilayah::findOrFail($id)->delete();
        return redirect()->route('admin.target.index')->with('success', 'Target wilayah berhasil dihapus.');
    }

    /**
     * Menghapus beberapa target wilayah sekaligus (Bulk Delete)
     */
    public function bulkDestroy(Request $request)
    {
        // Validasi input berupa array ID
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:trx_target_wilayah,id_target_wilayah' 
        ]);

        // Hapus data berdasarkan array ID yang dikirim
        TrxTargetWilayah::whereIn('id_target_wilayah', $request->ids)->delete();

        // Redirect kembali dengan pesan sukses beserta jumlah data yang dihapus
        return redirect()->route('admin.target.index')->with('success', count($request->ids) . ' target wilayah berhasil dihapus secara massal.');
    }
}