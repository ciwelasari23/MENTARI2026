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

    public function wilayah()
    {
        return $this->belongsTo(MstWilayah::class, 'id_wilayah', 'id_wilayah');
    }

    public function proses()
    {
        return $this->belongsTo(MstKegiatanLevel4Proses::class, 'id_proses', 'id_proses');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    // Diubah menggunakan TrxLaporanProgres dan foreign key id_target_wilayah
    public function laporans()
    {
        return $this->hasMany(TrxLaporanProgres::class, 'id_target_wilayah', 'id_target_wilayah');
    }
}