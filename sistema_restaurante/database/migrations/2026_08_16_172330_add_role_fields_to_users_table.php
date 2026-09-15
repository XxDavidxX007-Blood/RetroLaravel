<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellidos')->nullable()->after('name');
            $table->string('telefono', 20)->nullable()->after('email');
            $table->string('foto')->nullable()->after('password');
            $table->foreignId('role_id')
                ->nullable()
                ->after('foto')
                ->constrained('roles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'apellidos',
                'telefono',
                'foto',
                'role_id',
            ]);
        });
    }
};