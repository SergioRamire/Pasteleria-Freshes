<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Order extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'customer_id',
        'order_date',
        'order_status',
        'total_products',
        'sub_total',
        'vat',
        'invoice_no',
        'num_ticket',
        'num_tarjeta',
        'metodo_pago',
        'enviar',
        'total',
        'payment_status',
        'pay',
        'due',
        'branche_id',
        'user_id',
    ];

    public $sortable = [
        'customer_id',
        'order_date',
        'pay',
        'due',
        'total',
    ];

    protected $guarded = [
        'id',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function sucursal(){
        return $this->belongsTo(Branche::class, 'branche_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
