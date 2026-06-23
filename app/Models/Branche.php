<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branche extends Model
{
    use HasFactory,Sortable;

    protected $fillable = [
        'nombre',
        'direccion',
        'longitud',
        'latitud',
        'rul_maps',
    ];

    public $sortable = [
        'nombre',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('nombre', 'like', '%' . $search . '%');
        });
    }


    public function users()
    {
        return $this->hasMany(User::class, 'branche_id');
    }

}
