<?php

namespace Database\Seeders;

use App\Models\EstadoReserva;
use Illuminate\Database\Seeder;

class EstadoReservaSeeder extends Seeder
{
    public function run(): void
    {
        EstadoReserva::create([
            'nombre_estado' => 'Pendiente',
        ]);

        EstadoReserva::create([
            'nombre_estado' => 'Confirmada',
        ]);

        EstadoReserva::create([
            'nombre_estado' => 'Cancelada',
        ]);

        EstadoReserva::create([
            'nombre_estado' => 'Completada',
        ]);

        EstadoReserva::create([
            'nombre_estado' => 'No asistió',
        ]);
    }
}