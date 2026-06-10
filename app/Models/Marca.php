<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Marca extends Model
{
    use HasFactory;
    use Sortable;
    protected $fillable = [
        'nombre',
        'suppliers_id',
    ];

    public $sortable = [
        'nombre',
    ];

    protected $guarded = [
        'id',
    ];

    protected $table = 'marcas';

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'suppliers_id');
    }

    // metodo para realizar filtros en la consulta-> busqueda
    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('nombre', 'like', '%' . $search . '%');
        }
    }

    // public function products(){
    //     return $this->belongsTo(Product::class, 'marca_id');
    // }

}
