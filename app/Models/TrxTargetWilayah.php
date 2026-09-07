<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxTargetWilayah extends Model
{
    use HasFactory;

    protected $table = 'trx_target_wilayah';
    protected $primaryKey = 'id_target_wilayah';

    protected $fillable = [
        'id_wilayah',
        'id_proses',
        'target_daerah',
        'created_by',
    ];

    // Relasi ke MstWilayah
    public function wilayah()
    {
        return $this->belongsTo(MstWilayah::class, 'id_wilayah', 'id_wilayah');
    }

    // Relasi ke Proses Kegiatan Level 4 (mst_kegiatan_level4_proses)
    public function proses()
    {
        return $this->belongsTo(MstKegiatanLevel4Proses::class, 'id_proses', 'id_proses');
    }

    // Relasi ke User pembuat target
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}