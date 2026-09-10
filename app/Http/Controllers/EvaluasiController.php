<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MstKegiatanLevel2Kegiatan;
use App\Models\MstKegiatanLevel3Detail;
use App\Models\MstKegiatanLevel4Proses;
use App\Models\TrxTarget;
use App\Models\TrxLaporan;
use Illuminate\Support\Facades\DB;

class EvaluasiController extends Controller
{
    public function index()
    {
        // Ambil semua proses kegiatan beserta relasi induknya
        $prosesAll = MstKegiatanLevel4Proses::with([
            'detail.kegiatan.output'
        ])->get();

        $evaluasiData = [];

        foreach ($prosesAll as $proses) {
            // Hitung total target dari semua wilayah untuk proses ini
            $totalTarget = TrxTarget::where('id_proses', $proses->id_proses)
                ->sum('target_kuantiti');

            // Hitung total realisasi yang sudah disetujui (approved)
            $totalRealisasi = TrxLaporan::whereHas('target', function ($q) use ($proses) {
                    $q->where('id_proses', $proses->id_proses);
                })
                ->where('status_laporan', 'approved')
                ->sum('realisasi_kuantiti');

            // Hitung persentase capaian (max 100 untuk bintang, bisa >100 untuk angka)
            $persentase = ($totalTarget > 0)
                ? round(($totalRealisasi / $totalTarget) * 100, 1)
                : 0;

            // Konversi persentase ke rating bintang (1–5)
            $rating = $this->hitungRating($persentase);

            $evaluasiData[] = [
                'proses'         => $proses,
                'nama_kegiatan'  => $proses->detail->kegiatan->nama_kegiatan ?? '-',
                'nama_detail'    => $proses->detail->nama_keg_detail ?? '-',
                'total_target'   => $totalTarget,
                'total_realisasi'=> $totalRealisasi,
                'persentase'     => $persentase,
                'rating'         => $rating,
            ];
        }

        // Hitung rating keseluruhan (rata-rata persentase semua proses)
        $rataPersentase = count($evaluasiData) > 0
            ? round(collect($evaluasiData)->avg('persentase'), 1)
            : 0;
        $ratingKeseluruhan = $this->hitungRating($rataPersentase);

        return view('evaluasi.evaluasi', compact('evaluasiData', 'rataPersentase', 'ratingKeseluruhan'));
    }

    /**
     * Hitung rating bintang (1–5) berdasarkan persentase capaian.
     * ≥100% → 5 bintang
     * 85–99% → 4 bintang
     * 70–84% → 3 bintang
     * 50–69% → 2 bintang
     * <50%   → 1 bintang
     */
    private function hitungRating(float $persentase): int
    {
        if ($persentase >= 100) return 5;
        if ($persentase >= 85)  return 4;
        if ($persentase >= 70)  return 3;
        if ($persentase >= 50)  return 2;
        return 1;
    }
}