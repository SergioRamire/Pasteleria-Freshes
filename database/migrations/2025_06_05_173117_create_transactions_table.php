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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_transaccion'); // Ejemplo: 'ingreso', 'egreso'
            $table->string('metodo_pago'); // Ejemplo: 'efectivo', 'tarjeta', etc.
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('total', 10, 2);
            $table->decimal('monto', 10, 2);
            $table->string('descripcion')->nullable();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
