<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionNegocio extends Model
{
    protected $table = 'configuracion_negocio';

    protected $fillable = [
        'nombre_negocio',
        'telefono',
        'logo',
        'favicon',
    ];

}
