<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Cliente;
use App\Models\User;
use App\Models\TipoPedido;
use App\Models\EstadoPedido;
use App\Models\Producto;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $userHades = User::where('email', 'hades@gmail.com')->first();
        if (!$userHades) {
            $userHades = User::first();
        }

        $cliente = Cliente::firstOrCreate(['user_id' => $userHades->id]);
        $tipoMesa = TipoPedido::where('nombre', 'Mesa')->first() ?? TipoPedido::first();
        $estadoPendiente = EstadoPedido::where('nombre_estado', 'Pendiente')->first() ?? EstadoPedido::first();
        $producto = Producto::first();

        if (Pedido::count() === 0 && $producto) {
            $pedido = Pedido::create([
                'id' => 19,
                'cliente_id' => $cliente->id,
                'mesero_id' => null,
                'tipo_pedido_id' => $tipoMesa->id,
                'estado_pedido_id' => $estadoPendiente->id,
                'total' => 10000,
                'observaciones' => 'Mesa Salón - Sin cebolla',
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $producto->id,
                'cantidad' => 1,
                'precio_unitario' => 10000,
                'subtotal' => 10000,
            ]);
        }
    }
}
