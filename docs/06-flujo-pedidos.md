# Flujo de Pedidos y Domicilios

## Tipos de pedido

| Tipo | Tabla | Controlador Admin | Controlador Cliente | Identificación |
|---|---|---|---|---|
| Mesa | `pedidos` | `Admin\PedidoController` | `ClientePedidoController` | `tipo_pedido.nombre LIKE '%Mesa%'` |
| Para llevar | `pedidos` | `Admin\PedidoController` | `ClientePedidoController` | `tipo_pedido.nombre LIKE '%llevar%'` |
| Domicilio | `pedidos` | `Admin\DomicilioController` | `ClientePedidoController` | `tipo_pedido.nombre LIKE '%Domicilio%'` |

Los tres tipos se almacenan en la misma tabla `pedidos`. La separación entre "pedidos" y "domicilios" en el panel admin es solo visual: cada controlador filtra por `tipo_pedido_id`.

---

## Flujo completo: crear un pedido (cliente)

### 1. Navegación al catálogo
```
GET /catalogo → ProductoController@index
```
El cliente ve los productos disponibles (`estado = true`). Agrega ítems al carrito mediante JavaScript (almacenado en el estado del frontend).

### 2. Selección del tipo de pedido
El cliente elige en el modal del carrito:
- **Mesa** → debe indicar número de mesa y capacidad
- **Para llevar** → debe indicar nombre de quien recoge (y hora opcional)
- **Domicilio** → debe indicar dirección, teléfono y barrio (opcional)

### 3. Envío del pedido
```
POST /cliente/pedidos → ClientePedidoController@store
```

**Payload de ejemplo (Mesa):**
```json
{
  "tipo_pedido": "mesa",
  "mesa_numero": 3,
  "mesa_capacidad": 4,
  "items": [
    { "id": 5, "cantidad": 2, "observacion": "sin cebolla" },
    { "id": 12, "cantidad": 1, "observacion": "" }
  ]
}
```

**Payload de ejemplo (Domicilio):**
```json
{
  "tipo_pedido": "domicilio",
  "direccion": "Calle 10 #23-45",
  "barrio": "Chapinero",
  "telefono": "3001234567",
  "items": [
    { "id": 7, "cantidad": 3, "observacion": "" }
  ]
}
```

### 4. Procesamiento en el servidor

```
DB::beginTransaction()
│
├── Cliente::firstOrCreate(['user_id' => auth()->id()])
├── Resolver TipoPedido por nombre (con fallback a ID)
├── EstadoPedido 'Pendiente' → estado inicial
├── Construir texto de observaciones
├── Pedido::create([..., 'total' => 0])
│
├── Por cada ítem:
│   ├── DetallePedido::create([precio_unitario, subtotal])
│   ├── Inventario::where('producto_id')->decrement cantidad
│   └── MovimientoInventario::create(['tipo' => 'Salida'])
│
├── $pedido->update(['total' => $totalAcumulado])
├── Notificacion::create() → para el cliente
└── Notificacion::create() → para cada admin (role_id = 1)

DB::commit()
```

### 5. Respuesta JSON

```json
{
  "success": true,
  "tipo": "mesa",
  "pedido_id": 42,
  "codigo": "#ORD-00042",
  "total": "$45.000",
  "modulo": "Mis Pedidos",
  "message": "¡Tu pedido #ORD-00042 ha sido registrado exitosamente!",
  "redirect_url": "http://app.test/mis-pedidos"
}
```

Para domicilios, `redirect_url` apunta a `/domicilios` y `modulo` es `"Domicilios"`.

---

## Flujo de estados del pedido

```
[Pendiente]
    │
    ▼  (Admin cambia estado)
[En preparación]
    │
    ▼
[Listo]
    │
    ▼
[Entregado]

En cualquier momento:
    │
    ▼
[Cancelado]  → reintegra stock automáticamente
```

### Cambio de estado (admin)
```
PATCH /admin/pedidos/{id}/estado
PATCH /admin/domicilios/{id}/estado

Body: { "estado_pedido_id": 3 }
```

---

## Texto de observaciones por tipo

El campo `observaciones` del pedido almacena toda la información contextual como texto. El controlador lo construye así:

**Mesa:**
```
Mesa #3 (Cap. 4) | Notas: Burger: sin cebolla; Limonada: con azúcar
```

**Domicilio:**
```
Dirección: Calle 10 #23-45 (Chapinero) | Tel: 3001234567 | Notas: Pizza: extra queso
```

**Para llevar:**
```
Para llevar - Recoge: Juan Pérez a las 13:30 | Notas: Ensalada: sin tomate
```

Al mostrar los detalles en el panel admin, `PedidoController@detalles` parsea este string:

```php
if (str_contains($obs, 'Dirección:')) {
    $direccion = trim(explode('|', explode('Dirección:', $obs)[1])[0]);
} elseif (str_contains($obs, 'Mesa')) {
    $direccion = trim(explode('|', $obs)[0]);
} elseif (str_contains($obs, 'Para llevar')) {
    $direccion = trim(explode('|', $obs)[0]);
}
```

---

## Cancelación de pedido

### Por el cliente (solo estado Pendiente)
```
POST /cliente/pedidos/{pedido}/cancelar → ClientePedidoController@cancelar
```

Validaciones:
1. El pedido debe pertenecer al cliente autenticado
2. El estado debe ser **Pendiente** — no se puede cancelar si ya está en preparación

Acciones:
- Cambia `estado_pedido_id` a Cancelado
- Por cada `DetallePedido`: incrementa `inventario.cantidad` y registra `MovimientoInventario` tipo `Entrada`

### Por el admin
```
DELETE /admin/pedidos/{pedido} → PedidoController@destroy
DELETE /admin/domicilios/{pedido} → DomicilioController@destroy
```

**No elimina el registro.** Acciones:
- Cambia `estado_pedido_id` a Cancelado
- Reintegra stock de todos los ítems
- Registra movimientos de tipo `Entrada` con motivo `"Cancelación administrativa de #ORD-XXXXX"`

---

## Panel de pedidos (admin) — métricas

El `PedidoController@index` calcula estas métricas en cada carga de la vista:

| Métrica | Consulta |
|---|---|
| Pedidos hoy | `whereDate('created_at', today())` |
| En preparación | `whereHas estadoPedido LIKE '%preparaci%'` |
| Listos | `whereHas estadoPedido = 'Listo'` |
| Completados hoy | `whereDate today + estadoPedido = 'Entregado'` |
| Cancelados hoy | `whereDate today + estadoPedido = 'Cancelado'` |

### Filtros disponibles
- **Tab de estado**: Todos / Pendiente / En preparación / Listo / Entregado / Cancelado
- **Filtro por fecha**: `whereDate('created_at', $fecha)`
- **Buscador**: por ID de pedido, nombre del cliente o teléfono

---

## Panel de pedidos (cliente) — mis pedidos

```
GET /mis-pedidos → ClientePedidoController@indexPedidos
GET /domicilios  → ClientePedidoController@indexDomicilios
```

El cliente ve solo sus propios pedidos. Los domicilios y pedidos de mesa/llevar están en módulos separados, aunque ambos consultan la misma tabla `pedidos` con filtros diferentes.

---

## Código de pedido

Los pedidos se identifican en la UI con el formato `#ORD-XXXXX`:

```php
$codigo = '#ORD-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT);
// Ejemplo: #ORD-00042
```

Este código se usa en:
- Mensajes de notificación al cliente y al admin
- Motivos de movimientos de inventario
- Mensajes flash de éxito/error
- Respuestas JSON al frontend

---

## Validación de stock

Al crear un pedido **no se valida si hay stock suficiente** — el inventario se descuenta directamente con `max(0, cantidad_actual - cant_pedida)`. Si el stock es 0, se descuenta a 0 pero el pedido se registra igual. Una mejora futura podría agregar:

```php
if ($inventario->cantidad < $cant) {
    // rechazar o advertir
}
```
