<!DOCTYPE html>
<html>
<head>
    <title>Rekap Evaluasi MENTARI</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h2 class="text-center">Rekapitulasi Evaluasi & Penilaian Kegiatan MENTARI</h2>
    <p><strong>Tanggal Cetak:</strong> {{ $tanggalCetak }}</p>
    <p><strong>Rata-rata Skor Keseluruhan:</strong> {{ $rataPersentase }} / 100</p>

    <table>
        <thead>
            <tr>
                <th>Kegiatan</th>
                <th>Proses</th>
                <th class="text-center">Target</th>
                <th class="text-center">Realisasi</th>
                <th class="text-center">Skor Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluasiData as $row)
            <tr>
                <td>{{ $row['nama_kegiatan'] }}</td>
                <td>{{ $row['nama_detail'] }}<br><small>{{ $row['wilayah'] }}</small></td>
                <td class="text-center">{{ number_format($row['total_target']) }}</td>
                <td class="text-center">{{ number_format($row['total_realisasi']) }}</td>
                <td class="text-center font-bold">{{ $row['total_skor'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>