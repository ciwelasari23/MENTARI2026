<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstMenu extends Model
{
    use HasFactory;

    protected $table = 'mst_menu';
    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'parent_id',
        'nama_menu',
        'url'
    ];

    // Relasi ke Menu Induk
    public function parent()
    {
        return $this->belongsTo(MstMenu::class, 'parent_id', 'id_menu');
    }

    // Relasi ke Submenu (Anak)
    public function children()
    {
        return $this->hasMany(MstMenu::class, 'parent_id', 'id_menu');
    }
}