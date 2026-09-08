<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MstMenu;
use App\Models\Permission;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $defaultMenus = [
            // Dashboard dihapus dari sini karena sudah ada secara statis di layout utama
            ['nama_menu' => 'Visualisasi Data', 'url' => '/admin/visualisasi', 'parent_id' => null],
            ['nama_menu' => 'Master Data', 'url' => '#', 'parent_id' => null],
            ['nama_menu' => 'Kelola Kegiatan', 'url' => '#', 'parent_id' => null],
            ['nama_menu' => 'Target Wilayah', 'url' => '/admin/target-wilayah', 'parent_id' => null],
            ['nama_menu' => 'Form Pelaporan', 'url' => '/admin/pelaporan', 'parent_id' => null],
            ['nama_menu' => 'Verifikasi Laporan', 'url' => '/admin/verifikasi', 'parent_id' => null],
            ['nama_menu' => 'Evaluasi Kegiatan', 'url' => '/admin/evaluasi', 'parent_id' => null],
        ];

        foreach ($defaultMenus as $menuData) {
            // Simpan menu jika belum ada berdasarkan nama_menu
            $menu = MstMenu::firstOrCreate(
                ['nama_menu' => $menuData['nama_menu']],
                [
                    'url' => $menuData['url'],
                    'parent_id' => $menuData['parent_id']
                ]
            );

            // Sinkronisasi otomatis ke tabel permissions agar hak aksesnya langsung terbentuk
            Permission::firstOrCreate(
                ['display_name' => $menuData['nama_menu']],
                [
                    'name' => strtolower(str_replace(' ', '_', $menuData['nama_menu']))
                ]
            );
        }
    }
}