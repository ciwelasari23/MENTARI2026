<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxTargetWilayah;
use App\Models\MstWilayah;
use App\Models\MstKegiatanLevel4Proses;
use Illuminate\Support\Facades\DB;

class TargetWilayahController extends Controller
{
    public function index()
    {
        $targets = TrxTargetWilayah::with(['wilayah', 'proses.detail', 'pembuat'])->get();
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

        $proses = MstKegiatanLevel4Proses::findOrFail($request->id_proses);
        $targetProvinsi = $proses->target_total_provinsi;

        // Hitung total target kabupaten/kota yang sudah ada untuk proses ini
        $totalTargetKabKota = TrxTargetWilayah::where('id_proses', $request->id_proses)->sum('target_daerah');
        
        $calonTotal = $totalTargetKabKota + $request->target_daerah;

        if ($calonTotal > $targetProvinsi) {
            $sisa = $targetProvinsi - $totalTargetKabKota;
            return back()->withErrors(['target_daerah' => 'Total target kabupaten/kota (' . $calonTotal . ') melebihi Target Total Provinsi (' . $targetProvinsi . '). Sisa kuota yang tersedia hanya: ' . $sisa . '.'])->withInput();
        }

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
        $proses = MstKegiatanLevel4Proses::findOrFail($request->id_proses);
        $targetProvinsi = $proses->target_total_provinsi;

        // Hitung total target lain selain data yang sedang diedit
        $totalTargetLain = TrxTargetWilayah::where('id_proses', $request->id_proses)
            ->where('id_target_wilayah', '!=', $id)
            ->sum('target_daerah');

        $calonTotal = $totalTargetLain + $request->target_daerah;

        if ($calonTotal > $targetProvinsi) {
            $sisa = $targetProvinsi - $totalTargetLain;
            return back()->withErrors(['target_daerah' => 'Akumulasi target kabupaten/kota (' . $calonTotal . ') melebihi Target Total Provinsi (' . $targetProvinsi . '). Sisa kuota maksimal: ' . $sisa . '.'])->withInput();
        }

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

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:trx_target_wilayah,id_target_wilayah' 
        ]);

        TrxTargetWilayah::whereIn('id_target_wilayah', $request->ids)->delete();

        return redirect()->route('admin.target.index')->with('success', count($request->ids) . ' target wilayah berhasil dihapus secara massal.');
    }

    /**
     * Download Template CSV untuk Import Target Wilayah
     */
    public function template()
    {
        $fileName = "template_target_wilayah.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Header kolom CSV
            fputcsv($file, ['id_proses', 'id_wilayah', 'target_daerah']);
            // Contoh baris data (sesuaikan ID Proses & Wilayah di database Anda)
            fputcsv($file, ['1', '101', '100']);
            fputcsv($file, ['1', '102', '150']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Proses Import File Target Wilayah (CSV/Excel) dengan Validasi Total Target
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Lewati baris header pertama
        $header = fgetcsv($handle, 1000, ',');
        
        $importedData = [];
        $prosesTargets = [];

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 3) continue;
            
            $id_proses = trim($row[0]);
            $id_wilayah = trim($row[1]);
            $target_daerah = (int) trim($row[2]);

            // Kelompokkan per id_proses untuk divalidasi akumulasinya
            if (!isset($prosesTargets[$id_proses])) {
                $prosesTargets[$id_proses] = 0;
            }
            $prosesTargets[$id_proses] += $target_daerah;

            $importedData[] = [
                'id_proses' => $id_proses,
                'id_wilayah' => $id_wilayah,
                'target_daerah' => $target_daerah,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        fclose($handle);

        // Validasi apakah total import per proses melebihi Target Total Provinsi
        foreach ($prosesTargets as $id_proses => $totalInput) {
            $proses = MstKegiatanLevel4Proses::find($id_proses);
            if (!$proses) {
                return back()->withErrors(['file' => 'ID Proses Kegiatan (' . $id_proses . ') tidak ditemukan di database.']);
            }

            // Hitung target kabupaten yang sudah ada sebelumnya di database untuk proses ini
            $existingTarget = TrxTargetWilayah::where('id_proses', $id_proses)->sum('target_daerah');
            $grandTotal = $existingTarget + $totalInput;

            if ($grandTotal > $proses->target_total_provinsi) {
                return back()->withErrors([
                    'file' => "Gagal Import! Total target untuk Proses ID {$id_proses} ({$grandTotal}) melebihi Target Provinsi ({$proses->target_total_provinsi})."
                ]);
            }
        }

        // Simpan data secara massal jika lolos validasi
        DB::transaction(function () use ($importedData) {
            foreach ($importedData as $data) {
                TrxTargetWilayah::create($data);
            }
        });

        return redirect()->route('admin.target.index')->with('success', 'Data target wilayah berhasil diimport.');
    }
}