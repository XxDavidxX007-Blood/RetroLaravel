<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['inventario', 'categoria']);

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhereHas('categoria', function ($c) use ($search) {
                      $c->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por estado de stock
        if ($request->filled('filtro_stock')) {
            $filtro = $request->input('filtro_stock');
            $query->whereHas('inventario', function ($q) use ($filtro) {
                if ($filtro === 'sin_stock') {
                    $q->where('cantidad', 0);
                } elseif ($filtro === 'stock_bajo') {
                    $q->where('cantidad', '>', 0)
                      ->whereColumn('cantidad', '<=', 'stock_minimo');
                } elseif ($filtro === 'con_stock') {
                    $q->whereColumn('cantidad', '>', 'stock_minimo');
                }
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_producto_id', $request->input('categoria_id'));
        }

        $productos = $query->where('estado', true)->latest()->paginate(7)->withQueryString();

        $categorias = CategoriaProducto::where('estado', true)->get();

        // ===== MÉTRICAS =====
        $totalProductos = Producto::where('estado', true)->count();

        $valorStock = Producto::where('estado', true)
            ->whereHas('inventario')
            ->with('inventario')
            ->get()
            ->sum(fn($p) => ($p->inventario->cantidad ?? 0) * $p->precio);

        $stockBajo = Producto::where('estado', true)
            ->whereHas('inventario', function ($q) {
                $q->where('cantidad', '>', 0)
                  ->whereColumn('cantidad', '<=', 'stock_minimo');
            })->count();

        $sinStock = Producto::where('estado', true)
            ->whereHas('inventario', function ($q) {
                $q->where('cantidad', 0);
            })->count();

        $operacionesMes = MovimientoInventario::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.inventario.index', compact(
            'productos',
            'categorias',
            'totalProductos',
            'valorStock',
            'stockBajo',
            'sinStock',
            'operacionesMes'
        ));
    }

    public function actualizarStock(Request $request, Producto $producto)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:255',
        ]);

        $inventario = $producto->inventario;

        if (!$inventario) {
            return back()->with('error', 'El producto no tiene registro de inventario.');
        }

        $nuevaCantidad = $request->tipo === 'entrada'
            ? $inventario->cantidad + $request->cantidad
            : $inventario->cantidad - $request->cantidad;

        if ($nuevaCantidad < 0) {
            return back()->with('error', 'No hay suficiente stock para esta salida.');
        }

        $inventario->update(['cantidad' => $nuevaCantidad]);

        MovimientoInventario::create([
            'inventario_id' => $inventario->id,
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'motivo' => $request->motivo,
            'user_id' => auth()->id(),
        ]);

        $tipoTexto = $request->tipo === 'entrada' ? 'entrada' : 'salida';
        return back()->with('success', "Se registró la {$tipoTexto} de {$request->cantidad} unidades de {$producto->nombre}.");
    }

    public function sugerenciasStock()
    {
        $sugerencias = Producto::where('estado', true)
            ->whereHas('inventario', function ($q) {
                $q->where('cantidad', '<=', DB::raw('stock_minimo'));
            })
            ->with(['inventario', 'categoria'])
            ->get();

        return view('admin.inventario.sugerencias', compact('sugerencias'));
    }

    public function historial(Producto $producto){
    $movimientos = MovimientoInventario::whereHas('inventario', function ($q) use ($producto) {
        $q->where('producto_id', $producto->id);
    })->latest()->take(20)->get()->map(function ($m) {
        return [
            'tipo' => $m->tipo,
            'cantidad' => $m->cantidad,
            'motivo' => $m->motivo,
            'fecha' => $m->created_at->format('d/m/Y H:i'),
        ];
    });

    return response()->json($movimientos);
}

    public function editarMinimos(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'           => 'required|string|max:255',
            'categoria_id'     => 'required|exists:categoria_productos,id',
            'precio'           => 'required|numeric|min:0',
            'unidad'           => 'nullable|string|max:50',
            'cantidad'         => 'required|integer|min:0',
            'stock_minimo'     => 'required|integer|min:0',
        ]);

        // Actualizar datos del producto
        $producto->update([
            'nombre'                 => $request->nombre,
            'categoria_producto_id'  => $request->categoria_id,
            'precio'                 => $request->precio,
        ]);

        $inventario = $producto->inventario;

        if (!$inventario) {
            return back()->with('error', 'El producto no tiene registro de inventario.');
        }

        $inventario->update([
            'cantidad'     => $request->cantidad,
            'stock_minimo' => $request->stock_minimo,
            'unidad'       => $request->filled('unidad') ? $request->unidad : $inventario->unidad,
        ]);

        return back()->with('success', "Se actualizaron los datos de {$producto->nombre}.");
    }
}