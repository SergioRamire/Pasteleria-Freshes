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
        Schema::create('traspasosdetalles', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->foreignId('producto_id')->nullable()->constrained('products')->onDelete('restrict');
            $table->foreignId('inventario_id')->nullable()->constrained('inventarios')->onDelete('restrict');
            $table->foreignId('traspaso_id')->nullable()->constrained('traspasos')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traspasosdetalles');
    }
};
