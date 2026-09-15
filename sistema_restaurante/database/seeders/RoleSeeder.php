<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Acceso completo al sistema']
        );

        Role::updateOrCreate(
            ['nombre' => 'Empleado'],
            ['descripcion' => 'Acceso a las funciones operativas']
        );

        Role::updateOrCreate(
            ['nombre' => 'Cliente'],
            ['descripcion' => 'Acceso para clientes']
        );
    }
}