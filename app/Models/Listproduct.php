<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Listproduct extends Model
{
    use HasFactory,Sortable;

    protected $fillable = [
        'codigo',
        'fecha',
        'hora',
        'observaciones',
        'responsable',
        'sucursal_origen',
    ];

     public $sortable = [
        // 'codigo',
        'fecha',
        'hora',
    ];

    public function detalles()
    {
        return $this->hasMany(Detailslistproduct::class);
    }

}
