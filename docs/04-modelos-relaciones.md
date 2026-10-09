# Modelos y Relaciones Eloquent

## Mapa de relaciones

```
Role
 └── hasMany ──▶ User
                  ├── hasOne ──▶ Cliente
                  │               └── hasMany ──▶ Pedido
                  │               └── hasMany ──▶ Reserva
                  ├── hasOne ──▶ Mesero
                  │               └── hasMany ──▶ Pedido (nullable)
                  └── hasMany ──▶ Notificacion

Pedido
 ├── belongsTo ──▶ Cliente
 ├── belongsTo ──▶ Mesero (nullable)
 ├── belongsTo ──▶ TipoPedido
 ├── belongsTo ──▶ EstadoPedido
 ├── hasMany   ──▶ DetallePedido
 │                  └── belongsTo ──▶ Producto
 └── hasOne    ──▶ Factura
                    └── hasMany ──▶ DetalleFactura
                                    └── belongsTo ──▶ Producto

Producto
 ├── belongsTo ──▶ CategoriaProducto
 └── hasOne    ──▶ Inventario
                    └── hasMany ──▶ MovimientoInventario

Reserva
 ├── belongsTo ──▶ Cliente
 ├── belongsTo ──▶ Mesa
 │                  └── belongsTo ──▶ EstadoMesa
 └── belongsTo ──▶ EstadoReserva
```

---

## Modelos detallados

### `User`
**Tabla:** `users`

```php
protected $fillable = [
    'name', 'apellidos', 'email', 'telefono',
    'password', 'foto', 'role_id', 'estado'
];

protected $hidden = ['password', 'remember_token'];

protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];
```

**Relaciones:**
```php
belongsTo(Role::class)
hasOne(Mesero::class)
hasOne(Cliente::class)
hasMany(Notificacion::class)
```

---

### `Role`
**Tabla:** `roles`

```php
protected $fillable = ['nombre', 'descripcion'];
```

**Relaciones:**
```php
hasMany(User::class)
```

**Valores semilla:**

| id | nombre |
|---|---|
| 1 | Administrador |
| 2 | Empleado |
| 3 | Cliente |

---

### `Cliente`
**Tabla:** `clientes`

```php
protected $fillable = ['user_id'];
```

**Relaciones:**
```php
belongsTo(User::class)
hasMany(Pedido::class)
hasMany(Reserva::class)
```

Se crea automáticamente en dos momentos:
1. Al registrarse un usuario (`RegisterController`)
2. Al crear un pedido por primera vez (`Cliente::firstOrCreate(['user_id' => $user->id])`)

---

### `Mesero`
**Tabla:** `meseros`

```php
protected $fillable = ['user_id'];
```

**Relaciones:**
```php
belongsTo(User::class)
hasMany(Pedido::class)
```

Se crea cuando un admin crea un usuario con `role_id = 2`.

---

### `Mesa`
**Tabla:** `mesas`

```php
protected $fillable = [
    'numero_mesa', 'capacidad', 'ubicacion', 'estado_mesa_id'
];
```

**Relaciones:**
```php
belongsTo(EstadoMesa::class)
hasMany(Reserva::class)
```

**Estados posibles de la mesa:**

| Estado | Cuándo se asigna |
|---|---|
| Disponible | Estado inicial / reserva cancelada o completada |
| Ocupada | (Manual) |
| Reservada | Al confirmar o crear una reserva activa |
| Mantenimiento | (Manual) |

---

### `EstadoMesa` / `EstadoReserva` / `EstadoPedido` / `TipoPedido`
Modelos de catálogo simples con solo `nombre_estado` (o `nombre`). Solo tienen `hasMany` hacia sus entidades relacionadas.

**Estados de pedido semilla:**

| id | nombre_estado |
|---|---|
| 1 | Pendiente |
| 2 | En preparación |
| 3 | Listo |
| 4 | Entregado |
| 5 | Cancelado |

**Tipos de pedido semilla:**

| id | nombre |
|---|---|
| 1 | Mesa |
| 2 | Domicilio |
| 3 | Para llevar |

---

### `Producto`
**Tabla:** `productos`

```php
protected $fillable = [
    'categoria_producto_id', 'nombre', 'descripcion',
    'precio', 'imagen', 'estado'
];

protected $casts = ['precio' => 'decimal:2', 'estado' => 'boolean'];
```

**Relaciones:**
```php
belongsTo(CategoriaProducto::class)
hasOne(Inventario::class)
```

El campo `imagen` puede ser:
- Una URL externa (https://...)
- Una ruta relativa en `storage/` para imágenes subidas

---

### `CategoriaProducto`
**Tabla:** `categorias_productos`

```php
protected $fillable = ['nombre', 'descripcion', 'estado'];
```

**Relaciones:**
```php
hasMany(Producto::class)
```

---

### `Inventario`
**Tabla:** `inventarios`

```php
protected $fillable = [
    'producto_id', 'cantidad', 'stock_minimo', 'stock_maximo', 'unidad'
];
```

**Relaciones:**
```php
belongsTo(Producto::class)
hasMany(MovimientoInventario::class)
```

Regla de negocio: `cantidad <= stock_minimo` → el producto aparece en la vista de sugerencias de reabastecimiento.

---

### `MovimientoInventario`
**Tabla:** `movimientos_inventario`

```php
protected $fillable = [
    'inventario_id', 'tipo', 'cantidad', 'motivo', 'user_id'
];
```

**Relaciones:**
```php
belongsTo(Inventario::class)
belongsTo(User::class)
```

**Tipos de movimiento:**

| tipo | Cuándo se registra |
|---|---|
| `Salida` | Al crear un pedido (por cada ítem) |
| `Entrada` | Al cancelar un pedido (admin o cliente) / ajuste manual |

---

### `Pedido`
**Tabla:** `pedidos`

```php
protected $fillable = [
    'cliente_id', 'mesero_id', 'tipo_pedido_id',
    'estado_pedido_id', 'total', 'observaciones'
];

protected $casts = ['total' => 'decimal:2'];
```

**Relaciones:**
```php
belongsTo(Cliente::class)
belongsTo(Mesero::class)        // nullable
belongsTo(TipoPedido::class)
belongsTo(EstadoPedido::class)
hasMany(DetallePedido::class)
hasOne(Factura::class)
```

---

### `DetallePedido`
**Tabla:** `detalles_pedido`

```php
protected $fillable = [
    'pedido_id', 'producto_id', 'cantidad',
    'precio_unitario', 'subtotal'
];
```

**Relaciones:**
```php
belongsTo(Pedido::class)
belongsTo(Producto::class)
```

---

### `Factura`
**Tabla:** `facturas`

```php
protected $fillable = [
    'pedido_id', 'fecha_factura', 'subtotal', 'impuestos',
    'descuento', 'total', 'metodo_pago', 'estado'
];
```

**Relaciones:**
```php
belongsTo(Pedido::class)
hasMany(DetalleFactura::class)
```

---

### `DetalleFactura`
**Tabla:** `detalles_factura`

```php
protected $fillable = [
    'factura_id', 'producto_id', 'cantidad',
    'precio_unitario', 'subtotal'
];
```

**Relaciones:**
```php
belongsTo(Factura::class)
belongsTo(Producto::class)
```

---

### `Reserva`
**Tabla:** `reservas`

```php
protected $fillable = [
    'cliente_id', 'mesa_id', 'fecha_reserva', 'hora_reserva',
    'cantidad_personas', 'observaciones', 'estado_reserva_id'
];
```

**Relaciones:**
```php
belongsTo(Cliente::class)
belongsTo(Mesa::class)
belongsTo(EstadoReserva::class)
```

---

### `Notificacion`
**Tabla:** `notificaciones`

```php
protected $fillable = [
    'user_id', 'tipo', 'titulo', 'mensaje', 'referencia_id', 'leida'
];

protected $casts = ['leida' => 'boolean'];
```

**Relaciones:**
```php
belongsTo(User::class)
```

**Tipos de notificación:**

| tipo | Cuándo se crea |
|---|---|
| `pedido` | Nuevo pedido de mesa o para llevar |
| `domicilio` | Nuevo pedido a domicilio |
| `reserva` | Nueva reserva (según lógica de ReservaController) |
| `inventario` | Ajuste manual de stock |
| `stock` | Alerta de stock bajo |

---

### `Reporte`
**Tabla:** `reportes`

```php
protected $fillable = [
    'user_id', 'generado_por', 'filtro_usado', 'formato',
    'total_productos', 'valor_total', 'datos_json'
];

protected $casts = ['datos_json' => 'array'];
```

**Relaciones:**
```php
belongsTo(User::class)
```

---

### `Promocion`
**Tabla:** `promociones`

```php
protected $fillable = [
    'titulo', 'descripcion', 'descuento',
    'fecha_inicio', 'fecha_fin', 'activa'
];
```

Sin relaciones definidas. Sin controlador ni rutas implementadas.

---

## Eager loading recomendado

Los controladores cargan relaciones con `with()` para evitar el problema N+1:

```php
// En PedidoController / DomicilioController
Pedido::with([
    'cliente.user',
    'mesero.user',
    'tipoPedido',
    'estadoPedido',
    'detalles.producto'
])

// En ReservaController
Reserva::with([
    'cliente.user',
    'mesa',
    'estadoReserva'
])
```
