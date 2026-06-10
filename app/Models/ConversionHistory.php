<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branche_id',
        'producto_origen_id',
        'producto_origen_nombre',
        'producto_origen_codigo',
        'producto_origen_unidad',
        'cantidad_origen',
        'producto_destino_id',
        'producto_destino_nombre',
        'producto_destino_codigo',
        'producto_destino_unidad',
        'cantidad_destino',
        'factor_conversion',
        'total_unidades_generadas',
        'stock_origen_anterior',
        'stock_destino_anterior',
        'stock_origen_nuevo',
        'stock_destino_nuevo',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branche()
    {
        return $this->belongsTo(Branche::class);
    }

    public function productoOrigen()
    {
        return $this->belongsTo(Product::class, 'producto_origen_id');
    }

    public function productoDestino()
    {
        return $this->belongsTo(Product::class, 'producto_destino_id');
    }

    // Scopes
    public function scopeByBranche($query, $brancheId)
    {
        return $query->where('branche_id', $brancheId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProducto($query, $productId)
    {
        return $query->where('producto_origen_id', $productId)
                    ->orWhere('producto_destino_id', $productId);
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeRevertidas($query)
    {
        return $query->where('estado', 'revertida');
    }

    // Accessors
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getResumenConversionAttribute()
    {
        return "{$this->cantidad_origen} {$this->producto_origen_unidad} → {$this->total_unidades_generadas} {$this->producto_destino_unidad}";
    }

    // Métodos auxiliares
    public static function registrarConversion(
        $userId,
        $brancheId,
        $inventarioOrigen,
        $productoOrigen,
        $cantidadOrigen,
        $inventarioDestino,
        $productoDestino,
        $cantidadDestino,
        $factorConversion,
        $stockOrigenAnterior,
        $stockDestinoAnterior,
        $observaciones = null
    ) {
        return self::create([
            'user_id' => $userId,
            'branche_id' => $brancheId,
            'producto_origen_id' => $productoOrigen->id,
            'producto_origen_nombre' => $productoOrigen->product_name,
            'producto_origen_codigo' => $productoOrigen->product_code,
            'producto_origen_unidad' => $productoOrigen->equivalencia->abreviatura ?? 'Unidad',
            'cantidad_origen' => $cantidadOrigen,
            'producto_destino_id' => $productoDestino->id,
            'producto_destino_nombre' => $productoDestino->product_name,
            'producto_destino_codigo' => $productoDestino->product_code,
            'producto_destino_unidad' => $productoDestino->equivalencia->abreviatura ?? 'Unidad',
            'cantidad_destino' => $cantidadDestino,
            'factor_conversion' => $factorConversion,
            'total_unidades_generadas' => $cantidadOrigen * $factorConversion,
            'stock_origen_anterior' => $stockOrigenAnterior,
            'stock_destino_anterior' => $stockDestinoAnterior,
            'stock_origen_nuevo' => $inventarioOrigen->stock,
            'stock_destino_nuevo' => $inventarioDestino->stock,
            'observaciones' => $observaciones
        ]);
    }
}
