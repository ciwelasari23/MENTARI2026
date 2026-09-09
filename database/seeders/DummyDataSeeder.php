<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MstTeam;
use App\Models\MstWilayah;
use App\Models\MstKegiatanLevel1Output;
use App\Models\MstKegiatanLevel2Kegiatan;
use App\Models\MstKegiatanLevel3Detail;
use App\Models\MstKegiatanLevel4Proses;
use App\Models\TrxTarget;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Wilayah (Dummy) - Jika Belum Ada
        $wilayah1 = MstWilayah::firstOrCreate(
            ['id_wilayah' => '1400'],
            ['nama_wilayah' => 'Provinsi Riau', 'level_wilayah' => 1]
        );
        $wilayah2 = MstWilayah::firstOrCreate(
            ['id_wilayah' => '1471'],
            ['nama_wilayah' => 'Kota Pekanbaru', 'level_wilayah' => 2, 'id_parent_wilayah' => '1400']
        );
        $wilayah3 = MstWilayah::firstOrCreate(
            ['id_wilayah' => '1401'],
            ['nama_wilayah' => 'Kabupaten Kampar', 'level_wilayah' => 2, 'id_parent_wilayah' => '1400']
        );

        // 2. Data Team (Dummy) - Jika Belum Ada
        $team = MstTeam::firstOrCreate(
            ['nama_team' => 'Tim Produksi Statistik']
        );

        // 3. Level 1: Output Kegiatan
        $output = MstKegiatanLevel1Output::create([
            'id_team' => $team->id_team,
            'nama_output' => 'Output Sensus Pertanian',
            'tahun' => 2026
        ]);

        // 4. Level 2: Kegiatan
        $kegiatan = MstKegiatanLevel2Kegiatan::create([
            'id_output' => $output->id_output,
            'nama_kegiatan' => 'Pelaksanaan Sensus Lapangan'
        ]);

        // 5. Level 3: Detail Kegiatan
        $detail = MstKegiatanLevel3Detail::create([
            'id_kegiatan' => $kegiatan->id_kegiatan,
            'nama_keg_detail' => 'Pendataan Rumah Tangga Usaha Pertanian',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
            'target_total' => 10000,
            'satuan_target' => 'Rumah Tangga'
        ]);

        // 6. Level 4: Proses Kegiatan
        $proses1 = MstKegiatanLevel4Proses::create([
            'id_keg_detail' => $detail->id_keg_detail,
            'nama_proses' => 'Listing Pendataan Awal',
            'satuan_target' => 'Dokumen',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-03-31',
            'target_total_provinsi' => 10000
        ]);

        $proses2 = MstKegiatanLevel4Proses::create([
            'id_keg_detail' => $detail->id_keg_detail,
            'nama_proses' => 'Pencacahan Lengkap',
            'satuan_target' => 'Dokumen',
            'tanggal_mulai' => '2026-04-01',
            'tanggal_selesai' => '2026-08-31',
            'target_total_provinsi' => 10000
        ]);

        // 7. Target Wilayah
        TrxTarget::create([
            'id_proses' => $proses1->id_proses,
            'id_wilayah' => $wilayah2->id_wilayah, // Kota Pekanbaru
            'target_kuantiti' => 2500
        ]);

        TrxTarget::create([
            'id_proses' => $proses1->id_proses,
            'id_wilayah' => $wilayah3->id_wilayah, // Kabupaten Kampar
            'target_kuantiti' => 3500
        ]);

        TrxTarget::create([
            'id_proses' => $proses2->id_proses,
            'id_wilayah' => $wilayah2->id_wilayah, // Kota Pekanbaru
            'target_kuantiti' => 2500
        ]);
        
        TrxTarget::create([
            'id_proses' => $proses2->id_proses,
            'id_wilayah' => $wilayah3->id_wilayah, // Kabupaten Kampar
            'target_kuantiti' => 3500
        ]);
    }
}
