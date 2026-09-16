<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstSls extends Model
{
    use HasFactory;

    protected $table = 'mst_sls';
    protected $guarded = ['id'];
}