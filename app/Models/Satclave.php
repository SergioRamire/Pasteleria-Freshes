<?php

namespace App\Models;


use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Satclave extends Model
{
   use HasFactory, Sortable;

    protected $fillable = [
        'c_ClaveProdServ',
        'descripcion',
        'activo',
    ];

      public $sortable = [
        'c_ClaveProdServ',
    ];


    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('c_ClaveProdServ', 'like', '%' . $search . '%')
            ->orwhere('descripcion', 'like', '%' . $search . '%');
        });
    }
}
