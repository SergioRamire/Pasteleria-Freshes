<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'product_name',
        'category_id',
        // 'supplier_id',
        'product_code',
        'codigo_barras',
        'product_garage',
        'product_image',
        'product_store',
        'buying_date',
        'expire_date',
        'buying_price',
        'selling_price',
        'dealer_price',
        'marca_id',
        'status_product',
        'satclave_id',
        'equivalencia_id',
    ];

    public $sortable = [
        'product_name',
        'selling_price',
        'codigo_barras',
        'product_code',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'category',
        'marca'
    ];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function marca(){
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function equivalencia(){
        return $this->belongsTo(Equivalencia::class, 'equivalencia_id');
    }

     public function satclave(){
        return $this->belongsTo(Satclave::class, 'satclave_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('product_name', 'like', '%' . $search . '%');
        });
    }
}
