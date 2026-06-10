<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Inventario extends Model
{
     use HasFactory;
    use Sortable;
     protected $fillable = [
        'stock_minimo',
        'stock',
        'estado',
        'disponibilidad',
        'product_id',
        'branche_id'
    ];

    public function producto(){
        return $this->belongsTo(Product::class, 'product_id');
    }

     public function Sucursal(){
        return $this->belongsTo(Branche::class, 'branche_id');
    }

    // esto para que ordene por producto y codigo de barras
    public $sortableAs = ['producto', 'codigo_barras', 'stock','product_name','product_code'];
// public $sortableAs = ['', 'category_name', 'marca_nombre', 'codigo_barras'];






}
