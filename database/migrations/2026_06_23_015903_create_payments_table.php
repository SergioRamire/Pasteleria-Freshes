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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique()->required();
            $table->date('fecha')->required();
            $table->string('metodo_pago')->required();
            $table->decimal('monto', 10, 2)->required();
            $table->decimal('cambio', 10, 2)->nullable();
            $table->string('num_ticket')->nullable();
            $table->string('num_tarjeta')->nullable();
            $table->foreignId('order_id')->constrained('orders')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
