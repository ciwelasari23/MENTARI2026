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

    // ==========================================
    // TAMBAHKAN RELASI DAN FUNGSI INI
    // ==========================================

    // Relasi ke tabel MstRole (Many-to-One / BelongsTo)
    public function role()
    {
        return $this->belongsTo(MstRole::class, 'id_role', 'id_role');
    }

    // Fungsi helper untuk mengecek izin akses (permissions)
    // Fungsi helper untuk mengecek izin akses (permissions)
    // Perbaikan fungsi hasPermission agar fleksibel mengecek nama atau deskripsi permission
    public function hasPermission(string $permissionName): bool
    {
        // Jika Super Admin (id_role == 1), berikan akses penuh
        if ($this->id_role == 1) {
            return true;
        }

        // Cek melalui relasi role -> permissions (mencocokkan kolom 'name' ATAU 'display_name')
        if ($this->role && $this->role->permissions) {
            return $this->role->permissions()
                ->where(function($query) use ($permissionName) {
                    $query->where('name', 'LIKE', "%{$permissionName}%")
                          ->orWhere('display_name', 'LIKE', "%{$permissionName}%");
                })
                ->exists();
        }

        return false;
    }
}