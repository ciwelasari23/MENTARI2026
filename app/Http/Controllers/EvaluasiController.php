<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrxTargetWilayah;
use App\Models\TrxLaporanProgres;
use Illuminate\Support\Facades\DB;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai perPage dari request, default 10 jika tidak diisi
        $perPage = $request->input('perPage', 10);

        // Ambil data target wilayah dengan pagination
        $targetsPaginator = TrxTargetWilayah::with([
            'proses.detail.kegiatan.output',
            'wilayah',
            'laporans'
        ])->paginate($perPage)->withQueryString();

        $evaluasiData = [];
        foreach ($targetsPaginator as $target) {
            if (!$target->proses) {
                continue;
            }
            $evaluasiData[] = $this->kalkulasiSkorTarget($target);
        }

        // Hitung rata-rata skor keseluruhan dari seluruh data (bukan hanya halaman aktif)
        $allTargets = TrxTargetWilayah::with(['proses', 'laporans'])->get();
        $allEvaluasi = [];
        foreach ($allTargets as $t) {
            if ($t->proses) {
                $allEvaluasi[] = $this->kalkulasiSkorTarget($t);
            }
        }

        $rataPersentase = count($allEvaluasi) > 0
            ? (float) round(collect($allEvaluasi)->avg('total_skor'), 1)
            : 0;

        $ratingKeseluruhan = $this->hitungRating((int) round($rataPersentase));

        // Kita bungkus paginator agar variabel evaluasiData berisi data halaman aktif, 
        // namun tetap membawa fungsi paginasi.
        // Solusinya: Kita buat custom paginator atau manfaatkan objek paginator target.
        
        return view('evaluasi.evaluasi', [
            'evaluasiData'      => $evaluasiData,
            'targetsPaginator'  => $targetsPaginator, // Digunakan untuk link() dan info showing entries
            'rataPersentase'    => $rataPersentase,
            'ratingKeseluruhan' => $ratingKeseluruhan,
        ]);
    }

    public function updateSkor(Request $request, int $id)
    {
        $request->validate([
            'skor_manual' => 'required|numeric|min:0|max:100',
        ]);

        $laporan = TrxLaporanProgres::findOrFail($id);
        
        $laporan->skor_manual = $request->skor_manual;
        $laporan->status_kegiatan = 'selesai'; 
        $laporan->save();

        return redirect()->back()->with('success', 'Skor manual berhasil disimpan dan kegiatan ditandai selesai.');
    }

    public function exportPdf()
    {
        $allTargets = TrxTargetWilayah::with([
            'proses.detail.kegiatan.output',
            'wilayah',
            'laporans'
        ])->get();

        $evaluasiData = [];
        foreach ($allTargets as $target) {
            if (!$target->proses) continue;
            $evaluasiData[] = $this->kalkulasiSkorTarget($target);
        }

        $rataPersentase = count($evaluasiData) > 0
            ? (float) round(collect($evaluasiData)->avg('total_skor'), 1)
            : 0;

        $ratingKeseluruhan = $this->hitungRating((int) round($rataPersentase));

        $pdf = app('dompdf.wrapper')->loadView('evaluasi.pdf_template', [
            'evaluasiData'      => $evaluasiData,
            'rataPersentase'    => $rataPersentase,
            'ratingKeseluruhan' => $ratingKeseluruhan,
            'tanggalCetak'      => now()->translatedFormat('d F Y'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Evaluasi_MENTARI_' . date('Ymd_His') . '.pdf');
    }

    private function kalkulasiSkorTarget(TrxTargetWilayah $target): array
    {
        $proses = $target->proses;
        $totalTarget = (int) $target->target_daerah;

        $laporanList = $target->laporans;
        $laporanApproved = $laporanList->where('status_laporan', 'approved');
        $totalRealisasi = (int) $laporanApproved->sum('realisasi_saat_ini');

        $skorManualInput = $laporanApproved->whereNotNull('skor_manual')->avg('skor_manual');

        $persentase = ($totalTarget > 0)
            ? round(($totalRealisasi / $totalTarget) * 100, 1)
            : 0;

        $skorKualitas = (int) min(100, round($persentase));

        $skorResponsivitas = 100;
        if ($laporanList->count() > 0) {
            $skorResponsivitas = (int) round(($laporanApproved->count() / $laporanList->count()) * 100);
        } elseif ($totalTarget > 0) {
            $skorResponsivitas = 0;
        }

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

        $buktiLengkap = false;
        if ($laporanApproved->count() > 0) {
            $buktiAda = $laporanApproved->filter(function ($lap) {
                return !empty($lap->path_bukti_dukung) || !empty($lap->link_bukti) || !empty($lap->file_bukti);
            })->count();
            $buktiLengkap = ($buktiAda === $laporanApproved->count());
        }

        if ($skorManualInput !== null) {
            $totalSkor = (int) round($skorManualInput);
        } else {
            $skorTerbobot = ($skorKualitas * 0.40) + ($skorResponsivitas * 0.30) + ($skorKecepatan * 0.30);
            if (!$buktiLengkap && $totalTarget > 0) {
                $skorTerbobot -= 10;
            }
            $totalSkor = (int) max(0, min(100, round($skorTerbobot)));
        }

        $namaWilayah = 'Semua Wilayah';
        if ($target->wilayah) {
            $namaWilayah = trim(($target->wilayah->nama_provinsi ?? '') . ' ' . ($target->wilayah->kode_nama_kabkota ?? ''));
            if (empty($namaWilayah)) {
                $namaWilayah = $target->wilayah->id_wilayah ?? 'Semua Wilayah';
            }
        }

        $statusKegiatan = $laporanApproved->contains('status_kegiatan', 'selesai') ? 'selesai' : 'proses';

        return [
            'proses'             => $proses,
            'nama_kegiatan'      => $proses->detail->kegiatan->nama_kegiatan ?? '-',
            'nama_detail'        => $proses->detail->nama_keg_detail ?? '-',
            'wilayah'            => $namaWilayah,
            'total_target'       => $totalTarget,
            'total_realisasi'    => $totalRealisasi,
            'persentase'         => $persentase,
            'skor_kualitas'      => $skorKualitas,
            'skor_responsivitas' => $skorResponsivitas,
            'skor_kecepatan'     => $skorKecepatan,
            'bukti_lengkap'      => $buktiLengkap,
            'skor_manual'        => $skorManualInput,
            'status_kegiatan'    => $statusKegiatan,
            'total_skor'         => $totalSkor,
            'rating'             => $this->hitungRating($totalSkor),
        ];
    }

    private function hitungRating(int $skor): int
    {
        if ($skor >= 90) return 5;
        if ($skor >= 75) return 4;
        if ($skor >= 60) return 3;
        if ($skor >= 40) return 2;
        return 1;
    }
}