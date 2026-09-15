<?php

namespace Database\Seeders;

use App\Models\Mesa;
use App\Models\EstadoMesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    public function run(): void
    {
        $disponible = EstadoMesa::where('nombre_estado', 'Disponible')->first();

        if (!$disponible) {
            $this->command->warn('No se encontró el estado "Disponible".');
            return;
        }

        $mesas = [
            ['numero_mesa' => 1,  'capacidad' => 2,  'ubicacion' => 'Zona ventana'],
            ['numero_mesa' => 2,  'capacidad' => 2,  'ubicacion' => 'Zona ventana'],
            ['numero_mesa' => 3,  'capacidad' => 4,  'ubicacion' => 'Centro'],
            ['numero_mesa' => 4,  'capacidad' => 4,  'ubicacion' => 'Centro'],
            ['numero_mesa' => 5,  'capacidad' => 4,  'ubicacion' => 'Centro'],
            ['numero_mesa' => 6,  'capacidad' => 6,  'ubicacion' => 'Zona lateral'],
            ['numero_mesa' => 7,  'capacidad' => 6,  'ubicacion' => 'Zona lateral'],
            ['numero_mesa' => 8,  'capacidad' => 8,  'ubicacion' => 'Terraza'],
            ['numero_mesa' => 9,  'capacidad' => 8,  'ubicacion' => 'Terraza'],
            ['numero_mesa' => 10, 'capacidad' => 10, 'ubicacion' => 'Salón privado'],
        ];

        foreach ($mesas as $mesa) {
            Mesa::updateOrCreate(
                ['numero_mesa' => $mesa['numero_mesa']],
                [
                    'capacidad' => $mesa['capacidad'],
                    'ubicacion' => $mesa['ubicacion'],
                    'estado_mesa_id' => $disponible->id,
                ]
            );
        }

        $this->command->info('10 mesas creadas correctamente.');
    }
}