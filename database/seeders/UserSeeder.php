<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@bps.go.id'],
            [
                'id_role' => 1,
                'nip_nik' => '1234567890123456',
                'nama_lengkap' => 'Cici Wela Sari',
                'kategori_user' => 'Pegawai',
                'password_hash' => Hash::make('password123'),
            ]
        );

        // Akun Verifikator
        User::updateOrCreate(
            ['email' => 'verifikator@bps.go.id'],
            [
                'id_role' => 2,
                'nip_nik' => '1234567890123457',
                'nama_lengkap' => 'Verifikator Sistem',
                'kategori_user' => 'Pegawai',
                'password_hash' => Hash::make('password123'),
            ]
        );

        // Akun Pelapor (Mitra)
        User::updateOrCreate(
            ['email' => 'mitra@domain.com'],
            [
                'id_role' => 3,
                'nip_nik' => '1234567890123458',
                'nama_lengkap' => 'Mitra Pelapor',
                'kategori_user' => 'Mitra',
                'password_hash' => Hash::make('password123'),
            ]
        );

        // Akun Admin / Operator Kabupaten/Kota
        User::updateOrCreate(
            ['email' => 'kabupaten@bps.go.id'],
            [
                'id_role' => 3,
                'nip_nik' => '9988776655443322',
                'nama_lengkap' => 'Operator Kabupaten',
                'kategori_user' => 'Pegawai',
                'password_hash' => Hash::make('password123'),
            ]
        );
    }
}