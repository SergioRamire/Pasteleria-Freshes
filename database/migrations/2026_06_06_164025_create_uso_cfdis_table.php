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
        Schema::create('uso_cfdis', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique(); // Ej: "G01", "P01"
            $table->string('description', 150);  // Ej: "Adquisición de mercancías"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uso_cfdis');
    }
};
