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
        Schema::create('comprasdetalles', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->foreignId('producto_id')->nullable()->constrained('products')->onDelete('restrict');
            $table->foreignId('inventario_id')->nullable()->constrained('inventarios')->onDelete('restrict');
            $table->foreignId('compra_id')->nullable()->constrained('compras')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprasdetalles');
    }
};
