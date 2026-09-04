<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MstRole;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['nama_role' => 'Admin BPS Riau'],
            ['nama_role' => 'Verifikator'],
            ['nama_role' => 'Pelapor'],
        ];

        foreach ($roles as $role) {
            MstRole::create($role);
        }
    }
}