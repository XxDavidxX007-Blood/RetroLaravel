<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\EstadoPedido;
use App\Models\Cliente;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with([
            'cliente.user',
            'mesero.user',
            'tipoPedido',
            'estadoPedido',
            'detalles.producto'
        ]);

        // Filtro por Estado (Tabs)
        if ($request->filled('estado') && $request->input('estado') !== 'todos') {
            $estadoFiltro = strtolower(trim($request->input('estado')));
            $query->whereHas('estadoPedido', function ($q) use ($estadoFiltro) {
                if ($estadoFiltro === 'en_preparacion' || $estadoFiltro === 'en preparacion' || $estadoFiltro === 'en preparación') {
                    $q->where('nombre_estado', 'like', '%preparaci%');
                } else {
                    $q->where('nombre_estado', 'like', "%{$estadoFiltro}%");
                }
            });
        }

        // Filtro por Fecha
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->input('fecha'));
        }

        // Buscador
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('cliente.user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%");
                  });
            });
        }

        $pedidos = $query->latest()->paginate(7)->withQueryString();
        $estados = EstadoPedido::all();

        // ===== MÉTRICAS =====
        $pedidosHoy = Pedido::whereDate('created_at', now()->today())->count();
        
        $enPreparacion = Pedido::whereHas('estadoPedido', function ($q) {
            $q->where('nombre_estado', 'like', '%preparaci%');
        })->count();

        $listos = Pedido::whereHas('estadoPedido', function ($q) {
            $q->where('nombre_estado', 'Listo');
        })->count();

        $completadosHoy = Pedido::whereDate('created_at', now()->today())
            ->whereHas('estadoPedido', function ($q) {
                $q->where('nombre_estado', 'Entregado');
            })->count();

        $canceladosHoy = Pedido::whereDate('created_at', now()->today())
            ->whereHas('estadoPedido', function ($q) {
                $q->where('nombre_estado', 'Cancelado');
            })->count();

        return view('admin.pedidos.index', compact(
            'pedidos',
            'estados',
            'pedidosHoy',
            'enPreparacion',
            'listos',
            'completadosHoy',
            'canceladosHoy'
        ));
    }

    public function updateEstado(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado_pedido_id' => 'required|exists:estados_pedido,id',
        ]);

        $pedido->update([
            'estado_pedido_id' => $request->input('estado_pedido_id'),
        ]);

        $nuevoEstado = EstadoPedido::find($request->input('estado_pedido_id'));
        $nombreEstado = $nuevoEstado ? $nuevoEstado->nombre_estado : 'actualizado';

        return redirect()->route('admin.pedidos.index')
            ->with('success', "El pedido #ORD-" . str_pad($pedido->id, 5, '0', STR_PAD_LEFT) . " fue cambiado a estado: {$nombreEstado}.");
    }

    public function detalles(Pedido $pedido)
    {
        $pedido->load([
            'cliente.user',
            'mesero.user',
            'tipoPedido',
            'estadoPedido',
            'detalles.producto'
        ]);

        $detallesFormateados = $pedido->detalles->map(function ($d) {
            return [
                'producto' => $d->producto->nombre ?? 'Producto',
                'cantidad' => $d->cantidad,
                'precio_unitario' => '$' . number_format($d->precio_unitario, 0, ',', '.'),
                'subtotal' => '$' . number_format($d->subtotal, 0, ',', '.'),
            ];
        });

        return response()->json([
            'id' => $pedido->id,
            'codigo' => '#ORD-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
            'cliente' => $pedido->cliente->user->name ?? 'Cliente',
            'telefono' => $pedido->cliente->user->telefono ?? 'N/A',
            'tipo' => $pedido->tipoPedido->nombre ?? 'Mesa',
            'observaciones' => $pedido->observaciones ?? 'Sin observaciones',
            'estado' => $pedido->estadoPedido->nombre_estado ?? 'Pendiente',
            'estado_id' => $pedido->estado_pedido_id,
            'total' => '$' . number_format($pedido->total, 0, ',', '.'),
            'fecha' => $pedido->created_at ? $pedido->created_at->format('d/m/Y H:i A') : 'N/A',
            'detalles' => $detallesFormateados,
        ]);
    }
}
