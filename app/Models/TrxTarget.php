<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxTarget extends Model
{
    protected $table = 'trx_target';
    protected $primaryKey = 'id_target';
    protected $fillable = ['id_proses', 'id_wilayah', 'target_kuantiti'];

    public function proses()
    {
        return $this->belongsTo(MstKegiatanLevel4Proses::class, 'id_proses', 'id_proses');
    }

    public function wilayah()
    {
        return $this->belongsTo(MstWilayah::class, 'id_wilayah', 'id_wilayah');
    }
}