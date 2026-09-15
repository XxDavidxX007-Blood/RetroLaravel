<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\CategoriaProducto;
use App\Models\Inventario;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener categorías por nombre
        $carnes    = CategoriaProducto::where('nombre', 'Carnes')->first();
        $bebidas   = CategoriaProducto::where('nombre', 'Bebidas')->first();
        $postres   = CategoriaProducto::where('nombre', 'Postres')->first();
        $mariscos  = CategoriaProducto::where('nombre', 'Mariscos')->first();
        $verduras  = CategoriaProducto::where('nombre', 'Verduras')->first();
        $panaderia = CategoriaProducto::where('nombre', 'Panadería')->first();

        $productos = [
            // Carnes
            ['nombre' => 'Hamburguesa Clásica',       'precio' => 15000, 'categoria' => $carnes,    'stock' => 50],
            ['nombre' => 'Hamburguesa Doble Queso',   'precio' => 20000, 'categoria' => $carnes,    'stock' => 40],
            ['nombre' => 'Costillas BBQ',             'precio' => 28000, 'categoria' => $carnes,    'stock' => 25],
            ['nombre' => 'Pechuga a la Plancha',      'precio' => 18000, 'categoria' => $carnes,    'stock' => 30],
            ['nombre' => 'Carne Mechada',             'precio' => 22000, 'categoria' => $carnes,    'stock' => 20],

            // Bebidas
            ['nombre' => 'Limonada Natural',          'precio' => 5000,  'categoria' => $bebidas,   'stock' => 100],
            ['nombre' => 'Jugo de Naranja',           'precio' => 5000,  'categoria' => $bebidas,   'stock' => 80],
            ['nombre' => 'Cerveza Artesanal',         'precio' => 8000,  'categoria' => $bebidas,   'stock' => 60],
            ['nombre' => 'Coca-Cola 500ml',           'precio' => 4000,  'categoria' => $bebidas,   'stock' => 120],
            ['nombre' => 'Agua Mineral',              'precio' => 3000,  'categoria' => $bebidas,   'stock' => 150],

            // Postres
            ['nombre' => 'Tiramisú',                  'precio' => 12000, 'categoria' => $postres,   'stock' => 15],
            ['nombre' => 'Cheesecake',                'precio' => 10000, 'categoria' => $postres,   'stock' => 20],
            ['nombre' => 'Helado Artesanal',          'precio' => 7000,  'categoria' => $postres,   'stock' => 30],

            // Mariscos
            ['nombre' => 'Ceviche de Camarón',        'precio' => 25000, 'categoria' => $mariscos,  'stock' => 20],
            ['nombre' => 'Camarones Al Ajillo',       'precio' => 27000, 'categoria' => $mariscos,  'stock' => 18],

            // Verduras
            ['nombre' => 'Ensalada César',            'precio' => 12000, 'categoria' => $verduras,  'stock' => 35],
            ['nombre' => 'Ensalada Tropical',         'precio' => 14000, 'categoria' => $verduras,  'stock' => 30],

            // Panadería
            ['nombre' => 'Pan de Ajo',                'precio' => 6000,  'categoria' => $panaderia, 'stock' => 40],
            ['nombre' => 'Bruschetta',                'precio' => 8000,  'categoria' => $panaderia, 'stock' => 25],
        ];

        foreach ($productos as $p) {
            if (!$p['categoria']) {
                $this->command->warn("Categoría no encontrada para: {$p['nombre']}");
                continue;
            }

            $producto = Producto::updateOrCreate(
                ['nombre' => $p['nombre']],
                [
                    'categoria_producto_id' => $p['categoria']->id,
                    'precio' => $p['precio'],
                    'descripcion' => "Delicioso {$p['nombre']} preparado en Retro Restaurant",
                    'estado' => true,
                ]
            );

            // Crear inventario si no existe
            Inventario::updateOrCreate(
                ['producto_id' => $producto->id],
                [
                    'cantidad' => $p['stock'],
                    'stock_minimo' => 5,
                    'stock_maximo' => $p['stock'] * 2,
                ]
            );
        }

        $this->command->info(count($productos) . ' productos creados con su inventario.');
    }
}