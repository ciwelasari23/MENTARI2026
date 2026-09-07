<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstWilayah extends Model
{
    protected $table = 'mst_wilayah';
    protected $primaryKey = 'id_wilayah';
    
    // WAJIB DITAMBAHKAN KARENA ID BERUPA STRING (VARCHAR) BUKAN INTEGER AUTO_INCREMENT
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_wilayah',
        'nama_wilayah',
        'level_wilayah',
        'id_parent_wilayah' // Sesuai ERD
    ];
}