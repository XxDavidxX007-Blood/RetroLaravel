<?php

namespace Database\Seeders;

use App\Models\EstadoMesa;
use Illuminate\Database\Seeder;

class EstadoMesaSeeder extends Seeder
{
    public function run(): void
    {
        EstadoMesa::create(['nombre_estado' => 'Disponible']);
        EstadoMesa::create(['nombre_estado' => 'Ocupada']);
        EstadoMesa::create(['nombre_estado' => 'Reservada']);
        EstadoMesa::create(['nombre_estado' => 'Mantenimiento']);
    }
}