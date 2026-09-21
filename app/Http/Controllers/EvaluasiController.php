<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrxTargetWilayah; // Perbaikan di sini
use Illuminate\Support\Facades\DB;

class EvaluasiController extends Controller
{

    public function index()
    {
        $dataEvaluasi = $this->getEvaluasiData();

        return view('evaluasi.evaluasi', [
            'evaluasiData'      => $dataEvaluasi['evaluasiData'],
            'rataPersentase'    => $dataEvaluasi['rataPersentase'],
            'ratingKeseluruhan' => $dataEvaluasi['ratingKeseluruhan'],
        ]);
    }

    /**
     * Export rekapitulasi evaluasi kegiatan ke format PDF.
     */
    public function exportPdf()
    {
        $dataEvaluasi = $this->getEvaluasiData();

        $pdf = app('dompdf.wrapper')->loadView('evaluasi.pdf_template', [
            'evaluasiData'      => $dataEvaluasi['evaluasiData'],
            'rataPersentase'    => $dataEvaluasi['rataPersentase'],
            'ratingKeseluruhan' => $dataEvaluasi['ratingKeseluruhan'],
            'tanggalCetak'      => now()->translatedFormat('d F Y'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Evaluasi_MENTARI_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Mengambil data evaluasi berdasarkan per-target wilayah (id_target_wilayah).
     */
    private function getEvaluasiData(): array
    {
        // Perbaikan di sini menggunakan TrxTargetWilayah
        $targets = TrxTargetWilayah::with([
            'proses.detail.kegiatan.output',
            'wilayah',
            'laporans'
        ])->get();

        $evaluasiData = [];

        foreach ($targets as $target) {
            if (!$target->proses) {
                continue;
            }
            $evaluasiData[] = $this->kalkulasiSkorTarget($target);
        }

        $rataPersentase = count($evaluasiData) > 0
            ? (float) round(collect($evaluasiData)->avg('total_skor'), 1)
            : 0;

        $ratingKeseluruhan = $this->hitungRating((int) round($rataPersentase));

        return [
            'evaluasiData'      => $evaluasiData,
            'rataPersentase'    => $rataPersentase,
            'ratingKeseluruhan' => $ratingKeseluruhan,
        ];
    }

    /**
     * Helper untuk menghitung skor per 1 baris target wilayah.
     * 
     * @param TrxTargetWilayah $target
     * @return array
     */
    private function kalkulasiSkorTarget(TrxTargetWilayah $target): array
    {
        $proses = $target->proses;
        $totalTarget = (int) $target->target_daerah;

        // Ambil laporan yang hanya terkait dengan target wilayah ini
        $laporanList = $target->laporans;
        $laporanApproved = $laporanList->where('status_laporan', 'approved');
        $totalRealisasi = (int) $laporanApproved->sum('realisasi_saat_ini');

        $persentase = ($totalTarget > 0)
            ? round(($totalRealisasi / $totalTarget) * 100, 1)
            : 0;

        // 1. Parameter Kualitas (40%)
        $skorKualitas = (int) min(100, round($persentase));

        // 2. Parameter Responsivitas (30%)
        $skorResponsivitas = 100;
        if ($laporanList->count() > 0) {
            $skorResponsivitas = (int) round(($laporanApproved->count() / $laporanList->count()) * 100);
        } elseif ($totalTarget > 0) {
            $skorResponsivitas = 0;
        }

        // 3. Parameter Kecepatan (30%)
        $skorKecepatan = 100;
        if ($laporanApproved->count() > 0) {
            $laporanTepatWaktu = 0;
            $deadline = $proses->tanggal_selesai ?? null;
            foreach ($laporanApproved as $laporan) {
                if ($deadline && $laporan->created_at <= $deadline) {
                    $laporanTepatWaktu++;
                } elseif (!$deadline) {
                    $laporanTepatWaktu++;
                }
            }
            $skorKecepatan = (int) round(($laporanTepatWaktu / $laporanApproved->count()) * 100);
        } elseif ($totalTarget > 0) {
            $skorKecepatan = 0;
        }

        // 4. Verifikasi Bukti Dukung (Menyesuaikan dengan nama kolom)
        $buktiLengkap = false;
        if ($laporanApproved->count() > 0) {
            $buktiAda = $laporanApproved->filter(function ($lap) {
                return !empty($lap->path_bukti_dukung) || !empty($lap->link_bukti) || !empty($lap->file_bukti);
            })->count();
            $buktiLengkap = ($buktiAda === $laporanApproved->count());
        }

        // Total Skor Terbobot
        $skorTerbobot = ($skorKualitas * 0.40) + ($skorResponsivitas * 0.30) + ($skorKecepatan * 0.30);
        if (!$buktiLengkap && $totalTarget > 0) {
            $skorTerbobot -= 10; // Penalti -10 jika bukti dukung tidak lengkap
        }

        $totalSkor = (int) max(0, min(100, round($skorTerbobot)));
        $namaWilayah = 'Semua Wilayah';
        if ($target->wilayah) {
            $namaWilayah = trim(($target->wilayah->nama_provinsi ?? '') . ' ' . ($target->wilayah->kode_nama_kabkota ?? ''));
            // Jika kosong setelah trim, kembalikan ke default
            if (empty($namaWilayah)) {
                $namaWilayah = $target->wilayah->id_wilayah ?? 'Semua Wilayah';
            }
        }

        return [
            'proses'              => $proses,
            'nama_kegiatan'       => $proses->detail->kegiatan->nama_kegiatan ?? '-',
            'nama_detail'         => $proses->detail->nama_keg_detail ?? '-',
            'wilayah'             => $namaWilayah,
            'total_target'        => $totalTarget,
            'total_realisasi'     => $totalRealisasi,
            'persentase'          => $persentase,
            'skor_kualitas'       => $skorKualitas,
            'skor_responsivitas'  => $skorResponsivitas,
            'skor_kecepatan'      => $skorKecepatan,
            'bukti_lengkap'       => $buktiLengkap,
            'total_skor'          => $totalSkor,
            'rating'              => $this->hitungRating($totalSkor),
        ];
    }

    /**
     * Konversi nilai total skor (0–100) ke skala bintang (1–5).
     */
    private function hitungRating(int $skor): int
    {
        if ($skor >= 90) return 5;
        if ($skor >= 75) return 4;
        if ($skor >= 60) return 3;
        if ($skor >= 40) return 2;
        return 1;
    }
}