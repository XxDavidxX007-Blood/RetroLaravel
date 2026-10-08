<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            // Eliminar la FK existente y volver a crearla como nullable
            $table->dropForeign(['cliente_id']);
            $table->foreignId('cliente_id')
                ->nullable()
                ->change()
                ->constrained('clientes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->foreignId('cliente_id')
                ->nullable(false)
                ->change()
                ->constrained('clientes')
                ->cascadeOnDelete();
        });
    }
};
