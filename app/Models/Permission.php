<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id_permission';
    protected $fillable = ['name', 'display_name'];

    // Relasi balik ke Role
    public function roles()
    {
        return $this->belongsToMany(
            MstRole::class, 
            'role_permissions', 
            'id_permission', 
            'id_role'
        );
    }
}