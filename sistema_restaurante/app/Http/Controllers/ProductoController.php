<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'inventario']);

        // Buscador por nombre o descripción
        $search = $request->input('q') ?? $request->input('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria_id') && $request->categoria_id !== 'todas') {
            $query->where('categoria_producto_id', $request->categoria_id);
        }

        // Filtro de solo disponibles
        if ($request->has('disponibles') && $request->boolean('disponibles')) {
            $query->where('estado', true);
        }

        // Filtro por rango de precio
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', (float) $request->precio_min);
        }
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (float) $request->precio_max);
        }

        // Ordenamiento
        $orden = $request->input('orden', 'recientes');
        switch ($orden) {
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'nombre_asc':
                $query->orderBy('nombre', 'asc');
                break;
            case 'nombre_desc':
                $query->orderBy('nombre', 'desc');
                break;
            case 'recientes':
            default:
                $query->latest();
                break;
        }

        $productos = $query->paginate(12)->withQueryString();

        // Categorías activas que tienen al menos un producto en menú
        $categorias = CategoriaProducto::where('estado', true)
            ->withCount(['productos' => function ($q) {
                $q->where('estado', true);
            }])
            ->having('productos_count', '>', 0)
            ->orderBy('nombre')
            ->get();

        // Métricas informativas reales del catálogo
        $totalPlatos = Producto::where('estado', true)->count();
        $totalDisponibles = Producto::where('estado', true)
            ->whereHas('inventario', function ($q) {
                $q->where('cantidad', '>', 0);
            })
            ->count();
        $precioMin = Producto::where('estado', true)->min('precio') ?? 0;
        $precioMax = Producto::where('estado', true)->max('precio') ?? 0;

        // Mesas del restaurante para pedidos en mesa
        $mesas = \App\Models\Mesa::orderBy('numero_mesa')->get();

        if ($request->wantsJson() && !$request->has('view')) {
            return response()->json([
                'productos' => $productos,
                'categorias' => $categorias,
                'totalDisponibles' => $totalDisponibles,
                'mesas' => $mesas,
            ]);
        }

        return view('productos.index', compact(
            'productos',
            'categorias',
            'mesas',
            'totalPlatos',
            'totalDisponibles',
            'precioMin',
            'precioMax',
            'search',
            'orden'
        ));
    }

    public function create()
    {
        $categorias = CategoriaProducto::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'categoria_producto_id' => 'required|exists:categorias_productos,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean',
        ]);

        $datos['estado'] = $request->boolean('estado');

        Producto::create($datos);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Request $request, Producto $producto)
    {
        $producto->load('categoria', 'inventario');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($producto);
        }

        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = CategoriaProducto::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'categoria_producto_id' => 'required|exists:categorias_productos,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean',
        ]);

        $datos['estado'] = $request->boolean('estado');

        $producto->update($datos);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}