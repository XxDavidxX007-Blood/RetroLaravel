<?php

namespace Database\Seeders;

use App\Models\TipoPedido;
use Illuminate\Database\Seeder;

class TipoPedidoSeeder extends Seeder
{
    public function run(): void
    {
        TipoPedido::create([
            'nombre' => 'Mesa',
            'descripcion' => 'Pedido realizado en el restaurante',
        ]);

        TipoPedido::create([
            'nombre' => 'Domicilio',
            'descripcion' => 'Pedido para entrega a domicilio',
        ]);

        TipoPedido::create([
            'nombre' => 'Para llevar',
            'descripcion' => 'Pedido para recoger',
        ]);
    }
}