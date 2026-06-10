<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caja extends Model
{
    use HasFactory,Sortable;

    protected $fillable = [
        'user_id', 'branche_id', 'numero_caja','fecha', 'hora_apertura', 'hora_cierre',
        'monto_inicial', 'monto_final', 'estado',
    ];

      public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('numero_caja', 'like', '%' . $search . '%');
        });
    }

    public $sortable = [
        'numero_caja',
        'fecha',
        'hora_apertura',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sucursal(){
        return $this->belongsTo(Branche::class, 'branche_id');
    }
}
