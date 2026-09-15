<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nombre', 'Administrador')->first();
        $empleadoRole = Role::where('nombre', 'Empleado')->first();
        $clienteRole = Role::where('nombre', 'Cliente')->first();

        $adminId = $adminRole ? $adminRole->id : 1;
        $empleadoId = $empleadoRole ? $empleadoRole->id : 2;
        $clienteId = $clienteRole ? $clienteRole->id : 3;

        $usuarios = [
            [
                'name' => 'alicia robles',
                'email' => 'alicia@gmail.com',
                'telefono' => '3204417080',
                'role_id' => $clienteId,
                'estado' => 'Activo',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'hades perez',
                'email' => 'hades@gmail.com',
                'telefono' => '3651916255',
                'role_id' => $clienteId,
                'estado' => 'Activo',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'keiner trejos',
                'email' => 'keiner@gmail.com',
                'telefono' => '3201651516',
                'role_id' => $empleadoId,
                'estado' => 'Activo',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'sharon tovar',
                'email' => 'tovar@gmail.com',
                'telefono' => '3204417080',
                'role_id' => $adminId,
                'estado' => 'Activo',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'melani rojas',
                'email' => 'melani@gmail.com',
                'telefono' => '3156218',
                'role_id' => $empleadoId,
                'estado' => 'Activo',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'mario vega',
                'email' => 'mario@gmail.com',
                'telefono' => '321561651',
                'role_id' => $clienteId,
                'estado' => 'Activo',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'david ovalle',
                'email' => 'david@gmail.com',
                'telefono' => '3204417080',
                'role_id' => $adminId,
                'estado' => 'Activo',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($usuarios as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                $u
            );

            if ($user->role_id == $clienteId) {
                \App\Models\Cliente::firstOrCreate(['user_id' => $user->id]);
            } elseif ($user->role_id == $empleadoId) {
                \App\Models\Mesero::firstOrCreate(['user_id' => $user->id]);
            }
        }
    }
}
