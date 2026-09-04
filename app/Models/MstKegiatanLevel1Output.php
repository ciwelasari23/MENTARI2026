<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstKegiatanLevel1Output extends Model
{
    protected $table = 'mst_kegiatan_level1_output';
    protected $primaryKey = 'id_output';
    protected $fillable = ['id_team', 'nama_output', 'tahun'];

    // Relasi ke MstTeam
    public function team()
    {
        return $this->belongsTo(MstTeam::class, 'id_team', 'id_team');
    }
}