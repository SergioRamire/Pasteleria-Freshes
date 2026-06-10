<?php

namespace App\Models;


use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equivalencia extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'nombre',
        'abreviatura',
        'descripcion',
        'clave_sat',
        'tipo',
        'activo',
    ];

    public $sortable = [
        'nombre',
        'clave_sat',
        'tipo',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('nombre', 'like', '%' . $search . '%')
                        ->orWhere('abreviatura', 'like', '%' . $search . '%')
                        ->orWhere('clave_sat', 'like', '%' . $search . '%')
                        ->orWhere('tipo', 'like', '%' . $search . '%');
        });
    }
}
