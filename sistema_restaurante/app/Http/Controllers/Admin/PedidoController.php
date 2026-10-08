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
        $tipoDomicilio = \App\Models\TipoPedido::where('nombre', 'like', '%domicilio%')->first();
        $tipoDomicilioId = $tipoDomicilio ? $tipoDomicilio->id : null;

        $query = Pedido::with([
            'cliente.user',
            'mesero.user',
            'tipoPedido',
            'estadoPedido',
            'detalles.producto'
        ]);

        if ($tipoDomicilioId) {
            $query->where('tipo_pedido_id', '!=', $tipoDomicilioId);
        }

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
        $basePedidos = Pedido::query();
        if ($tipoDomicilioId) {
            $basePedidos->where('tipo_pedido_id', '!=', $tipoDomicilioId);
        }

        $pedidosHoy = (clone $basePedidos)->whereDate('created_at', now()->today())->count();
        
        $enPreparacion = (clone $basePedidos)->whereHas('estadoPedido', function ($q) {
            $q->where('nombre_estado', 'like', '%preparaci%');
        })->count();

        $listos = (clone $basePedidos)->whereHas('estadoPedido', function ($q) {
            $q->where('nombre_estado', 'Listo');
        })->count();

        $completadosHoy = (clone $basePedidos)->whereDate('created_at', now()->today())
            ->whereHas('estadoPedido', function ($q) {
                $q->where('nombre_estado', 'Entregado');
            })->count();

        $canceladosHoy = (clone $basePedidos)->whereDate('created_at', now()->today())
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

        $obs = $pedido->observaciones ?? '';
        $direccion = $obs;
        if (str_contains($obs, 'Dirección:')) {
            $direccion = trim(explode('|', explode('Dirección:', $obs)[1])[0]);
        } elseif (str_contains($obs, 'Mesa')) {
            $direccion = trim(explode('|', $obs)[0]);
        } elseif (str_contains($obs, 'Para llevar')) {
            $direccion = trim(explode('|', $obs)[0]);
        }

        return response()->json([
            'id' => $pedido->id,
            'codigo' => '#ORD-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
            'cliente' => $pedido->cliente->user->name ?? 'Cliente',
            'telefono' => $pedido->cliente->user->telefono ?? 'N/A',
            'tipo' => strtolower($pedido->tipoPedido->nombre ?? 'mesa'),
            'observaciones' => $obs,
            'direccion' => $direccion,
            'estado' => $pedido->estadoPedido->nombre_estado ?? 'Pendiente',
            'estado_id' => $pedido->estado_pedido_id,
            'total' => '$' . number_format($pedido->total, 0, ',', '.'),
            'fecha' => $pedido->created_at ? $pedido->created_at->format('d/m/Y') : 'N/A',
            'detalles' => $detallesFormateados,
        ]);
    }

    public function destroy(Pedido $pedido)
    {
        try {
            $codigo = '#ORD-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT);
            $estadoCancelado = EstadoPedido::where('nombre_estado', 'like', '%cancelado%')->first();
            
            if ($estadoCancelado) {
                $pedido->update(['estado_pedido_id' => $estadoCancelado->id]);
            }

            // Reintegrar inventario
            foreach ($pedido->detalles as $detalle) {
                $inv = \App\Models\Inventario::where('producto_id', $detalle->producto_id)->first();
                if ($inv) {
                    $inv->increment('cantidad', $detalle->cantidad);
                    \App\Models\MovimientoInventario::create([
                        'inventario_id' => $inv->id,
                        'tipo' => 'Entrada',
                        'cantidad' => $detalle->cantidad,
                        'motivo' => "Cancelación administrativa de {$codigo}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            return redirect()->route('admin.pedidos.index')
                ->with('success', "El pedido {$codigo} ha sido cancelado exitosamente y su stock reintegrado.");
        } catch (\Exception $e) {
            return redirect()->route('admin.pedidos.index')
                ->with('error', "Error al cancelar el pedido: " . $e->getMessage());
        }
    }
}
