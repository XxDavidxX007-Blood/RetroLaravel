<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\CategoriaProducto;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'inventario']);

        // Buscador por nombre o descripción
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_producto_id', $request->input('categoria_id'));
        }

        // Filtro por visibilidad / estado
        if ($request->filled('visibilidad')) {
            $vis = $request->input('visibilidad');
            if ($vis === 'visible') {
                $query->where('estado', true);
            } elseif ($vis === 'oculto') {
                $query->where('estado', false);
            }
        }

        $productos = $query->latest()->paginate(7)->withQueryString();
        $categorias = CategoriaProducto::where('estado', true)->get();

        // ===== MÉTRICAS =====
        $totalPlatos = Producto::count();
        $platosOcultos = Producto::where('estado', false)->count();
        $precioPromedio = Producto::avg('precio') ?? 0;

        return view('admin.menu.index', compact(
            'productos',
            'categorias',
            'totalPlatos',
            'platosOcultos',
            'precioPromedio'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'categoria_producto_id' => 'required|exists:categorias_productos,id',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
            'imagen' => 'nullable|image|max:2048',
            'imagen_url' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
            'stock_inicial' => 'nullable|integer|min:0',
        ]);

        $validated['estado'] = $request->has('estado') ? (bool)$request->estado : true;

        // Manejo de imagen subida o URL
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen'] = '/storage/' . $path;
        } elseif (!empty($validated['imagen_url'])) {
            $validated['imagen'] = $validated['imagen_url'];
        }

        unset($validated['imagen_url'], $validated['stock_inicial']);

        $producto = Producto::create($validated);

        // Crear inventario asociado
        $stock = $request->input('stock_inicial', 20);
        Inventario::create([
            'producto_id' => $producto->id,
            'cantidad' => $stock,
            'stock_minimo' => 5,
            'stock_maximo' => max(50, $stock * 2),
        ]);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Plato agregado al menú correctamente.');
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'categoria_producto_id' => 'required|exists:categorias_productos,id',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
            'imagen' => 'nullable|image|max:2048',
            'imagen_url' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->has('estado') ? (bool)$request->estado : true;

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen'] = '/storage/' . $path;
        } elseif (!empty($validated['imagen_url'])) {
            $validated['imagen'] = $validated['imagen_url'];
        }

        unset($validated['imagen_url']);

        $producto->update($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Plato actualizado correctamente.');
    }

    public function toggleStatus(Producto $producto)
    {
        $producto->estado = !$producto->estado;
        $producto->save();

        $estadoTexto = $producto->estado ? 'Visible' : 'Oculto';

        return redirect()->route('admin.menu.index')
            ->with('success', "Disponibilidad del plato cambiada a {$estadoTexto}.");
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', 'Plato eliminado del menú correctamente.');
    }
}
