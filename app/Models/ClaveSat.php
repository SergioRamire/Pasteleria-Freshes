<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaveSat extends Model
{
    protected $table = 'claves_sat';

    protected $fillable = [
        'c_ClaveProdServ',
        'descripcion',
        'activo',
    ];
}
