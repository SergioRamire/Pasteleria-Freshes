<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversion_histories', function (Blueprint $table) {
            $table->id();

            // Información del usuario que realizó la conversión
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branche_id');

            // Producto origen (el que se convierte/reduce)
            $table->unsignedBigInteger('producto_origen_id');
            $table->string('producto_origen_nombre');
            $table->string('producto_origen_codigo');
            $table->string('producto_origen_unidad');
            $table->integer('cantidad_origen'); // Cantidad que se descontó

            // Producto destino (el que se genera/aumenta)
            $table->unsignedBigInteger('producto_destino_id');
            $table->string('producto_destino_nombre');
            $table->string('producto_destino_codigo');
            $table->string('producto_destino_unidad');
            $table->integer('cantidad_destino'); // Cantidad que se generó

            // Factor de conversión aplicado
            $table->integer('factor_conversion');

            // Totales calculados
            $table->integer('total_unidades_generadas'); // cantidad_origen * factor_conversion

            // Stocks antes de la conversión (para referencia)
            $table->integer('stock_origen_anterior');
            $table->integer('stock_destino_anterior');

            // Stocks después de la conversión
            $table->integer('stock_origen_nuevo');
            $table->integer('stock_destino_nuevo');

            // Estado de la conversión
            $table->enum('estado', ['completada', 'revertida'])->default('completada');

            // Observaciones adicionales
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Índices
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('branche_id')->references('id')->on('branches');
            $table->foreign('producto_origen_id')->references('id')->on('products');
            $table->foreign('producto_destino_id')->references('id')->on('products');

            // Índices adicionales para consultas frecuentes
            $table->index(['branche_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['producto_origen_id', 'created_at']);
            $table->index(['producto_destino_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversion_histories');
    }
};
