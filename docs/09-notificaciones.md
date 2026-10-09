# Sistema de Notificaciones

## Descripción

Las notificaciones son alertas en base de datos que se crean automáticamente en respuesta a eventos del sistema (nuevo pedido, cancelación, etc.) y se consultan desde el frontend vía AJAX para mostrar el contador de no leídas y el dropdown en el navbar.

No se usa el sistema de notificaciones nativo de Laravel (`Illuminate\Notifications`) — están implementadas con un modelo `Notificacion` propio y un controlador dedicado.

---

## Estructura de datos

### Tabla `notificaciones`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | BIGINT | PK |
| `user_id` | BIGINT FK | Usuario destinatario (cascadeOnDelete) |
| `tipo` | VARCHAR | Categoría de la notificación |
| `titulo` | VARCHAR | Título corto mostrado en el dropdown |
| `mensaje` | TEXT | Texto completo de la notificación |
| `referencia_id` | BIGINT NULLABLE | ID del pedido/reserva relacionado |
| `leida` | BOOLEAN | `false` por defecto |

**Índice:** `(user_id, leida)` — optimiza la consulta de no leídas.

---

## Tipos de notificación

| `tipo` | Cuándo se crea | Destinatario |
|---|---|---|
| `pedido` | Nuevo pedido de mesa o para llevar | Cliente + todos los admins |
| `domicilio` | Nuevo pedido a domicilio | Cliente + todos los admins |
| `reserva` | Nueva reserva creada | Según lógica de ReservaController |
| `inventario` | Ajuste manual de stock | (según implementación) |
| `stock` | Alerta de stock bajo | (según implementación) |

---

## Creación de notificaciones

### Al crear un pedido (`ClientePedidoController@store`)

Se crean dos grupos de notificaciones por cada pedido:

**Para el cliente:**
```php
Notificacion::create([
    'user_id'      => $user->id,
    'tipo'         => $esDomicilio ? 'domicilio' : 'pedido',
    'titulo'       => $esDomicilio
                        ? 'Pedido a domicilio recibido'
                        : 'Pedido en restaurante confirmado',
    'mensaje'      => "Tu pedido #ORD-00042 por $45.000 fue registrado correctamente.",
    'referencia_id' => $pedido->id,
    'leida'        => false,
]);
```

**Para cada administrador (`role_id = 1`):**
```php
$admins = User::where('role_id', 1)->get();
foreach ($admins as $admin) {
    Notificacion::create([
        'user_id'      => $admin->id,
        'tipo'         => $esDomicilio ? 'domicilio' : 'pedido',
        'titulo'       => $esDomicilio
                            ? 'Nuevo pedido a domicilio'
                            : 'Nuevo pedido de cliente',
        'mensaje'      => "El cliente Alicia realizó un pedido #ORD-00042 (Mesa).",
        'referencia_id' => $pedido->id,
        'leida'        => false,
    ]);
}
```

---

## Rutas del módulo

Todas requieren `middleware('auth')`.

| Método | URI | Acción | Respuesta |
|---|---|---|---|
| GET | `/notificaciones` | `index` | HTML |
| GET | `/notificaciones/latest` | `getLatest` | **JSON** |
| POST | `/notificaciones/{id}/leer` | `marcarLeida` | **JSON** |
| POST | `/notificaciones/leer-todas` | `marcarTodasLeidas` | **JSON** |
| DELETE | `/notificaciones/{id}` | `destroy` | **JSON** |

---

## Endpoint de polling (`/notificaciones/latest`)

Este endpoint es consultado periódicamente por el JavaScript del navbar para actualizar el contador de no leídas y el dropdown.

```
GET /notificaciones/latest → NotificacionController@getLatest
```

### Respuesta JSON

```json
{
  "total_no_leidas": 3,
  "notificaciones": [
    {
      "id": 15,
      "tipo": "pedido",
      "titulo": "Nuevo pedido de cliente",
      "mensaje": "El cliente Alicia realizó un pedido #ORD-00042 (Mesa).",
      "referencia_id": 42,
      "leida": false,
      "created_at": "hace 5 minutos"
    },
    ...
  ]
}
```

Retorna las últimas **6 notificaciones** del usuario autenticado, junto con el total de no leídas.

---

## Marcar como leída

```
POST /notificaciones/{id}/leer → NotificacionController@marcarLeida
```

```json
{ "success": true }
```

Solo marca como leída si la notificación pertenece al usuario autenticado.

---

## Marcar todas como leídas

```
POST /notificaciones/leer-todas → NotificacionController@marcarTodasLeidas
```

```php
Notificacion::where('user_id', auth()->id())
            ->where('leida', false)
            ->update(['leida' => true]);
```

---

## Eliminar notificación

```
DELETE /notificaciones/{id} → NotificacionController@destroy
```

Solo permite eliminar notificaciones propias del usuario autenticado.

---

## Vista de notificaciones

```
GET /notificaciones → NotificacionController@index
```

Muestra el historial completo de notificaciones con filtro por leídas/no leídas. El layout se elige según el rol del usuario:

```php
if (auth()->user()->role_id == 1) {
    return view('admin.notificaciones.index', compact('notificaciones'));
}
return view('cliente.notificaciones', compact('notificaciones'));
```

---

## Integración con el navbar

El navbar de ambos layouts (`App.blade.php` y `Admin.blade.php`) incluye un dropdown que:

1. Hace polling a `GET /notificaciones/latest` para actualizar el contador
2. Muestra las últimas 6 notificaciones con título, mensaje y tiempo relativo
3. Permite marcar como leída individualmente haciendo clic
4. Tiene un botón "Marcar todas como leídas"
5. Enlaza a `/notificaciones` para ver el historial completo

---

## Consideraciones de escalabilidad

El sistema usa **polling** (consultas periódicas desde el cliente). Para volúmenes altos de tráfico, una alternativa más eficiente sería usar WebSockets con Laravel Broadcasting + Pusher/Reverb. La estructura actual de la tabla `notificaciones` es compatible con una migración futura a broadcasting.

La columna compuesta `(user_id, leida)` garantiza que la consulta de no leídas use índice en lugar de un full table scan.
