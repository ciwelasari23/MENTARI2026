<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_wilayah' => '1473', 'kode_wilayah' => '14.73', 'nama_provinsi' => 'RIAU', 'kode_nama_kabkota' => 'KOTA DUMAI'],
            ['id_wilayah' => '1471', 'kode_wilayah' => '14.71', 'nama_provinsi' => 'RIAU', 'kode_nama_kabkota' => 'KOTA PEKANBARU'],
            ['id_wilayah' => '1410', 'kode_wilayah' => '14.10', 'nama_provinsi' => 'RIAU', 'kode_nama_kabkota' => 'KAB. ROKAN HILIR'],
            ['id_wilayah' => '1409', 'kode_wilayah' => '14.09', 'nama_provinsi' => 'RIAU', 'kode_nama_kabkota' => 'KAB. ROKAN HULU'],
            ['id_wilayah' => '1408', 'kode_wilayah' => '14.08', 'nama_provinsi' => 'RIAU', 'kode_nama_kabkota' => 'KAB. BENGKALIS'],
            ['id_wilayah' => '1407', 'kode_wilayah' => '14.07', 'nama_provinsi' => 'RIAU', 'kode_nama_kabkota' => 'KAB. KEPULAUAN MERANTI'],
        ];

        DB::table('mst_wilayah')->insert($data);
    }
}