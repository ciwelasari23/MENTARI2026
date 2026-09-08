<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxLaporan extends Model
{
    protected $table = 'trx_laporan';
    protected $primaryKey = 'id_laporan';
    protected $fillable = [
        'id_target', 'id_user', 'tanggal_lapor', 
        'realisasi_kuantiti', 'link_bukti', 'file_bukti',
        'status_laporan', 'catatan_verifikator'
    ];

    public function target()
    {
        return $this->belongsTo(TrxTarget::class, 'id_target', 'id_target');
    }

    public function user()
    {
        // Ganti User::class jika model Anda bernama MstUser::class
        return $this->belongsTo(User::class, 'id_user', 'id_user'); 
    }
}