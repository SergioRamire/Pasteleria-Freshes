<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailslistproduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'cantidad',
        'producto_id',
        'listproduct_id',
    ];

    public function lista()
    {
        return $this->belongsTo(Listproduct::class, 'listproduct_id');
    }

    /**
     * Relación: un detalle pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

}
