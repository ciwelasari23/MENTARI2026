<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstWilayah extends Model
{
    use HasFactory;

    protected $table = 'mst_wilayah'; 
    
    // Pastikan ini sesuai dengan nama kolom Primary Key di MySQL (id_wilayah)
    protected $primaryKey = 'id_wilayah';
    public $incrementing = false; 
    protected $keyType = 'string';

    // id_wilayah wajib dimasukkan ke $fillable
    protected $fillable = [
        'id_wilayah',
        'kode_wilayah',
        'nama_provinsi',
        'kode_nama_kabkota',
        'kode_nama_kecamatan',
        'kode_nama_desa',
        'kode_nama_sls',
        'kode_nama_sub_sls'
    ];

    /**
     * Relasi Eloquent ke Tabel Target Wilayah
     */
    public function targetWilayah()
    {
        return $this->hasMany(TrxTargetWilayah::class, 'id_wilayah', 'id_wilayah');
    }

    /**
     * Relasi Eloquent ke Tabel Master User
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_wilayah', 'id_wilayah');
    }
}