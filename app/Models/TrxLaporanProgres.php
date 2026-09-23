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
        'realisasi_saat_ini', 
        'catatan_over_target', 
        'is_selesai',
        'status_laporan', 
        'path_bukti_dukung', 
        'link_bukti',
        'catatan_verifikasi',
        'file_bukti'
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

    // Relasi ke User Verifikator (SESUAI DENGAN $fillable: id_user_verifikator)
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'id_user_verifikator', 'id_user');
    }
}