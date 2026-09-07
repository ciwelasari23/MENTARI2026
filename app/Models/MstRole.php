<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstRole extends Model
{
    protected $table = 'mst_role';
    protected $primaryKey = 'id_role';
    
    protected $fillable = [
        'nama_role'
    ];

    // Relasi Many-to-Many ke Permissions (Cukup 4 parameter inti)
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class, 
            'role_permissions', 
            'id_role', 
            'id_permission'
        );
    }
}