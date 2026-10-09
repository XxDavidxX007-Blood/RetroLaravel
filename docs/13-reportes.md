# Módulo de Reportes

## Descripción

El módulo de reportes permite al administrador generar snapshots del estado del inventario en un momento dado, con distintos filtros. Los reportes se guardan en base de datos y pueden consultarse más tarde o descargarse como archivo CSV compatible con Excel.

---

## Rutas

Todas requieren `middleware('auth')`, prefijo `/admin`.

| Método | URI | Nombre | Respuesta |
|---|---|---|---|
| GET | `/admin/reportes` | `admin.reportes.index` | HTML — panel con métricas y historial |
| GET | `/admin/reportes/live-data` | `admin.reportes.live-data` | **JSON** — métricas actualizadas en vivo |
| POST | `/admin/reportes/generar` | `admin.reportes.generar` | Redirect (pantalla) o **CSV** (descarga) |
| GET | `/admin/reportes/{reporte}/ver` | `admin.reportes.ver` | **JSON** — datos de un reporte guardado |
| DELETE | `/admin/reportes/{reporte}` | `admin.reportes.destroy` | Redirect |

---

## Estructura de datos

### Tabla `reportes`

| Campo | Tipo | Descripción |
|---|---|---|
| `user_id` | BIGINT FK | Quién lo generó |
| `generado_por` | VARCHAR | Nombre del usuario al momento de generarlo |
| `filtro_usado` | VARCHAR | Descripción textual del filtro |
| `formato` | VARCHAR | `"Ver en Pantalla (Imprimible)"` \| `"CSV / Excel"` |
| `total_productos` | INTEGER | Cantidad de productos en el reporte |
| `valor_total` | DECIMAL(10,2) | Valor total del inventario filtrado |
| `datos_json` | JSON | Array con los ítems del reporte (se castea a `array`) |

---

## Generar un reporte

```
POST /admin/reportes/generar → ReporteController@generar
```

### Parámetros

| Parámetro | Valores posibles |
|---|---|
| `filtro` | `todo` \| `stock_bajo` \| `sin_stock` \| `cat_{id}` |
| `formato` | `pantalla` \| `csv` |

### Lógica de filtros

```php
// Todo el inventario (default)
$query = Producto::with(['categoria', 'inventario'])->where('estado', true);

// Solo stock bajo: cantidad > 0 Y cantidad <= stock_minimo
if ($filtro === 'stock_bajo') {
    $query->whereHas('inventario', function ($q) {
        $q->where('cantidad', '>', 0)->whereColumn('cantidad', '<=', 'stock_minimo');
    });
}

// Sin stock: cantidad = 0
elseif ($filtro === 'sin_stock') {
    $query->whereHas('inventario', fn($q) => $q->where('cantidad', 0));
}

// Por categoría: filtro = "cat_3"
elseif (str_starts_with($filtro, 'cat_')) {
    $catId = str_replace('cat_', '', $filtro);
    $query->where('categoria_producto_id', $catId);
}
```

### Datos que se guardan en `datos_json`

```json
[
  {
    "id": 5,
    "nombre": "Hamburguesa Clásica",
    "categoria": "Platos principales",
    "precio": 22000,
    "stock": 15,
    "valor_subtotal": 330000
  },
  ...
]
```

### Flujo después de generar

```
POST /admin/reportes/generar
         │
         ▼
  Consulta con filtro seleccionado
         │
         ▼
  Reporte::create([...datos_json...])
         │
         ├── formato = 'pantalla'
         │       └── redirect con flash "Reporte #X generado"
         │
         └── formato = 'csv'
                 └── StreamedResponse → descarga directa del CSV
```

---

## Exportación CSV

Cuando el formato es `csv`, el sistema genera y descarga el archivo directamente sin guardar nada en disco.

### Nombre del archivo
```
Reporte_RetroRestaurant_{id}_{YYYYMMDD_HHiiss}.csv
```

### Estructura del CSV

```
REPORTE DE INVENTARIO - RETRO RESTAURANT
ID Reporte,42
Fecha de Generación,09/10/2026 14:35
Generado Por,Sharon
Filtro Usado,Todo el Inventario
Total Productos,18
Valor Total ($),4250000

ID,Producto,Categoría,Precio Unitario ($),Stock Actual,Valor Subtotal ($)
5,Hamburguesa Clásica,Platos principales,22000,15,330000
...
```

### Implementación técnica

```php
private function descargarCsv(Reporte $reporte): StreamedResponse
{
    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="Reporte_..."',
    ];

    $callback = function () use ($reporte, $items) {
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF"); // BOM UTF-8 — necesario para que Excel abra bien con tildes
        // ... fputcsv líneas de cabecera y datos
        fclose($file);
    };

    return new StreamedResponse($callback, 200, $headers);
}
```

El BOM `\xEF\xBB\xBF` es importante para que Excel reconozca correctamente la codificación UTF-8 y no corrompa caracteres como tildes y ñ.

---

## Ver un reporte guardado

```
GET /admin/reportes/{reporte}/ver → JSON
```

```json
{
  "id": 42,
  "fecha": "09/10/2026 14:35 PM",
  "generado_por": "Sharon",
  "filtro_usado": "Todo el Inventario",
  "total_productos": 18,
  "valor_total": "$4.250.000",
  "items": [...]
}
```

Este endpoint es consumido por el modal de la vista para mostrar los detalles de un reporte histórico sin recargar la página.

---

## Métricas del dashboard de reportes

Calculadas por `getMetricsAndChartData()` — método privado usado tanto en `index()` como en `liveData()` (JSON para polling).

| Métrica | Descripción |
|---|---|
| Total productos activos | `Producto::where('estado', true)->count()` |
| Valor total en inventario | Suma de `precio × cantidad` por cada producto activo |
| Usuarios registrados | `User::count()` |

### Gráfico 1 — Valor por categoría (Donut)

Para cada categoría activa: suma el valor (`precio × cantidad`) de todos sus productos. Se excluyen categorías con valor 0 y sin productos.

```php
// Colores asignados en orden:
['#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#14b8a6']
```

### Gráfico 2 — Estado del stock (Pie)

| Segmento | Color | Condición |
|---|---|---|
| En Stock | `#10b981` (verde) | `cantidad > stock_minimo` |
| Stock Bajo | `#f59e0b` (ámbar) | `cantidad > 0 AND cantidad <= stock_minimo` |
| Sin Stock | `#ef4444` (rojo) | `cantidad = 0` |

Si no hay inventario configurado, se usa el total de productos activos como fallback visual para no mostrar el gráfico vacío.

---

## Endpoint de datos en vivo

```
GET /admin/reportes/live-data → JSON
```

Devuelve exactamente los mismos datos que `getMetricsAndChartData()`. El frontend puede consultarlo periódicamente para actualizar las métricas sin recargar la página.

---

## Historial de reportes

Los reportes generados se listan en el panel con paginación de 7 por página. Cada fila muestra:
- ID del reporte
- Fecha de generación
- Generado por
- Filtro usado
- Total de productos
- Valor total
- Formato (pantalla o CSV)
- Botones: Ver detalles (modal) y Eliminar
