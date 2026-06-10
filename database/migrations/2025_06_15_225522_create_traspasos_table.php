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
        Schema::create('traspasos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->date('fecha');
            $table->time('hora');
            $table->string('estado')->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->foreignId('responsable')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('sucursal_origen')->constrained('branches')->onDelete('restrict');
            $table->foreignId('sucursal_destino')->constrained('branches')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traspasos');
    }
};
