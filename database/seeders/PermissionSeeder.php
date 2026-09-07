<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage-master', 'display_name' => 'Master Data (Wilayah, Tim Kerja, User, Role)'],
            ['name' => 'manage-kegiatan', 'display_name' => 'Kelola Kegiatan (Level 1 - 4: Tambah/Edit/Hapus)'],
            ['name' => 'manage-target', 'display_name' => 'Target Wilayah (Menetapkan beban target kab/kota)'],
            ['name' => 'manage-pelaporan', 'display_name' => 'Form Pelaporan (Upload progres & bukti dukung)'],
            ['name' => 'manage-verifikasi', 'display_name' => 'Verifikasi Laporan & Beri Rating/Scoring'],
            ['name' => 'view-kabkot-only', 'display_name' => 'Batasi Hanya Lihat Wilayah Sendiri (Viewer Kab/Kota)'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}