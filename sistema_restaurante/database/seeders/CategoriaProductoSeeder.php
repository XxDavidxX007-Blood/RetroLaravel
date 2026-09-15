<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaProducto;

class CategoriaProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Carnes',
            'Lácteos',
            'Verduras',
            'Frutas',
            'Bebidas',
            'Granos y Cereales',
            'Mariscos',
            'Condimentos',
            'Panadería',
            'Postres',
        ];

        foreach ($categorias as $categoria) {
            CategoriaProducto::updateOrCreate(
                ['nombre' => $categoria],
                [
                    'estado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}