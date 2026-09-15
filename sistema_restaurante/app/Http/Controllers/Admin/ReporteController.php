<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\User;
use App\Models\CategoriaProducto;
use App\Models\Reporte;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function index()
    {
        $data = $this->getMetricsAndChartData();

        $reportes = Reporte::latest()->paginate(7)->withQueryString();
        $categorias = CategoriaProducto::where('estado', true)->get();

        return view('admin.reportes.index', array_merge($data, [
            'reportes' => $reportes,
            'categorias' => $categorias,
        ]));
    }

    public function liveData()
    {
        $data = $this->getMetricsAndChartData();
        return response()->json($data);
    }

    public function generar(Request $request)
    {
        $filtro = $request->input('filtro', 'todo');
        $formato = $request->input('formato', 'pantalla');

        $query = Producto::with(['categoria', 'inventario'])->where('estado', true);
        $nombreFiltro = 'Todo el Inventario';

        if ($filtro === 'stock_bajo') {
            $nombreFiltro = 'Productos con Stock Bajo';
            $query->whereHas('inventario', function ($q) {
                $q->where('cantidad', '>', 0)
                  ->whereColumn('cantidad', '<=', 'stock_minimo');
            });
        } elseif ($filtro === 'sin_stock') {
            $nombreFiltro = 'Productos Sin Stock';
            $query->whereHas('inventario', function ($q) {
                $q->where('cantidad', 0);
            });
        } elseif (str_starts_with($filtro, 'cat_')) {
            $catId = str_replace('cat_', '', $filtro);
            $cat = CategoriaProducto::find($catId);
            $nombreFiltro = 'Categoría: ' . ($cat ? $cat->nombre : 'General');
            $query->where('categoria_producto_id', $catId);
        }

        $productos = $query->get();
        $totalProductos = $productos->count();
        $valorTotal = $productos->sum(function ($p) {
            return ($p->inventario->cantidad ?? 0) * $p->precio;
        });

        $itemsJson = $productos->map(function ($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'categoria' => $p->categoria->nombre ?? 'General',
                'precio' => $p->precio,
                'stock' => $p->inventario->cantidad ?? 0,
                'valor_subtotal' => ($p->inventario->cantidad ?? 0) * $p->precio,
            ];
        });

        // Guardar reporte en base de datos
        $reporte = Reporte::create([
            'user_id' => auth()->id(),
            'generado_por' => auth()->user()->name ?? 'Administrador',
            'filtro_usado' => $nombreFiltro,
            'formato' => $formato === 'csv' ? 'CSV / Excel' : 'Ver en Pantalla (Imprimible)',
            'total_productos' => $totalProductos,
            'valor_total' => $valorTotal,
            'datos_json' => $itemsJson,
        ]);

        if ($formato === 'csv') {
            return $this->descargarCsv($reporte);
        }

        return redirect()->route('admin.reportes.index')
            ->with('success', "Reporte #{$reporte->id} ({$nombreFiltro}) generado correctamente.")
            ->with('ultimo_reporte_id', $reporte->id);
    }

    public function ver(Reporte $reporte)
    {
        return response()->json([
            'id' => $reporte->id,
            'fecha' => $reporte->created_at->format('d/m/Y H:i A'),
            'generado_por' => $reporte->generado_por,
            'filtro_usado' => $reporte->filtro_usado,
            'total_productos' => $reporte->total_productos,
            'valor_total' => '$' . number_format($reporte->valor_total, 0, ',', '.'),
            'items' => $reporte->datos_json ?? [],
        ]);
    }

    public function destroy(Reporte $reporte)
    {
        $reporte->delete();

        return redirect()->route('admin.reportes.index')
            ->with('success', 'Reporte eliminado del historial.');
    }

    private function getMetricsAndChartData()
    {
        // 1. Total Productos
        $totalProductos = Producto::where('estado', true)->count();

        // 2. Valor Total Inventario
        $productosConInv = Producto::where('estado', true)->with('inventario')->get();
        $valorTotalInventario = $productosConInv->sum(function ($p) {
            return ($p->inventario->cantidad ?? 0) * $p->precio;
        });

        // 3. Usuarios Registrados
        $usuariosRegistrados = User::count();

        // 4. Valor de Inventario por Categoría (Gráfico Donut 1)
        $categorias = CategoriaProducto::where('estado', true)->with(['productos.inventario'])->get();
        $catLabels = [];
        $catValues = [];

        foreach ($categorias as $cat) {
            $valCat = $cat->productos->sum(function ($p) {
                return ($p->inventario->cantidad ?? 0) * $p->precio;
            });

            if ($valCat > 0 || $cat->productos->count() > 0) {
                $catLabels[] = $cat->nombre;
                $catValues[] = (float)$valCat;
            }
        }

        if (empty($catLabels)) {
            $catLabels = ['General'];
            $catValues = [$valorTotalInventario > 0 ? $valorTotalInventario : 1000];
        }

        // Colores para las categorías
        $coloresDisponibles = [
            '#ef4444', // Rojo
            '#3b82f6', // Azul
            '#10b981', // Verde
            '#f59e0b', // Naranja/Ámbar
            '#8b5cf6', // Púrpura
            '#ec4899', // Rosa
            '#06b6d4', // Cyan
            '#14b8a6', // Teal
        ];
        $catColors = array_slice($coloresDisponibles, 0, count($catLabels));

        // 5. Estado General del Stock (Gráfico Pie 2)
        $enStock = Producto::where('estado', true)->whereHas('inventario', function ($q) {
            $q->whereColumn('cantidad', '>', 'stock_minimo');
        })->count();

        $stockBajo = Producto::where('estado', true)->whereHas('inventario', function ($q) {
            $q->where('cantidad', '>', 0)->whereColumn('cantidad', '<=', 'stock_minimo');
        })->count();

        $sinStock = Producto::where('estado', true)->whereHas('inventario', function ($q) {
            $q->where('cantidad', 0);
        })->count();

        // Fallback visual si no hay inventario configurado aún
        if ($enStock + $stockBajo + $sinStock === 0) {
            $enStock = $totalProductos > 0 ? $totalProductos : 6;
        }

        return [
            'totalProductos' => $totalProductos,
            'valorTotalInventario' => $valorTotalInventario,
            'usuariosRegistrados' => $usuariosRegistrados,
            'catLabels' => $catLabels,
            'catValues' => $catValues,
            'catColors' => $catColors,
            'stockLabels' => ['En Stock', 'Stock Bajo', 'Sin Stock'],
            'stockValues' => [$enStock, $stockBajo, $sinStock],
            'stockColors' => ['#10b981', '#f59e0b', '#ef4444'],
        ];
    }

    private function descargarCsv(Reporte $reporte)
    {
        $filename = "Reporte_RetroRestaurant_{$reporte->id}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $items = $reporte->datos_json ?? [];

        $callback = function () use ($reporte, $items) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($file, ['REPORTE DE INVENTARIO - RETRO RESTAURANT']);
            fputcsv($file, ['ID Reporte', $reporte->id]);
            fputcsv($file, ['Fecha de Generación', $reporte->created_at->format('d/m/Y H:i')]);
            fputcsv($file, ['Generado Por', $reporte->generado_por]);
            fputcsv($file, ['Filtro Usado', $reporte->filtro_usado]);
            fputcsv($file, ['Total Productos', $reporte->total_productos]);
            fputcsv($file, ['Valor Total ($)', $reporte->valor_total]);
            fputcsv($file, []); // Blank line

            fputcsv($file, ['ID', 'Producto', 'Categoría', 'Precio Unitario ($)', 'Stock Actual', 'Valor Subtotal ($)']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item['id'] ?? '',
                    $item['nombre'] ?? '',
                    $item['categoria'] ?? '',
                    $item['precio'] ?? 0,
                    $item['stock'] ?? 0,
                    $item['valor_subtotal'] ?? 0,
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
