<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstKegiatanLevel4Proses extends Model
{
    protected $table = 'mst_kegiatan_level4_proses';
    protected $primaryKey = 'id_proses';
    protected $fillable = [
        'id_keg_detail', 
        'nama_proses', 
        'satuan_target', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'target_total_provinsi'
    ];

    public function detail()
    {
        return $this->belongsTo(MstKegiatanLevel3Detail::class, 'id_keg_detail', 'id_keg_detail');
    }
}