<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->restrictOnDelete();

            $table->foreignId('mesero_id')
                ->nullable()
                ->constrained('meseros')
                ->nullOnDelete();

            $table->foreignId('tipo_pedido_id')
                ->constrained('tipos_pedido')
                ->restrictOnDelete();

            $table->foreignId('estado_pedido_id')
                ->constrained('estados_pedido')
                ->restrictOnDelete();

            $table->decimal('total', 10, 2)->default(0);

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
