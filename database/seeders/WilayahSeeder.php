<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('mst_wilayah')->delete();

        // Arahkan langsung ke folder database/seeders/data_wilayah.csv
        $csvFile = database_path('seeders/data_wilayah.csv');
        
        if (file_exists($csvFile) && is_readable($csvFile)) {
            $header = true;
            $batchData = [];
            $batchSize = 1000;

            if (($handle = fopen($csvFile, 'r')) !== FALSE) {
                while (($row = fgetcsv($handle, 2000, ';')) !== FALSE) {
                    // Lewati baris pertama (header)
                    if ($header) {
                        $header = false;
                        continue;
                    }

                    // Pastikan index kolom idsubsls (index ke-2) ada
                    if (isset($row[2]) && !empty(trim($row[2]))) {
                        $batchData[] = [
                            'id_wilayah'          => trim($row[2]), 
                            'nama_provinsi'       => isset($row[7]) ? trim($row[7]) : null, 
                            'kode_nama_kabkota'   => isset($row[9]) ? trim($row[9]) : null, 
                            'kode_nama_kecamatan' => isset($row[11]) ? trim($row[11]) : null, 
                            'kode_nama_desa'      => isset($row[13]) ? trim($row[13]) : null, 
                            'kode_nama_sls'       => isset($row[3]) ? trim($row[3]) : null, 
                            'kode_nama_sub_sls'   => isset($row[15]) ? trim($row[15]) : null, 
                            'jumlah_kk'           => isset($row[17]) && is_numeric(trim($row[17])) ? (int) trim($row[17]) : 0,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];

                        if (count($batchData) >= $batchSize) {
                            DB::table('mst_wilayah')->insert($batchData);
                            $batchData = [];
                        }
                    }
                }
                
                if (!empty($batchData)) {
                    DB::table('mst_wilayah')->insert($batchData);
                }

                fclose($handle);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}