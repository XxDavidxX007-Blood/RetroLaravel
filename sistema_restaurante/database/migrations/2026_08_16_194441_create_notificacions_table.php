<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo', 30)->default('general');
            $table->string('titulo', 150);
            $table->string('mensaje', 500);
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'leida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
