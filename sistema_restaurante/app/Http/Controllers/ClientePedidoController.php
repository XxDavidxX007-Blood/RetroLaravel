<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\TipoPedido;
use App\Models\EstadoPedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetallePedido;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClientePedidoController extends Controller
{
    /**
     * Muestra el módulo "Mis Pedidos" del cliente (Mesa y Para llevar)
     */
    public function indexPedidos(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $cliente = Cliente::firstOrCreate(['user_id' => $user->id]);

        $tipoDomicilio = TipoPedido::where('nombre', 'like', '%domicilio%')->first();
        $tipoDomicilioId = $tipoDomicilio ? $tipoDomicilio->id : 2;

        // Pedidos del cliente que NO sean a domicilio (Mesa y Para llevar)
        $query = Pedido::with([
            'tipoPedido',
            'estadoPedido',
            'detalles.producto.categoria'
        ])
        ->where('cliente_id', $cliente->id)
        ->where('tipo_pedido_id', '!=', $tipoDomicilioId);

        // Filtro por estado
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $estadoFiltro = strtolower($request->estado);
            $query->whereHas('estadoPedido', function ($q) use ($estadoFiltro) {
                if (str_contains($estadoFiltro, 'prepara')) {
                    $q->where('nombre_estado', 'like', '%preparaci%');
                } else {
                    $q->where('nombre_estado', 'like', "%{$estadoFiltro}%");
                }
            });
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('observaciones', 'like', "%{$search}%");
            });
        }

        $pedidos = $query->latest()->paginate(8)->withQueryString();

        // Métricas
        $totalPedidos = Pedido::where('cliente_id', $cliente->id)
            ->where('tipo_pedido_id', '!=', $tipoDomicilioId)
            ->count();

        $enPreparacion = Pedido::where('cliente_id', $cliente->id)
            ->where('tipo_pedido_id', '!=', $tipoDomicilioId)
            ->whereHas('estadoPedido', fn($q) => $q->where('nombre_estado', 'like', '%preparaci%'))
            ->count();

        $pendientes = Pedido::where('cliente_id', $cliente->id)
            ->where('tipo_pedido_id', '!=', $tipoDomicilioId)
            ->whereHas('estadoPedido', fn($q) => $q->where('nombre_estado', 'like', '%pendiente%'))
            ->count();

        $entregados = Pedido::where('cliente_id', $cliente->id)
            ->where('tipo_pedido_id', '!=', $tipoDomicilioId)
            ->whereHas('estadoPedido', fn($q) => $q->where('nombre_estado', 'like', '%entregado%')->orWhere('nombre_estado', 'like', '%listo%'))
            ->count();

        return view('cliente.pedidos', compact(
            'pedidos',
            'totalPedidos',
            'enPreparacion',
            'pendientes',
            'entregados'
        ));
    }

    /**
     * Muestra el módulo "Domicilios" del cliente (Domicilios)
     */
    public function indexDomicilios(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $cliente = Cliente::firstOrCreate(['user_id' => $user->id]);

        $tipoDomicilio = TipoPedido::where('nombre', 'like', '%domicilio%')->first();
        $tipoDomicilioId = $tipoDomicilio ? $tipoDomicilio->id : 2;

        // Solo pedidos a domicilio del cliente
        $query = Pedido::with([
            'tipoPedido',
            'estadoPedido',
            'detalles.producto.categoria'
        ])
        ->where('cliente_id', $cliente->id)
        ->where('tipo_pedido_id', $tipoDomicilioId);

        // Filtro por estado
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $estadoFiltro = strtolower($request->estado);
            $query->whereHas('estadoPedido', function ($q) use ($estadoFiltro) {
                if (str_contains($estadoFiltro, 'prepara')) {
                    $q->where('nombre_estado', 'like', '%preparaci%');
                } else {
                    $q->where('nombre_estado', 'like', "%{$estadoFiltro}%");
                }
            });
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('observaciones', 'like', "%{$search}%");
            });
        }

        $domicilios = $query->latest()->paginate(8)->withQueryString();

        // Métricas
        $totalDomicilios = Pedido::where('cliente_id', $cliente->id)
            ->where('tipo_pedido_id', $tipoDomicilioId)
            ->count();

        $enCamino = Pedido::where('cliente_id', $cliente->id)
            ->where('tipo_pedido_id', $tipoDomicilioId)
            ->whereHas('estadoPedido', fn($q) => $q->where('nombre_estado', 'like', '%preparaci%')->orWhere('nombre_estado', 'like', '%listo%'))
            ->count();

        $pendientes = Pedido::where('cliente_id', $cliente->id)
            ->where('tipo_pedido_id', $tipoDomicilioId)
            ->whereHas('estadoPedido', fn($q) => $q->where('nombre_estado', 'like', '%pendiente%'))
            ->count();

        $entregados = Pedido::where('cliente_id', $cliente->id)
            ->where('tipo_pedido_id', $tipoDomicilioId)
            ->whereHas('estadoPedido', fn($q) => $q->where('nombre_estado', 'like', '%entregado%'))
            ->count();

        return view('cliente.domicilios', compact(
            'domicilios',
            'totalDomicilios',
            'enCamino',
            'pendientes',
            'entregados'
        ));
    }

    /**
     * Registra el pedido o domicilio realizado desde el catálogo / carrito
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para realizar tu pedido.',
                'require_login' => true,
                'login_url' => route('login'),
            ], 401);
        }

        $validated = $request->validate([
            'tipo_pedido' => 'required|in:mesa,domicilio,llevar',
            'mesa_numero' => 'nullable|integer|min:1|max:50',
            'mesa_capacidad' => 'nullable|integer|min:1',
            'direccion' => 'nullable|string|max:300',
            'telefono' => 'nullable|string|max:50',
            'barrio' => 'nullable|string|max:100',
            'nombre_recoge' => 'nullable|string|max:150',
            'hora_recogida' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.observacion' => 'nullable|string|max:300',
        ]);

        $user = Auth::user();

        // Validaciones condicionales según el tipo elegido
        if ($validated['tipo_pedido'] === 'mesa' && empty($validated['mesa_numero'])) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor selecciona el número de mesa para tu pedido.',
            ], 422);
        }

        if ($validated['tipo_pedido'] === 'domicilio') {
            if (empty($validated['direccion']) || empty($validated['telefono'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'La dirección y el teléfono son obligatorios para pedidos a domicilio.',
                ], 422);
            }
            // Actualizar teléfono del usuario si no lo tiene guardado
            if (empty($user->telefono) && !empty($validated['telefono'])) {
                $user->update(['telefono' => $validated['telefono']]);
            }
        }

        if ($validated['tipo_pedido'] === 'llevar' && empty($validated['nombre_recoge'])) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor indica el nombre de la persona que recogerá el pedido.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $cliente = Cliente::firstOrCreate(['user_id' => $user->id]);

            // Determinar tipo de pedido según la opción elegida
            $nombreTipo = match ($validated['tipo_pedido']) {
                'domicilio' => 'Domicilio',
                'llevar' => 'Para llevar',
                default => 'Mesa',
            };

            $tipoPedido = TipoPedido::where('nombre', 'like', "%{$nombreTipo}%")->first();
            if (!$tipoPedido) {
                // Fallback por ID común
                $idFallback = match ($validated['tipo_pedido']) {
                    'domicilio' => 2,
                    'llevar' => 3,
                    default => 1,
                };
                $tipoPedido = TipoPedido::find($idFallback) ?? TipoPedido::first();
            }

            // Estado inicial: Pendiente
            $estadoInicial = EstadoPedido::where('nombre_estado', 'like', '%pendiente%')->first();
            $estadoInicialId = $estadoInicial ? $estadoInicial->id : 1;

            // Recopilar notas de los platillos individuales
            $notasItems = [];
            foreach ($validated['items'] as $it) {
                if (!empty($it['observacion'])) {
                    $prod = Producto::find($it['id']);
                    $nombreProd = $prod ? $prod->nombre : 'Ítem';
                    $notasItems[] = "{$nombreProd}: {$it['observacion']}";
                }
            }
            $stringNotas = !empty($notasItems) ? ' | Notas: ' . implode('; ', $notasItems) : '';

            // Construir texto de observaciones para cocina/repartidor
            $observacionesFinal = '';
            if ($validated['tipo_pedido'] === 'mesa') {
                $cap = $validated['mesa_capacidad'] ?? 4;
                $observacionesFinal = "Mesa #{$validated['mesa_numero']} (Cap. {$cap}){$stringNotas}";
            } elseif ($validated['tipo_pedido'] === 'domicilio') {
                $dir = $validated['direccion'];
                $barrio = !empty($validated['barrio']) ? " ({$validated['barrio']})" : '';
                $tel = $validated['telefono'];
                $observacionesFinal = "Dirección: {$dir}{$barrio} | Tel: {$tel}{$stringNotas}";
            } else {
                $recoge = $validated['nombre_recoge'] ?? $user->name;
                $hora = !empty($validated['hora_recogida']) ? " a las {$validated['hora_recogida']}" : '';
                $observacionesFinal = "Para llevar - Recoge: {$recoge}{$hora}{$stringNotas}";
            }

            // Crear el Pedido
            $pedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'mesero_id' => null,
                'tipo_pedido_id' => $tipoPedido->id,
                'estado_pedido_id' => $estadoInicialId,
                'total' => 0,
                'observaciones' => $observacionesFinal,
            ]);

            $totalAcumulado = 0;

            // Registrar cada detalle y actualizar inventario
            foreach ($validated['items'] as $item) {
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

                // Descontar inventario
                $inventario = Inventario::where('producto_id', $producto->id)->first();
                if ($inventario) {
                    $stockAnterior = $inventario->cantidad;
                    $inventario->cantidad = max(0, $inventario->cantidad - $cant);
                    $inventario->save();

                    // Registrar movimiento de salida
                    MovimientoInventario::create([
                        'inventario_id' => $inventario->id,
                        'tipo' => 'Salida',
                        'cantidad' => $cant,
                        'motivo' => "Pedido #ORD-" . str_pad($pedido->id, 5, '0', STR_PAD_LEFT) . " ({$nombreTipo})",
                        'user_id' => $user->id,
                    ]);
                }
            }

            $pedido->total = $totalAcumulado;
            $pedido->save();

            // Notificación para el cliente
            Notificacion::create([
                'user_id' => $user->id,
                'tipo' => $validated['tipo_pedido'] === 'domicilio' ? 'domicilio' : 'pedido',
                'titulo' => $validated['tipo_pedido'] === 'domicilio' 
                    ? 'Pedido a domicilio recibido' 
                    : 'Pedido en restaurante confirmado',
                'mensaje' => "Tu pedido #ORD-" . str_pad($pedido->id, 5, '0', STR_PAD_LEFT) . " por $" . number_format($totalAcumulado, 0, ',', '.') . " fue registrado correctamente.",
                'referencia_id' => $pedido->id,
                'leida' => false,
            ]);

            // Notificación para los administradores
            $admins = User::where('role_id', 1)->get();
            foreach ($admins as $admin) {
                Notificacion::create([
                    'user_id' => $admin->id,
                    'tipo' => $validated['tipo_pedido'] === 'domicilio' ? 'domicilio' : 'pedido',
                    'titulo' => $validated['tipo_pedido'] === 'domicilio' 
                        ? 'Nuevo pedido a domicilio' 
                        : 'Nuevo pedido de cliente',
                    'mensaje' => "El cliente {$user->name} realizó un pedido #ORD-" . str_pad($pedido->id, 5, '0', STR_PAD_LEFT) . " ({$nombreTipo}).",
                    'referencia_id' => $pedido->id,
                    'leida' => false,
                ]);
            }

            DB::commit();

            // Determinar la redirección según la opción elegida
            $esDomicilio = ($validated['tipo_pedido'] === 'domicilio');
            $redirectUrl = $esDomicilio ? route('domicilios.cliente') : route('pedidos.cliente');
            $moduloDestino = $esDomicilio ? 'Domicilios' : 'Mis Pedidos';

            $codigoFormateado = '#ORD-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'tipo' => $validated['tipo_pedido'],
                'pedido_id' => $pedido->id,
                'codigo' => $codigoFormateado,
                'total' => '$' . number_format($totalAcumulado, 0, ',', '.'),
                'modulo' => $moduloDestino,
                'message' => "¡Tu pedido {$codigoFormateado} ha sido registrado exitosamente en el módulo de {$moduloDestino}!",
                'redirect_url' => $redirectUrl,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar tu pedido: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Permite al cliente cancelar su pedido si aún se encuentra en estado Pendiente
     */
    public function cancelar(Pedido $pedido)
    {
        $user = Auth::user();
        $cliente = Cliente::where('user_id', $user->id)->first();

        if (!$cliente || $pedido->cliente_id !== $cliente->id) {
            return back()->with('error', 'No tienes autorización para cancelar este pedido.');
        }

        $estadoPendiente = EstadoPedido::where('nombre_estado', 'like', '%pendiente%')->first();
        if ($pedido->estado_pedido_id !== ($estadoPendiente ? $estadoPendiente->id : 1)) {
            return back()->with('error', 'Solo puedes cancelar pedidos que aún se encuentren en estado Pendiente.');
        }

        $estadoCancelado = EstadoPedido::where('nombre_estado', 'like', '%cancelado%')->first();
        if ($estadoCancelado) {
            $pedido->update(['estado_pedido_id' => $estadoCancelado->id]);

            // Reintegrar stock al inventario
            foreach ($pedido->detalles as $detalle) {
                $inv = Inventario::where('producto_id', $detalle->producto_id)->first();
                if ($inv) {
                    $inv->increment('cantidad', $detalle->cantidad);
                    MovimientoInventario::create([
                        'inventario_id' => $inv->id,
                        'tipo' => 'Entrada',
                        'cantidad' => $detalle->cantidad,
                        'motivo' => "Cancelación de pedido #ORD-" . str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
                        'user_id' => $user->id,
                    ]);
                }
            }
        }

        return back()->with('success', 'El pedido #ORD-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT) . ' ha sido cancelado exitosamente.');
    }

    /**
     * Retorna los detalles formateados para el modal de inspección
     */
    public function detalles(Pedido $pedido)
    {
        $user = Auth::user();
        $cliente = Cliente::where('user_id', $user->id)->first();

        // Validar que pertenezca al cliente (o si es admin)
        if ($user->role_id != 1 && (!$cliente || $pedido->cliente_id !== $cliente->id)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $pedido->load([
            'cliente.user',
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
}
