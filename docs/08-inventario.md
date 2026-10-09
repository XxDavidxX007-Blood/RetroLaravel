# Módulo de Inventario

## Descripción

El inventario controla el stock de cada producto del menú. Cada producto tiene exactamente un registro en la tabla `inventarios`. Los movimientos de stock (entradas y salidas) se registran automáticamente al procesar pedidos y cancelaciones, y también pueden realizarse de forma manual desde el panel de administración.

---

## Estructura de datos

### Tabla `inventarios`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | BIGINT | PK |
| `producto_id` | BIGINT FK | Uno a uno con `productos` |
| `cantidad` | INTEGER | Stock actual disponible |
| `stock_minimo` | INTEGER | Umbral de alerta (default: 5) |
| `stock_maximo` | INTEGER | Límite máximo sugerido (default: 50) |
| `unidad` | VARCHAR | Unidad de medida (default: "unidades") |

### Tabla `movimientos_inventario`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | BIGINT | PK |
| `inventario_id` | BIGINT FK | Inventario afectado |
| `tipo` | VARCHAR | `Entrada` \| `Salida` |
| `cantidad` | INTEGER | Cantidad del movimiento |
| `motivo` | TEXT | Descripción del motivo |
| `user_id` | BIGINT FK | Usuario que realizó el movimiento |

---

## Rutas del módulo

Todas requieren `middleware('auth')` bajo el prefijo `/admin`.

| Método | URI | Acción |
|---|---|---|
| GET | `/admin/inventario` | Panel principal con listado y métricas |
| POST | `/admin/inventario` | Crear nuevo producto + inventario |
| POST | `/admin/inventario/{producto}/stock` | Ajuste manual de stock |
| GET | `/admin/inventario/{producto}/historial` | Historial JSON de movimientos |
| GET | `/admin/inventario/sugerencias` | Vista de productos con stock bajo |
| PUT | `/admin/inventario/{producto}/editar-minimos` | Editar datos del producto y umbrales |
| DELETE | `/admin/inventario/{producto}` | Eliminar producto y todo su inventario |

---

## Funcionalidades del panel

### Métricas del dashboard de inventario

| Métrica | Descripción |
|---|---|
| Total de productos | Cantidad de productos con inventario |
| Valor total en stock | Suma de `precio * cantidad` por producto |
| Productos con stock bajo | `cantidad <= stock_minimo` |
| Productos sin stock | `cantidad = 0` |
| Operaciones del mes | Movimientos registrados en el mes actual |

### Filtros disponibles

- **Sin stock**: `cantidad = 0`
- **Stock bajo**: `cantidad > 0 AND cantidad <= stock_minimo`
- **Con stock**: `cantidad > stock_minimo`
- **Por categoría**: filtra por `categoria_producto_id`

---

## Creación de producto con inventario

Al crear un producto desde `/admin/inventario`, el sistema crea ambos registros en una sola operación:

```php
// InventarioController@store
$producto = Producto::create([
    'nombre'                 => $request->nombre,
    'descripcion'            => $request->descripcion,
    'precio'                 => $request->precio,
    'categoria_producto_id'  => $request->categoria_producto_id,
    'imagen'                 => $imagenPath,
    'estado'                 => true,
]);

$stockInicial = $request->input('stock_inicial', 0);

Inventario::create([
    'producto_id'  => $producto->id,
    'cantidad'     => $stockInicial,
    'stock_minimo' => $request->input('stock_minimo', 5),
    'stock_maximo' => max(50, $stockInicial * 2),
    'unidad'       => $request->input('unidad', 'unidades'),
]);
```

El `stock_maximo` se calcula automáticamente como `max(50, stock_inicial * 2)`.

Lo mismo ocurre desde **MenuController@store** al agregar un producto al menú — también crea el inventario con valores por defecto.

---

## Ajuste manual de stock

```
POST /admin/inventario/{producto}/stock → InventarioController@actualizarStock
```

```php
// Entrada: incrementa el stock
if ($request->tipo === 'Entrada') {
    $inventario->increment('cantidad', $request->cantidad);
}

// Salida: decrementa el stock (mínimo 0)
if ($request->tipo === 'Salida') {
    $inventario->cantidad = max(0, $inventario->cantidad - $request->cantidad);
    $inventario->save();
}

// Registro del movimiento
MovimientoInventario::create([
    'inventario_id' => $inventario->id,
    'tipo'          => $request->tipo,
    'cantidad'      => $request->cantidad,
    'motivo'        => $request->motivo,
    'user_id'       => auth()->id(),
]);
```

---

## Movimientos automáticos por pedidos

### Al crear un pedido (Salida automática)

```php
// ClientePedidoController@store — por cada ítem del pedido
$inventario = Inventario::where('producto_id', $producto->id)->first();
if ($inventario) {
    $inventario->cantidad = max(0, $inventario->cantidad - $cant);
    $inventario->save();

    MovimientoInventario::create([
        'inventario_id' => $inventario->id,
        'tipo'          => 'Salida',
        'cantidad'      => $cant,
        'motivo'        => "Pedido #ORD-00042 (Mesa)",
        'user_id'       => $user->id,
    ]);
}
```

### Al cancelar un pedido (Entrada automática)

La misma lógica aplica tanto para cancelaciones del cliente como del admin:

```php
foreach ($pedido->detalles as $detalle) {
    $inv = Inventario::where('producto_id', $detalle->producto_id)->first();
    if ($inv) {
        $inv->increment('cantidad', $detalle->cantidad);

        MovimientoInventario::create([
            'inventario_id' => $inv->id,
            'tipo'          => 'Entrada',
            'cantidad'      => $detalle->cantidad,
            'motivo'        => "Cancelación de pedido #ORD-00042",
            'user_id'       => auth()->id(),
        ]);
    }
}
```

---

## Historial de movimientos (AJAX)

```
GET /admin/inventario/{producto}/historial → JSON
```

Retorna los últimos 20 movimientos del inventario del producto:

```json
[
  {
    "tipo": "Salida",
    "cantidad": 2,
    "motivo": "Pedido #ORD-00042 (Mesa)",
    "usuario": "Alicia García",
    "fecha": "09/10/2026 14:35"
  },
  {
    "tipo": "Entrada",
    "cantidad": 10,
    "motivo": "Reabastecimiento manual",
    "usuario": "Sharon Admin",
    "fecha": "08/10/2026 09:00"
  }
]
```

---

## Sugerencias de reabastecimiento

```
GET /admin/inventario/sugerencias → InventarioController@sugerenciasStock
```

Retorna todos los productos donde `cantidad <= stock_minimo`. Esta vista permite identificar rápidamente qué productos necesitan reabastecimiento.

---

## Editar umbrales de stock

```
PUT /admin/inventario/{producto}/editar-minimos → InventarioController@editarMinimos
```

Permite actualizar:
- Datos del producto: `nombre`, `descripcion`, `precio`, `categoria`
- Umbrales del inventario: `stock_minimo`, `stock_maximo`, `unidad`

---

## Eliminación de producto

```
DELETE /admin/inventario/{producto} → InventarioController@destroy
```

La eliminación borra en cascada:
1. `MovimientoInventario` (todos los movimientos del inventario)
2. `Inventario` (el registro de inventario)
3. Referencias en `DetallePedido` (los detalles de pedidos que incluían el producto)
4. Referencias en `DetalleFactura`
5. `Producto` (el producto en sí)

---

## Diagrama de flujo de movimientos

```
Producto creado (MenuController / InventarioController)
         │
         ▼
   Inventario::create()   ← stock_inicial, stock_min, stock_max

         │
         ▼
Cliente hace pedido ──▶ Salida automática ──▶ MovimientoInventario (Salida)
         │
         ▼
Cliente cancela pedido ──▶ Entrada automática ──▶ MovimientoInventario (Entrada)
         │
Admin cancela pedido ───┘

         │
         ▼
Admin ajuste manual ──▶ MovimientoInventario (Entrada | Salida)
```

---

## Comportamiento ante stock insuficiente

Actualmente el sistema **no bloquea pedidos** cuando el stock es insuficiente. El inventario se descuenta hasta 0 con `max(0, cantidad - cant_pedida)`. Si se piden 5 unidades y solo hay 2, el stock queda en 0 y el pedido se registra igual.

Esto es un comportamiento a revisar si se requiere validación estricta de disponibilidad.
