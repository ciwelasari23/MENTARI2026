<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstWilayah extends Model
{
    protected $table = 'mst_wilayah';
    protected $primaryKey = 'id_wilayah';
    
    // Karena id_wilayah bertipe VARCHAR (bukan angka auto-increment), set ini ke false:
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_wilayah',
        'kode_wilayah', 
        'nama_wilayah',
        'level_wilayah',
        'id_parent_wilayah',
    ];
}