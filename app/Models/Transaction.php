<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory,Sortable;


    protected $fillable = [
        'tipo_transaccion','metodo_pago','monto','total', 'fecha', 'descripcion', 'hora', 'caja_id'
    ];

     public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('numero_caja', 'like', '%' . $search . '%');
        });
    }

    public $sortable = [
        'tipo_transaccion',
        'fecha',
        'metodo_pago',
        'hora',
    ];

    public function caja(){
        return $this->belongsTo(Caja::class, 'caja_id');
    }
}
