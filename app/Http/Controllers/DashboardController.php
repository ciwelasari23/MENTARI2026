<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrxTarget;
use App\Models\TrxLaporan;
use App\Models\MstKegiatanLevel4Proses;
use App\Models\MstWilayah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ============================================================
        // Ambil semua target beserta relasi proses & laporan approved
        // ============================================================
        $targets = TrxTarget::with([
            'proses',
            'wilayah',
        ])->get();

        // ============================================================
        // Hitung realisasi per target (jumlah laporan approved)
        // ============================================================
        $realisasiPerTarget = TrxLaporan::where('status_laporan', 'approved')
            ->select('id_target', DB::raw('SUM(realisasi_kuantiti) as total_realisasi'))
            ->groupBy('id_target')
            ->pluck('total_realisasi', 'id_target');

        // ============================================================
        // Klasifikasi Status setiap target:
        //   Selesai    = realisasi >= target_kuantiti
        //   Terlambat  = belum selesai DAN tanggal_selesai sudah lewat
        //   Dalam Proses = belum selesai DAN masih dalam waktu
        // ============================================================
        $totalKegiatan = 0;
        $totalSelesai  = 0;
        $totalProses   = 0;
        $totalTerlambat = 0;

        foreach ($targets as $target) {
            $realisasi   = $realisasiPerTarget[$target->id_target] ?? 0;
            $targetValue = $target->target_kuantiti;
            $deadline    = $target->proses ? Carbon::parse($target->proses->tanggal_selesai) : null;

            $totalKegiatan++;

            if ($realisasi >= $targetValue && $targetValue > 0) {
                $totalSelesai++;
            } elseif ($deadline && $today->greaterThan($deadline)) {
                $totalTerlambat++;
            } else {
                $totalProses++;
            }
        }

        // ============================================================
        // Grafik Bar: Rata-rata Capaian (%) per Kabupaten/Kota
        // ============================================================
        $wilayahList = MstWilayah::where('level_wilayah', 2)->get(); // level 2 = Kab/Kota

        $grafikLabels = [];
        $grafikData   = [];

        foreach ($wilayahList as $wilayah) {
            $targetsWilayah = $targets->where('id_wilayah', $wilayah->id_wilayah);

            if ($targetsWilayah->isEmpty()) continue;

            $totalCapaian = 0;
            $count        = 0;

            foreach ($targetsWilayah as $t) {
                if ($t->target_kuantiti > 0) {
                    $real = $realisasiPerTarget[$t->id_target] ?? 0;
                    $pct  = min(100, round(($real / $t->target_kuantiti) * 100, 1));
                    $totalCapaian += $pct;
                    $count++;
                }
            }

            if ($count > 0) {
                $grafikLabels[] = $wilayah->nama_wilayah;
                $grafikData[]   = round($totalCapaian / $count, 1);
            }
        }

        // ============================================================
        // Tabel Top 5: Target dengan capaian terbaru
        // ============================================================
        $top5Targets = TrxTarget::with(['proses.detail', 'wilayah'])
            ->get()
            ->map(function ($target) use ($realisasiPerTarget, $today) {
                $realisasi   = $realisasiPerTarget[$target->id_target] ?? 0;
                $targetValue = $target->target_kuantiti;
                $pct         = $targetValue > 0 ? min(100, round(($realisasi / $targetValue) * 100, 1)) : 0;
                $deadline    = $target->proses ? Carbon::parse($target->proses->tanggal_selesai) : null;

                if ($realisasi >= $targetValue && $targetValue > 0) {
                    $status = 'Selesai';
                } elseif ($deadline && $today->greaterThan($deadline)) {
                    $status = 'Terlambat';
                } else {
                    $status = 'Dalam Proses';
                }

                return [
                    'nama_proses'  => $target->proses->nama_proses ?? '-',
                    'nama_wilayah' => $target->wilayah->nama_wilayah ?? '-',
                    'target'       => $targetValue,
                    'realisasi'    => $realisasi,
                    'pct'          => $pct,
                    'status'       => $status,
                ];
            })
            ->sortByDesc('realisasi')
            ->take(5)
            ->values();

        return view('visualisasi.dashboard', compact(
            'totalKegiatan',
            'totalSelesai',
            'totalProses',
            'totalTerlambat',
            'grafikLabels',
            'grafikData',
            'top5Targets'
        ));
    }
}