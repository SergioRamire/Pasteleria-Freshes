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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_minimo');
            $table->integer('stock');
            $table->boolean('estado')->nullable();
            $table->boolean('disponibilidad')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('restrict');
            $table->foreignId('branche_id')->nullable()->constrained('branches')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
