# Gestión del Menú y Catálogo de Productos

## Descripción

El módulo de menú permite al administrador gestionar el catálogo completo de productos que se muestran a los clientes. Cada producto tiene una categoría, precio, imagen y estado de visibilidad. Al crear un producto se genera automáticamente su registro de inventario.

---

## Actores y rutas

### Panel Admin (`Admin\MenuController`)

| Método | URI | Nombre | Acción |
|---|---|---|---|
| GET | `/admin/menu` | `admin.menu.index` | Listado con filtros y métricas |
| POST | `/admin/menu` | `admin.menu.store` | Crear producto |
| PUT | `/admin/menu/{producto}` | `admin.menu.update` | Actualizar producto |
| PATCH | `/admin/menu/{producto}/estado` | `admin.menu.toggle-status` | Ocultar / Mostrar |
| DELETE | `/admin/menu/{producto}` | `admin.menu.destroy` | Eliminar producto |

### Catálogo cliente (`ProductoController`)

| Método | URI | Nombre | Acción |
|---|---|---|---|
| GET | `/catalogo` | `cliente.catalogo` | Ver productos activos con carrito |

---

## Estructura de datos

### Tabla `productos`

| Campo | Tipo | Notas |
|---|---|---|
| `categoria_producto_id` | BIGINT FK | Categoría del producto |
| `nombre` | VARCHAR 150 | Nombre del plato |
| `descripcion` | TEXT NULLABLE | Descripción breve |
| `precio` | DECIMAL(10,2) | Precio de venta |
| `imagen` | VARCHAR NULLABLE | URL externa o ruta en `/storage/productos/` |
| `estado` | BOOLEAN | `true` = visible en catálogo, `false` = oculto |

### Tabla `categorias_productos`

| Campo | Tipo | Notas |
|---|---|---|
| `nombre` | VARCHAR | Nombre de la categoría |
| `descripcion` | TEXT NULLABLE | |
| `estado` | BOOLEAN | Activa / Inactiva |

---

## Crear un producto

```
POST /admin/menu → MenuController@store
```

### Validación

```php
'nombre'                => 'required|string|max:150',
'categoria_producto_id' => 'required|exists:categorias_productos,id',
'precio'                => 'required|numeric|min:0',
'descripcion'           => 'nullable|string|max:500',
'imagen'                => 'nullable|image|max:2048',   // archivo subido (máx 2MB)
'imagen_url'            => 'nullable|string|max:500',   // URL externa alternativa
'estado'                => 'nullable|boolean',
'stock_inicial'         => 'nullable|integer|min:0',
```

### Lógica de imagen

```php
// Prioridad 1: archivo subido
if ($request->hasFile('imagen')) {
    $path = $request->file('imagen')->store('productos', 'public');
    $validated['imagen'] = '/storage/' . $path;
}
// Prioridad 2: URL externa
elseif (!empty($validated['imagen_url'])) {
    $validated['imagen'] = $validated['imagen_url'];
}
// Sin imagen: el campo queda null
```

Las imágenes subidas se almacenan en `storage/app/public/productos/` y son accesibles desde `http://app.test/storage/productos/nombre.jpg`.

### Creación automática del inventario

```php
$stock = $request->input('stock_inicial', 20); // default: 20 unidades

Inventario::create([
    'producto_id' => $producto->id,
    'cantidad'    => $stock,
    'stock_minimo' => 5,
    'stock_maximo' => max(50, $stock * 2),
]);
```

Si no se especifica stock inicial, se usa **20 unidades** por defecto. El stock máximo es `max(50, stock_inicial * 2)`.

---

## Actualizar un producto

```
PUT /admin/menu/{producto} → MenuController@update
```

Mismas validaciones que en `store`, excepto `stock_inicial` (no se actualiza el inventario desde este endpoint — para eso está `/admin/inventario`).

La imagen se actualiza solo si se sube una nueva o se proporciona una URL nueva. Si no se envía ninguna, la imagen anterior se conserva.

---

## Cambiar visibilidad (toggle)

```
PATCH /admin/menu/{producto}/estado → MenuController@toggleStatus
```

```php
$producto->estado = !$producto->estado;
$producto->save();
// Resultado: "Disponibilidad del plato cambiada a Visible / Oculto"
```

Un producto con `estado = false` no aparece en el catálogo del cliente pero sigue existiendo en el sistema y en los pedidos históricos.

---

## Eliminar un producto

```
DELETE /admin/menu/{producto} → MenuController@destroy
```

Elimina el registro del producto. Si se elimina desde `/admin/inventario`, la eliminación es en cascada (borra también el inventario, movimientos, y referencias en detalles de pedidos).

---

## Métricas del panel de menú

| Métrica | Cálculo |
|---|---|
| Total platos | `Producto::count()` |
| Platos ocultos | `Producto::where('estado', false)->count()` |
| Precio promedio | `Producto::avg('precio')` |

---

## Filtros del panel de menú

- **Búsqueda**: por `nombre` o `descripcion` (`LIKE %texto%`)
- **Categoría**: filtra por `categoria_producto_id`
- **Visibilidad**: `visible` (`estado = true`) / `oculto` (`estado = false`) / todos

---

## Catálogo del cliente

```
GET /catalogo → ProductoController@index
```

Muestra solo los productos con `estado = true`. El cliente puede:

1. Ver nombre, descripción, precio e imagen de cada plato
2. Agregar productos al carrito (JavaScript del lado cliente)
3. Especificar cantidad y observación por ítem
4. Abrir el modal de checkout para elegir tipo de pedido

El carrito no persiste en base de datos — se mantiene en el estado del frontend (memoria del navegador) hasta que se confirma el pedido.

---

## Relación con inventario

Cada producto tiene exactamente **un** registro en `inventarios`. El módulo de menú y el módulo de inventario son independientes pero comparten el modelo `Producto`:

```
MenuController   → crea Producto + crea Inventario (stock_inicial)
InventarioController → gestiona el Inventario (ajustes de stock, mínimos, historial)
```

No es posible tener un producto sin inventario ni un inventario sin producto.
