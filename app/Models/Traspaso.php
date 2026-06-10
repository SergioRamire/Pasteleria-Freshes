<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Traspaso extends Model
{
    use HasFactory,Sortable;

    protected $fillable = [
        'codigo',
        'fecha',
        'hora',
        'estado',
        'observaciones',
        'responsable',
        'sucursal_origen',
        'sucursal_destino',
    ];

     public $sortable = [
        'codigo',
        'fecha',
        'hora',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'responsable');
    }

    public function sucursalOrigen()
    {
        return $this->belongsTo(Branch::class, 'sucursal_origen');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Branch::class, 'sucursal_destino');
    }

    public function detalles()
    {
        return $this->hasMany(TraspasoDetalle::class);
    }
}
