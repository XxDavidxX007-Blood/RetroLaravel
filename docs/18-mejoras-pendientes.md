# Deuda Técnica y Mejoras Recomendadas

Este documento recoge las limitaciones conocidas del sistema actual, organizadas por prioridad, junto con la forma recomendada de resolverlas.

---

## 🔴 Alta prioridad — Seguridad

### 1. Ausencia de middleware de roles en rutas admin

**Problema:** Las rutas `/admin/*` solo requieren `middleware('auth')`. Cualquier usuario autenticado (cliente, empleado) que conozca la URL puede acceder al panel de administración.

**Solución recomendada:**

```php
// Opción A: Gate en AppServiceProvider
Gate::define('admin', fn ($user) => $user->role_id === 1);

// Aplicar en rutas
Route::middleware(['auth', 'can:admin'])->prefix('admin')->group(function () { ... });
```

```php
// Opción B: Middleware personalizado
// app/Http/Middleware/RoleMiddleware.php
public function handle(Request $request, Closure $next, string $role)
{
    $roles = ['admin' => 1, 'empleado' => 2, 'cliente' => 3];
    if (auth()->user()->role_id !== $roles[$role]) {
        abort(403);
    }
    return $next($request);
}

// Registro en bootstrap/app.php
$middleware->alias(['role' => RoleMiddleware::class]);

// Uso en rutas
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () { ... });
```

---

### 2. Sin validación de stock al crear pedidos

**Problema:** Si un producto tiene stock 0, el sistema igual permite el pedido. El inventario se descuenta a 0 con `max(0, cantidad - pedida)` pero no bloquea la operación.

**Solución:**

```php
// ClientePedidoController@store — dentro del bucle de ítems
$inventario = Inventario::where('producto_id', $producto->id)->first();
if (!$inventario || $inventario->cantidad < $cant) {
    DB::rollBack();
    return response()->json([
        'success' => false,
        'message' => "Stock insuficiente para \"{$producto->nombre}\". Disponible: {$inventario->cantidad ?? 0}.",
    ], 422);
}
```

---

## 🟠 Media prioridad — Fragilidad del código

### 3. `tipo_pedido_id = 2` asumido como domicilio

**Problema:** `DashboardController` usa `where('tipo_pedido_id', 2)` asumiendo que el ID 2 siempre es Domicilio. Si el seeder cambia el orden, la lógica se rompe silenciosamente.

**Solución:**

```php
// Resolver el ID dinámicamente una sola vez
$idDomicilio = TipoPedido::where('nombre', 'like', '%Domicilio%')->value('id');

$domiciliosHoy = Pedido::whereDate('created_at', $hoy)
    ->where('tipo_pedido_id', $idDomicilio)
    ->count();
```

O cachear el ID en el arranque de la aplicación en `AppServiceProvider`.

---

### 4. Campo `observaciones` como texto libre estructurado

**Problema:** El campo `observaciones` del pedido almacena información de entrega concatenada como string (`"Mesa #3 (Cap. 4) | Notas: ..."`, `"Dirección: X | Tel: Y | ..."`). El controlador parsea este string con `str_contains` para extraer los campos, lo que es frágil ante cualquier cambio de formato.

**Solución:** Agregar columnas específicas o una tabla `detalle_entrega` con campos separados:

```php
// En la tabla pedidos
$table->string('mesa_numero')->nullable();
$table->string('direccion_entrega')->nullable();
$table->string('telefono_entrega')->nullable();
$table->string('nombre_recoge')->nullable();
$table->string('hora_recogida')->nullable();
// o bien una relación hasOne hacia DetalleEntrega
```

---

### 5. Lógica de sincronización de reservas duplicada

**Problema:** La lógica de sincronizar el estado de la mesa existe en dos lugares: `Admin\ReservaController` (con helpers privados bien estructurados) y `ClienteController` (con lógica inline duplicada). Si se agrega un nuevo estado de reserva, hay que actualizar ambos.

**Solución:** Extraer la lógica a un servicio:

```php
// app/Services/ReservaService.php
class ReservaService
{
    public function sincronizarMesa(int $mesaId, string $nombreEstado): void { ... }
    public function liberarMesaAnterior(int $anterior, int $nueva): void { ... }
}
```

E inyectarlo en ambos controladores.

---

## 🟡 Media prioridad — Funcionalidad incompleta

### 6. Módulo de Promociones sin implementar

**Estado actual:** Modelo `Promocion` y migración `promociones` listos. Sin controlador, rutas ni vistas.

**Para implementar:**
- `Admin\PromocionController` con CRUD
- Rutas en `/admin/promociones`
- Aplicar descuentos en `ClientePedidoController@store` al calcular el total
- Vista para que el cliente vea las promociones vigentes

```php
// Lógica de descuento al crear pedido
$promocion = Promocion::where('activa', true)
    ->where('fecha_inicio', '<=', now())
    ->where('fecha_fin', '>=', now())
    ->first();

if ($promocion) {
    $totalAcumulado *= (1 - $promocion->descuento / 100);
}
```

---

### 7. Flujo de facturación sin implementar

**Estado actual:** Tablas `facturas` y `detalles_factura` existen. El modelo `Factura` tiene sus relaciones. `UserController` consulta `facturas` para calcular "ventas hoy", pero no hay flujo para crearlas.

**Para implementar:**
- `Admin\FacturaController` que genere una factura a partir de un pedido entregado
- Vista de factura imprimible
- Opcionalmente: exportación a PDF (usando `barryvdh/laravel-dompdf`)

---

### 8. `MeseroController` sin funcionalidad expuesta

**Estado actual:** El archivo `MeseroController.php` existe pero no tiene rutas definidas en `web.php`.

**Posible uso:** Panel donde los empleados puedan ver y gestionar los pedidos asignados a su turno, similar al panel admin de pedidos pero con alcance limitado a sus propios pedidos.

---

## 🟢 Baja prioridad — Mejoras de arquitectura

### 9. Sin capa de servicios

Toda la lógica de negocio vive en los controladores. `ClientePedidoController@store` supera las 150 líneas. Para proyectos más grandes, extraer la lógica a servicios mejora la testeabilidad y la reutilización:

```
app/
  Services/
    PedidoService.php       ← createPedido(), cancelarPedido()
    InventarioService.php   ← descontarStock(), reintegrarStock()
    NotificacionService.php ← notificarPedido(), notificarAdmin()
    ReservaService.php      ← sincronizarMesa(), liberarMesa()
```

---

### 10. Sin tests automatizados

No hay tests en el directorio `tests/`. Para un sistema de producción se recomienda al menos:

```bash
# Tests de feature para los flujos críticos
php artisan make:test PedidoTest
php artisan make:test InventarioTest
php artisan make:test AuthTest
```

Casos clave a cubrir:
- Crear pedido descuenta el inventario correctamente
- Cancelar pedido reintegra el stock
- Login con usuario inactivo es rechazado
- Cliente no puede acceder a rutas admin

---

### 11. Polling en lugar de WebSockets para notificaciones

El frontend consulta `/notificaciones/latest` periódicamente (polling). Para notificaciones en tiempo real se recomienda migrar a Laravel Reverb (WebSockets nativos de Laravel):

```bash
composer require laravel/reverb
php artisan reverb:install
```

La tabla `notificaciones` existente es compatible con una transición a `broadcasting`.

---

### 12. Sin API REST

No existe `routes/api.php`. Si en el futuro se quiere construir una app móvil o un frontend desacoplado (Vue, React), habría que:

1. Crear `routes/api.php`
2. Instalar Laravel Sanctum para autenticación con tokens
3. Convertir los controladores web a API Resources

---

## Resumen de prioridades

| # | Mejora | Prioridad | Esfuerzo |
|---|---|---|---|
| 1 | Middleware de roles en rutas admin | 🔴 Alta | Bajo |
| 2 | Validación de stock al pedir | 🔴 Alta | Bajo |
| 3 | ID domicilio hardcodeado | 🟠 Media | Bajo |
| 4 | Campo observaciones como texto libre | 🟠 Media | Alto |
| 5 | Lógica de reservas duplicada | 🟠 Media | Medio |
| 6 | Promociones sin implementar | 🟡 Media | Alto |
| 7 | Facturación sin implementar | 🟡 Media | Alto |
| 8 | MeseroController vacío | 🟡 Media | Medio |
| 9 | Sin capa de servicios | 🟢 Baja | Alto |
| 10 | Sin tests automatizados | 🟢 Baja | Alto |
| 11 | Polling → WebSockets | 🟢 Baja | Medio |
| 12 | Sin API REST | 🟢 Baja | Alto |
