<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Historiale extends Model
{
    use HasFactory;
    use Sortable;

    protected $fillable = [
        'fecha',
        'accion',
        'descripcion',
        'user_id',
        'product_id',
    ];

    public $sortable = [
        'fecha',
        'accion',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
