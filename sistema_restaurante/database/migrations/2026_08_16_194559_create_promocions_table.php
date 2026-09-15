<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 100);
            $table->string('descripcion', 255);
            $table->decimal('descuento', 5, 2)->default(0);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->index(['fecha_fin', 'activa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};
