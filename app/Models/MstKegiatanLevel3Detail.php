<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstKegiatanLevel3Detail extends Model
{
    protected $table = 'mst_kegiatan_level3_detail';
    protected $primaryKey = 'id_keg_detail';
    
    protected $fillable = [
        'id_kegiatan', 
        'nama_keg_detail', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'target_total', 
        'satuan_target'
    ];

    // Relasi ke Level 2 (Kegiatan Induk)
    public function kegiatan()
    {
        return $this->belongsTo(MstKegiatanLevel2Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    // Relasi ke Level 4 (Proses/Tahapan)
    public function prosesAll()
    {
        return $this->hasMany(MstKegiatanLevel4Proses::class, 'id_keg_detail', 'id_keg_detail');
    }
}