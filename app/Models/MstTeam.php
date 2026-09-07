<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstTeam extends Model
{
    protected $table = 'mst_team';
    
    // Sesuaikan primary key dengan database Anda (id_team)
    protected $primaryKey = 'id_team';

    // Daftarkan kolom yang boleh diisi
    protected $fillable = [
        'nama_team'
    ];
}