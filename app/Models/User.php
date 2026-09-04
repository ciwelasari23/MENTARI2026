<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Sesuaikan nama tabel dan primary key dengan ERD
    protected $table = 'mst_user';
    protected $primaryKey = 'id_user';

    // 2. Daftarkan semua kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'id_wilayah',
        'id_role',
        'id_team',
        'nip_nik',
        'nama_lengkap',
        'email',
        'kategori_user',
        'password_hash',
    ];

    // 3. Sembunyikan kolom password_hash demi keamanan saat data dipanggil
    protected $hidden = [
        'password_hash',
    ];

    // 4. Beri tahu sistem Login Laravel untuk menggunakan 'password_hash' bukan 'password'
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Pastikan password_hash dienkripsi otomatis oleh Laravel
            'password_hash' => 'hashed',
        ];
    }
}