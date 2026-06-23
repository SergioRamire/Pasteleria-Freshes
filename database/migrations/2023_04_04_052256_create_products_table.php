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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->integer('category_id');
            $table->string('product_code')->nullable();
            $table->string('codigo_barras')->nullable()->unique();
            // $table->string('clave_sat')->nullable();
            $table->string('product_garage')->nullable();
            $table->string('product_image')->nullable();
            $table->integer('product_store')->nullable();
            $table->date('buying_date')->nullable();
            $table->boolean('status_product')->nullable();
            $table->string('expire_date')->nullable();
            $table->decimal('buying_price')->nullable();
            $table->decimal('selling_price')->nullable();
            $table->decimal('dealer_price')->nullable();
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->onDelete('restrict');
            $table->foreignId('equivalencia_id')->nullable()->constrained('equivalencias')->onDelete('restrict');
            // $table->foreignId('satclave_id')->nullable()->constrained('satclaves')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
