<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrxTargetWilayah;
use App\Models\TrxLaporanProgres;
use App\Models\MstKegiatanLevel4Proses;
use App\Models\MstWilayah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        $periode = $request->query('periode');$startOfMonth = null;
        $endOfMonth = null;

        if ($periode) {
            $parts = explode('-',$periode);
            if (count($parts) == 2) {
                $year =$parts[0];
                $month =$parts[1];

                $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();$endOfMonth = Carbon::createFromDate($year,$month, 1)->endOfMonth();
            }
        }
        $targetsQuery = TrxTargetWilayah::with([
            'proses.detail', // Langsung load detail agar tabel Top 5 tidak melakukan query ulang
            'wilayah'
        ]);

        if ($startOfMonth &&$endOfMonth) {
            // Hanya ambil kegiatan yang rentang pelaksanaannya melewati bulan terpilih
            $targetsQuery->whereHas('proses', function($q) use ($startOfMonth,$endOfMonth) {
                $q->where('tanggal_mulai', '<=',$endOfMonth->toDateString())
                  ->where('tanggal_selesai', '>=', $startOfMonth->toDateString());
            });
        }

        $targets =$targetsQuery->get();

        $realisasiQuery = TrxLaporanProgres::where('status_laporan', 'approved')
            ->select('id_target_wilayah', DB::raw('SUM(realisasi_saat_ini) as total_realisasi'));

        if ($startOfMonth &&$endOfMonth) {
            // Catatan: Diasumsikan tanggal laporan dihitung berdasarkan 'created_at'.
            // Jika Anda punya field sendiri misal 'tanggal_laporan', ganti 'created_at' di bawah ini.
            $realisasiQuery->whereBetween('created_at', [
                $startOfMonth->startOfDay()->toDateTimeString(),$endOfMonth->endOfDay()->toDateTimeString()
            ]);
        }

        $realisasiPerTarget =$realisasiQuery->groupBy('id_target_wilayah')
            ->pluck('total_realisasi', 'id_target_wilayah');

        $totalKegiatan = 0;
        $totalSelesai  = 0;
        $totalProses   = 0;
        $totalTerlambat = 0;

        foreach ($targets as $target) {$realisasi   = $realisasiPerTarget[$target->id_target_wilayah] ?? 0;
            $targetValue =$target->target_daerah;
            $deadline    =$target->proses && isset($target->proses->tanggal_selesai) ? Carbon::parse($target->proses->tanggal_selesai) : null;

            $totalKegiatan++;

            if ($realisasi >=$targetValue && $targetValue > 0) {$totalSelesai++;
            } elseif ($deadline &&$today->greaterThan($deadline)) {$totalTerlambat++;
            } else {
                $totalProses++;
            }
        }

        $wilayahList = MstWilayah::where('level_wilayah', 2)->get(); // level 2 = Kab/Kota

        $grafikLabels = [];$grafikData   = [];

        foreach ($wilayahList as $wilayah) {$targetsWilayah = $targets->where('id_wilayah',$wilayah->id_wilayah);

            if ($targetsWilayah->isEmpty()) continue;

            $totalCapaian = 0;
            $count        = 0;

            foreach ($targetsWilayah as$t) {
                if ($t->target_daerah > 0) {$real = $realisasiPerTarget[$t->id_target_wilayah] ?? 0;
                    $pct  = min(100, round(($real / $t->target_daerah) * 100, 1));
                    $totalCapaian +=$pct;
                    $count++;
                }
            }

            if ($count > 0) {$grafikLabels[] = $wilayah->kode_nama_kabkota ?? 'Wilayah ' . $wilayah->id_wilayah;
                $grafikData[]   = round($totalCapaian / $count, 1);
            }
        }

        $top5Targets =$targets->map(function ($target) use ($realisasiPerTarget, $today) {$realisasi   = $realisasiPerTarget[$target->id_target_wilayah] ?? 0;
            $targetValue =$target->target_daerah;
            $pct         =$targetValue > 0 ? min(100, round(($realisasi / $targetValue) * 100, 1)) : 0;
            $deadline    =$target->proses && isset($target->proses->tanggal_selesai) ? Carbon::parse($target->proses->tanggal_selesai) : null;

            if ($realisasi >=$targetValue && $targetValue > 0) {$status = 'Selesai';
            } elseif ($deadline &&$today->greaterThan($deadline)) {$status = 'Terlambat';
            } else {
                $status = 'Dalam Proses';
            }

            return [
                'nama_proses'  => $target->proses->nama_proses ?? '-',
                'nama_wilayah' => isset($target->wilayah) ? trim(($target->wilayah->nama_provinsi ?? '') . ' ' . ($target->wilayah->kode_nama_kabkota ?? '')) : '-',
                'target'       => $targetValue,
                'realisasi'    => $realisasi,
                'pct'          => $pct,
                'status'       => $status,
            ];
        })
        ->sortByDesc('realisasi') // Urutkan berdasarkan yang realisasinya paling tinggi
        ->take(5)
        ->values();

        $statusLaporanQuery = TrxLaporanProgres::select('status_laporan', DB::raw('count(*) as total'));

        if ($startOfMonth && $endOfMonth) {$statusLaporanQuery->whereBetween('created_at', [
                $startOfMonth->startOfDay()->toDateTimeString(),$endOfMonth->endOfDay()->toDateTimeString()
            ]);
        }

        $statusLaporan =$statusLaporanQuery->groupBy('status_laporan')
            ->pluck('total', 'status_laporan');

        $laporanPieData = [
            $statusLaporan['pending'] ?? 0,   
            $statusLaporan['revision'] ?? 0,  
        ];

        return view('visualisasi.dashboard', compact(
            'totalKegiatan',
            'totalSelesai',
            'totalProses',
            'totalTerlambat',
            'grafikLabels',
            'grafikData',
            'top5Targets',
            'laporanPieData'
        ));
    }
}