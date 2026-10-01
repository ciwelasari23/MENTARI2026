<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrxTargetWilayah;
use App\Models\TrxLaporanProgres;
use App\Models\MstWilayah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $startParsed = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $endParsed = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        // 1. Ambil Data Laporan Berdasarkan Rentang Tanggal
        $laporans = $this->getLaporans($startParsed, $endParsed);

        // 2. Hitung Kartu Ringkasan (Summary Cards)
        $summary = $this->calculateSummary($laporans, $today);

        // 3. Ambil Data Target & Realisasi untuk Grafik
        $targets = TrxTargetWilayah::with(['proses', 'wilayah'])->get();
        $realisasiPerTarget = $this->getRealisasiPerTarget();
        $grafik = $this->getGrafikCapaian($targets, $realisasiPerTarget);

        // 4. Hitung Top 5 Progres Kegiatan
        $top5Targets = $this->getTop5Targets($laporans, $realisasiPerTarget, $today);

        // 5. Hitung Data Pie Chart Status Laporan
        $laporanPieData = $this->getLaporanPieData($laporans);

        return view('visualisasi.dashboard', array_merge([
            'top5Targets'    => $top5Targets,
            'laporanPieData' => $laporanPieData,
        ], $summary, $grafik));
    }

    private function getLaporans(?Carbon $start, ?Carbon $end): Collection
    {
        $query = TrxLaporanProgres::with(['targetWilayah.proses', 'targetWilayah.wilayah']);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()]);
        }

        return $query->get();
    }

    private function calculateSummary(Collection $laporans, Carbon $today): array
    {
        $totalKegiatan = $laporans->count();
        $totalSelesai = 0;
        $totalProses = 0;
        $totalTerlambat = 0;

        foreach ($laporans as $lap) {
            $deadline = optional(optional($lap->targetWilayah)->proses)->tanggal_selesai 
                ? Carbon::parse($lap->targetWilayah->proses->tanggal_selesai) 
                : null;

            $statusLaporan = $lap->status_laporan; // 'pending', 'approved', 'revision', 'rejected'
            $isSelesai = (bool) ($lap->is_selesai ?? false); 

            // 1. Jika sudah selesai atau disetujui penuh
            if ($isSelesai || $statusLaporan === 'approved') {
                $totalSelesai++;
            } 
            // 2. PRIORITAS: Jika status masih Diajukan (pending), Perlu Revisi (revision), atau Ditolak (rejected)
            elseif (in_array($statusLaporan, ['pending', 'revision', 'rejected'])) {
                $totalProses++;
            } 
            // 3. Jika melewati batas tanggal deadline
            elseif ($deadline && $today->greaterThan($deadline)) {
                $totalTerlambat++;
            } 
            // 4. Selebihnya masuk ke Dalam Proses
            else {
                $totalProses++;
            }
        }

        return compact('totalKegiatan', 'totalSelesai', 'totalProses', 'totalTerlambat');
    }

    private function getRealisasiPerTarget(): Collection
    {
        return TrxLaporanProgres::where('status_laporan', 'approved')
            ->select('id_target_wilayah', DB::raw('SUM(realisasi_saat_ini) as total_realisasi'))
            ->groupBy('id_target_wilayah')
            ->pluck('total_realisasi', 'id_target_wilayah');
    }

    private function getGrafikCapaian(Collection $targets, Collection $realisasiPerTarget): array
    {
        $kabKotaList = MstWilayah::select('kode_nama_kabkota')
            ->whereNotNull('kode_nama_kabkota')
            ->distinct()
            ->orderBy('kode_nama_kabkota')
            ->get();

        $grafikLabels = [];
        $grafikData   = [];

        foreach ($kabKotaList as $kabkota) {
            $namaKabKota = $kabkota->kode_nama_kabkota;
            
            $targetsWilayah = $targets->filter(function($target) use ($namaKabKota) {
                return $target->wilayah && $target->wilayah->kode_nama_kabkota === $namaKabKota;
            });

            $totalCapaian = 0;
            $count        = 0;

            if ($targetsWilayah->isNotEmpty()) {
                foreach ($targetsWilayah as $t) {
                    if ($t->target_daerah > 0) {
                        $real = $realisasiPerTarget[$t->id_target_wilayah] ?? 0;
                        $pct  = min(100, round(($real / $t->target_daerah) * 100, 1));
                        $totalCapaian += $pct;
                        $count++;
                    }
                }
            }

            $grafikLabels[] = $namaKabKota;
            $grafikData[] = $count > 0 ? round($totalCapaian / $count, 1) : 0;
        }

        return compact('grafikLabels', 'grafikData');
    }

    private function getTop5Targets(Collection $laporans, Collection $realisasiPerTarget, Carbon $today): Collection
    {
        return $laporans->sortByDesc('created_at')->take(5)->map(function ($lap) use ($realisasiPerTarget, $today) {
            $targetVal = optional($lap->targetWilayah)->target_daerah ?? 0;
            $realisasi = $realisasiPerTarget[$lap->id_target_wilayah] ?? $lap->realisasi_saat_ini;
            $pct = $targetVal > 0 ? min(100, round(($realisasi / $targetVal) * 100, 1)) : 0;
            
            $deadline = optional(optional($lap->targetWilayah)->proses)->tanggal_selesai 
                ? Carbon::parse($lap->targetWilayah->proses->tanggal_selesai) 
                : null;

            $isSelesai = (bool) ($lap->is_selesai ?? false);
            $statusLaporan = $lap->status_laporan;

            if ($isSelesai || $statusLaporan === 'approved') {
                $status = 'Selesai';
            } elseif (in_array($statusLaporan, ['pending', 'revision', 'rejected'])) {
                $status = 'Dalam Proses';
            } elseif ($deadline && $today->greaterThan($deadline)) {
                $status = 'Terlambat';
            } else {
                $status = 'Dalam Proses';
            }

            return [
                'nama_proses'  => optional(optional($lap->targetWilayah)->proses)->nama_proses ?? '-',
                'nama_wilayah' => optional($lap->targetWilayah)->wilayah ? trim((optional($lap->targetWilayah->wilayah)->nama_provinsi ?? '') . ' ' . (optional($lap->targetWilayah->wilayah)->kode_nama_kabkota ?? '')) : '-',
                'target'       => $targetVal,
                'realisasi'    => $realisasi,
                'pct'          => $pct,
                'status'       => $status,
            ];
        })->values();
    }

    private function getLaporanPieData(Collection $laporans): array
    {
        $statusLaporanCounts = $laporans->groupBy('status_laporan');

        return [
            optional($statusLaporanCounts->get('pending'))->count() ?? 0,
            optional($statusLaporanCounts->get('approved'))->count() ?? 0,
            optional($statusLaporanCounts->get('revision'))->count() ?? 0,
            optional($statusLaporanCounts->get('rejected'))->count() ?? 0,
        ];
    }
}