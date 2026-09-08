<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id_wilayah
 * @property string $nama_wilayah
 * @property int $level_wilayah
 * @property string|null $id_parent_wilayah
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class MstWilayah extends Model
{
    use HasFactory;

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