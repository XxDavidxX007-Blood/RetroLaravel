<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('generado_por')->default('Administrador');
            $table->string('filtro_usado')->default('Todo el Inventario');
            $table->string('formato')->default('Ver en Pantalla');
            $table->integer('total_productos')->default(0);
            $table->decimal('valor_total', 14, 2)->default(0);
            $table->json('datos_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
