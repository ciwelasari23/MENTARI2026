<?php

namespace App\Imports;

use App\Models\MstSls;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SlsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return MstSls::updateOrCreate(
            ['idsubsls' => $row['idsubsls']],
            [
                'semester'      => $row['semester'] ?? null,
                'nmsls'         => $row['nmsls'] ?? '-',
                'nama_ket'      => $row['nama_ket'] ?? null,
                'jenis'         => $row['jenis'] ?? null,
                'kdprov'        => $row['kdprov'] ?? '',
                'nmprov'        => $row['nmprov'] ?? '',
                'kdkab'         => $row['kdkab'] ?? '',
                'nmkab'         => $row['nmkab'] ?? '',
                'kdkec'         => $row['kdkec'] ?? '',
                'nmkec'         => $row['nmkec'] ?? '',
                'kddesa'        => $row['kddesa'] ?? '',
                'nmdesa'        => $row['nmdesa'] ?? '',
                'kdsls'         => $row['kdsls'] ?? '',
                'klas'          => $row['klas'] ?? null,
                'jumlah_kk'     => $row['jumlah_kk'] ?? 0,
                'jumlah_bstt'   => $row['jumlah_bstt'] ?? 0,
                'jumlah_bsbtt'  => $row['jumlah_bsbtt'] ?? 0,
                'jumlah_bsttk'  => $row['jumlah_bsttk'] ?? 0,
                'jumlah_bku'    => $row['jumlah_bku'] ?? 0,
                'jumlah_usaha'  => $row['jumlah_usaha'] ?? 0,
                'jumlah_muatan' => $row['jumlah_muatan'] ?? 0,
                'dominan'       => $row['dominan'] ?? null,
            ]
        );
    }
}