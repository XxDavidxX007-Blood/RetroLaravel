<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            EstadoMesaSeeder::class,
            EstadoReservaSeeder::class,
            EstadoPedidoSeeder::class,
            TipoPedidoSeeder::class,
            CategoriaProductoSeeder::class,
            MesaSeeder::class,
            UserSeeder::class,
            ProductoSeeder::class,
        ]);
    }
}