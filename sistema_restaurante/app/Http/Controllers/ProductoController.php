<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('productos.index', compact('productos'));
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

    public function show(Producto $producto)
    {
        $producto->load('categoria', 'inventario');

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