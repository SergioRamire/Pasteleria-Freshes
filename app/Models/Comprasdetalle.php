<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprasdetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'cantidad',
        'producto_id',
        'compra_id',
        'inventario_id',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
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
