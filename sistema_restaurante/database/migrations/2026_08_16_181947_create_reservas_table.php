<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->foreignId('mesa_id')
                ->constrained('mesas')
                ->restrictOnDelete();

            $table->date('fecha_reserva');
            $table->time('hora_reserva');
            $table->integer('cantidad_personas');
            $table->text('observaciones')->nullable();

            $table->foreignId('estado_reserva_id')
                ->constrained('estado_reservas')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
