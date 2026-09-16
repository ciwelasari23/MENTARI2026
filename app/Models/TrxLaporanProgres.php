<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxLaporanProgres extends Model
{
    protected $table = 'trx_laporan_progres';
    protected $primaryKey = 'id_laporan';
    
    protected $fillable = [
        'id_target_wilayah', 
        'id_user_pelapor', 
        'id_user_verifikator',
        'tanggal_lapor',
        'realisasi saat ini', 
        'catatan over target', 
        'is selesai',
        'status_laporan', 
        'path bukti dukung', 
        'link_bukti',
        'catatan verifikasi'
    ];

    // Relasi ke TrxTargetWilayah sesuai ERD
    public function targetWilayah()
    {
        return $this->belongsTo(TrxTargetWilayah::class, 'id_target_wilayah', 'id_target_wilayah');
    }

    // Relasi ke User Pelapor
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_user_pelapor', 'id_user'); 
    }
}