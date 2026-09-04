<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstKegiatanLevel2Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'mst_kegiatan_level2_kegiatan';
    protected $primaryKey = 'id_kegiatan';
    public $timestamps = true;

    protected $fillable = [
        'id_output',
        'nama_kegiatan'
    ];

    // Relasi ke Level 1 (Output)
    public function output()
    {
        return $this->belongsTo(MstKegiatanLevel1Output::class, 'id_output', 'id_output');
    }
}