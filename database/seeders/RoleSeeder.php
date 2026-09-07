<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MstRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel agar tidak terjadi duplikasi saat seeder dijalankan ulang
        Schema::disableForeignKeyConstraints();
        MstRole::truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            ['nama_role' => 'Super Admin'],
            ['nama_role' => 'Admin Provinsi'],
            ['nama_role' => 'Admin Kabupaten/Kota'],
            ['nama_role' => 'Operator Kabupaten/Kota (Pelapor)'],
            ['nama_role' => 'Verifikator'],
            ['nama_role' => 'Viewer Provinsi'],
            ['nama_role' => 'Viewer Kabupaten/Kota'],
        ];

        foreach ($roles as $role) {
            MstRole::create($role);
        }
    }
}