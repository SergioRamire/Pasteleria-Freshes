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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('phone2')->nullable();
            $table->string('shopname')->nullable();
            $table->string('photo')->nullable();
            $table->string('regimen_fiscal')->nullable();
            $table->string('uso_cfdi')->nullable();
            $table->string('city')->nullable();
            $table->string('type_customer')->nullable();
            $table->string('rfc')->nullable()->unique();
            $table->string('tipo_persona')->nullable();
            $table->string('num_interior')->nullable();
            $table->string('num_exterior')->nullable();
            $table->string('calle')->nullable();
            $table->string('colonia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('estado')->nullable();
            $table->string('pais')->nullable();
            $table->string('referencia')->nullable();
            $table->integer('cp')->nullable();
            $table->string('rul_maps')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
