<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traspasosdetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'cantidad',
        'producto_id',
        'traspaso_id',
        'inventario_id',
    ];

    public function traspaso()
    {
        return $this->belongsTo(Traspaso::class, 'traspaso_id');
    }

    /**
     * Relación: un detalle pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    public function inventario(){
        return $this->belongsTo(Inventario::class, 'inventarios_id');
    }
}
