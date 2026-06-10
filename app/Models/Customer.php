<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Kyslik\ColumnSortable\Sortable;

class Customer extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'shopname',
        'photo',
        'city',
        'type_customer',
        'rfc',
        'tipo_persona',
        'num_interior',
        'num_exterior',
        'calle',
        'colonia',
        'municipio',
        'estado',
        'pais',
        'referencia',
        'cp',
        'rul_maps',
        'regimen_fiscal',
        'uso_cfdi',
    ];
    public $sortable = [
        'name',
        'email',
        'phone',
        'shopname',
        'city',
    ];

    protected $guarded = [
        'id',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')->orWhere('shopname', 'like', '%' . $search . '%');
        });
    }
}
