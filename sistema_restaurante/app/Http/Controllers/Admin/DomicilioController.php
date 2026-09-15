<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\TipoPedido;
use App\Models\EstadoPedido;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Producto;
use App\Models\DetallePedido;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomicilioController extends Controller
{
    public function index(Request $request)
    {
        $tipoDomicilio = TipoPedido::where('nombre', 'like', '%domicilio%')->first();
        $tipoDomicilioId = $tipoDomicilio ? $tipoDomicilio->id : null;

        $query = Pedido::with([
            'cliente.user',
            'mesero.user',
            'tipoPedido',
            'estadoPedido',
            'detalles.producto'
        ]);

        if ($tipoDomicilioId) {
            $query->where('tipo_pedido_id', $tipoDomicilioId);
        }

        // Filtro por Estado (Tabs)
        $tabActual = $request->input('estado', 'todos');
        if ($tabActual !== 'todos' && !empty($tabActual)) {
            $estadoFiltro = strtolower(trim($tabActual));
            $query->whereHas('estadoPedido', function ($q) use ($estadoFiltro) {
                if (in_array($estadoFiltro, ['en_preparacion', 'en preparacion', 'en preparación'])) {
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
                  ->orWhere('observaciones', 'like', "%{$search}%")
                  ->orWhereHas('cliente.user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%");
                  });
            });
        }

        $domicilios = $query->latest()->paginate(8)->withQueryString();
        $estados = EstadoPedido::all();
        $clientes = Cliente::with('user')->get();
        $productos = Producto::where('estado', true)->with('inventario')->get();

        // ===== MÉTRICAS =====
        $baseDomicilios = Pedido::query();
        if ($tipoDomicilioId) {
            $baseDomicilios->where('tipo_pedido_id', $tipoDomicilioId);
        }

        $domiciliosHoy = (clone $baseDomicilios)->whereDate('created_at', now()->today())->count();
        $domiciliosAyer = (clone $baseDomicilios)->whereDate('created_at', now()->yesterday())->count();

        // Cálculo porcentaje vs ayer
        if ($domiciliosAyer > 0) {
            $dif = $domiciliosHoy - $domiciliosAyer;
            $porcentajeVsAyer = round(($dif / $domiciliosAyer) * 100);
            $signo = $porcentajeVsAyer >= 0 ? '+' : '';
            $textoVsAyer = "{$signo}{$porcentajeVsAyer}% vs ayer";
        } else {
            $textoVsAyer = "+0% vs ayer";
        }

        $pendientes = (clone $baseDomicilios)->whereHas('estadoPedido', function ($q) {
            $q->where('nombre_estado', 'like', '%pendiente%');
        })->count();

        $enPreparacion = (clone $baseDomicilios)->whereHas('estadoPedido', function ($q) {
            $q->where('nombre_estado', 'like', '%preparaci%');
        })->count();

        $entregados = (clone $baseDomicilios)->whereHas('estadoPedido', function ($q) {
            $q->where('nombre_estado', 'like', '%entregado%');
        })->count();

        $cancelados = (clone $baseDomicilios)->whereHas('estadoPedido', function ($q) {
            $q->where('nombre_estado', 'like', '%cancelado%');
        })->count();

        return view('admin.domicilios.index', compact(
            'domicilios',
            'estados',
            'clientes',
            'productos',
            'domiciliosHoy',
            'textoVsAyer',
            'pendientes',
            'enPreparacion',
            'entregados',
            'cancelados',
            'tabActual'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre_cliente' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:500',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $tipoDomicilio = TipoPedido::where('nombre', 'like', '%domicilio%')->firstOrFail();
            $estadoInicial = EstadoPedido::where('nombre_estado', 'like', '%pendiente%')->firstOrFail();

            // Obtener o crear cliente
            $clienteId = $request->input('cliente_id');

            if (!$clienteId && $request->filled('nombre_cliente')) {
                // Crear usuario temporal para el cliente
                $emailTemp = 'cliente_' . time() . '_' . rand(100, 999) . '@retrorestaurant.com';
                $user = User::create([
                    'name' => $request->input('nombre_cliente'),
                    'apellidos' => '',
                    'email' => $emailTemp,
                    'telefono' => $request->input('telefono'),
                    'password' => bcrypt('retro123'),
                    'role_id' => 3, // Cliente
                    'estado' => 'Activo',
                ]);

                $cliente = Cliente::create([
                    'user_id' => $user->id,
                ]);

                $clienteId = $cliente->id;
            } elseif (!$clienteId) {
                // Tomar primer cliente por defecto
                $primerCliente = Cliente::first();
                if (!$primerCliente) {
                    $user = User::first();
                    $primerCliente = Cliente::create(['user_id' => $user->id]);
                }
                $clienteId = $primerCliente->id;
            }

            // Preparar observaciones con dirección
            $direccion = $request->input('direccion');
            $obsExtra = $request->input('observaciones');
            $observacionesFinal = "Dirección: {$direccion}" . ($obsExtra ? " | Nota: {$obsExtra}" : '');

            // Crear Pedido
            $pedido = Pedido::create([
                'cliente_id' => $clienteId,
                'mesero_id' => null,
                'tipo_pedido_id' => $tipoDomicilio->id,
                'estado_pedido_id' => $estadoInicial->id,
                'total' => 0,
                'observaciones' => $observacionesFinal,
            ]);

            $totalAcumulado = 0;

            foreach ($request->input('productos') as $item) {
                $producto = Producto::find($item['id']);
                if (!$producto) continue;

                $cant = (int)$item['cantidad'];
                $precio = (float)$producto->precio;
                $subtotal = $cant * $precio;
                $totalAcumulado += $subtotal;

                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cant,
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotal,
                ]);

                // Descontar inventario si existe
                $inventario = Inventario::where('producto_id', $producto->id)->first();
                if ($inventario) {
                    $stockAnterior = $inventario->cantidad;
                    $inventario->cantidad = max(0, $inventario->cantidad - $cant);
                    $inventario->save();

                    MovimientoInventario::create([
                        'inventario_id' => $inventario->id,
                        'tipo_movimiento' => 'Salida',
                        'cantidad' => $cant,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $inventario->cantidad,
                        'motivo' => "Domicilio #DOM-" . str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            $pedido->update(['total' => $totalAcumulado]);

            DB::commit();

            return redirect()->route('admin.domicilios.index')
                ->with('success', 'Pedido a domicilio registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.domicilios.index')
                ->with('error', 'Error al registrar el domicilio: ' . $e->getMessage());
        }
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

        return redirect()->route('admin.domicilios.index')
            ->with('success', "El domicilio #DOM-" . str_pad($pedido->id, 5, '0', STR_PAD_LEFT) . " fue actualizado a estado: {$nombreEstado}.");
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
            'codigo' => '#DOM-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
            'cliente' => ($pedido->cliente->user->name ?? 'Cliente') . ' ' . ($pedido->cliente->user->apellidos ?? ''),
            'telefono' => $pedido->cliente->user->telefono ?? 'N/A',
            'observaciones' => $pedido->observaciones ?? 'Sin notas de entrega',
            'estado' => $pedido->estadoPedido->nombre_estado ?? 'Pendiente',
            'estado_id' => $pedido->estado_pedido_id,
            'total' => '$' . number_format($pedido->total, 0, ',', '.'),
            'fecha' => $pedido->created_at ? $pedido->created_at->format('d/m/Y H:i A') : 'N/A',
            'detalles' => $detallesFormateados,
        ]);
    }

    public function destroy(Pedido $pedido)
    {
        try {
            $estadoCancelado = EstadoPedido::where('nombre_estado', 'like', '%cancelado%')->first();
            if ($estadoCancelado) {
                $pedido->update(['estado_pedido_id' => $estadoCancelado->id]);
            }
            return redirect()->route('admin.domicilios.index')
                ->with('success', "El domicilio #DOM-" . str_pad($pedido->id, 5, '0', STR_PAD_LEFT) . " ha sido cancelado.");
        } catch (\Exception $e) {
            return redirect()->route('admin.domicilios.index')
                ->with('error', 'Error al cancelar el domicilio: ' . $e->getMessage());
        }
    }
}
