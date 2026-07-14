<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';

    protected $fillable = [
        'codigo',
        'fecha',
        'metodo_pago',
        'monto',
        'cambio',
        'num_ticket',
        'num_tarjeta',
        'order_id',
    ];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }
}

       

