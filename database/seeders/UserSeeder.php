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
        User::create([
            'id_role' => 1,
            'nip_nik' => '1234567890123456',
            'nama_lengkap' => 'Cici Wela Sari',
            'email' => 'admin@bps.go.id',
            'kategori_user' => 'Pegawai',
            'password_hash' => Hash::make('password123'), 
        ]);

        // Akun Verifikator
        User::create([
            'id_role' => 2,
            'nip_nik' => '1234567890123457',
            'nama_lengkap' => 'Verifikator Sistem',
            'email' => 'verifikator@bps.go.id',
            'kategori_user' => 'Pegawai',
            'password_hash' => Hash::make('password123'), 
        ]);

        // Akun Pelapor (Mitra)
        User::create([
            'id_role' => 3,
            'nip_nik' => '1234567890123458',
            'nama_lengkap' => 'Mitra Pelapor',
            'email' => 'mitra@domain.com',
            'kategori_user' => 'Mitra',
            'password_hash' => Hash::make('password123'), 
        ]);

        // Akun Admin / Operator Kabupaten/Kota
        User::create([
            'id_role' => 3, // Sesuaikan dengan id_role Kabupaten/Kota Anda
            'nip_nik' => '9988776655443322',
            'nama_lengkap' => 'Operator Kabupaten',
            'email' => 'kabupaten@bps.go.id',
            'kategori_user' => 'Pegawai', // atau 'Mitra' sesuai sistem Anda
            'password_hash' => Hash::make('password123'), 
        ]);
    }
}