# Referencia de Rutas

El sistema usa exclusivamente rutas web (`routes/web.php`). No existe `routes/api.php`.

Las rutas que devuelven JSON lo hacen mediante `response()->json()` dentro de controladores web estándar (usadas por el frontend con `fetch` / AJAX).

---

## Rutas públicas (sin autenticación)

| Método | URI | Controlador | Nombre | Descripción |
|---|---|---|---|---|
| GET | `/` | Closure | — | Landing page |
| GET | `/login` | `AuthController@showLogin` | `login` | Formulario de login |
| POST | `/login` | `AuthController@login` | `login.post` | Procesar login |
| POST | `/logout` | `AuthController@logout` | `logout` | Cerrar sesión |
| GET | `/register` | `RegisterController@showRegister` | `register` | Formulario de registro |
| POST | `/register` | `RegisterController@register` | `register.post` | Procesar registro |
| GET | `/productos` | `ProductoController@index` | `productos.index` | Listado de productos (resource) |
| POST | `/productos` | `ProductoController@store` | `productos.store` | Crear producto |
| GET | `/productos/{id}` | `ProductoController@show` | `productos.show` | Ver producto |
| PUT/PATCH | `/productos/{id}` | `ProductoController@update` | `productos.update` | Actualizar |
| DELETE | `/productos/{id}` | `ProductoController@destroy` | `productos.destroy` | Eliminar |

---

## Rutas del dashboard

| Método | URI | Controlador | Nombre | Middleware |
|---|---|---|---|---|
| GET | `/dashboard` | `DashboardController@index` | `dashboard` | `auth` |

Redirige a la vista correspondiente según `role_id` del usuario autenticado.

---

## Panel de Administración

Todas las rutas bajo `/admin` requieren `middleware('auth')` y tienen el prefijo de nombre `admin.`.

### Usuarios

| Método | URI | Controlador | Nombre |
|---|---|---|---|
| GET | `/admin/usuarios` | `UserController@index` | `admin.usuarios.index` |
| POST | `/admin/usuarios` | `UserController@store` | `admin.usuarios.store` |
| PUT | `/admin/usuarios/{usuario}` | `UserController@update` | `admin.usuarios.update` |
| PATCH | `/admin/usuarios/{usuario}/estado` | `UserController@toggleStatus` | `admin.usuarios.toggle-status` |
| DELETE | `/admin/usuarios/{usuario}` | `UserController@destroy` | `admin.usuarios.destroy` |

### Menú (productos del catálogo)

| Método | URI | Controlador | Nombre |
|---|---|---|---|
| GET | `/admin/menu` | `MenuController@index` | `admin.menu.index` |
| POST | `/admin/menu` | `MenuController@store` | `admin.menu.store` |
| PUT | `/admin/menu/{producto}` | `MenuController@update` | `admin.menu.update` |
| PATCH | `/admin/menu/{producto}/estado` | `MenuController@toggleStatus` | `admin.menu.toggle-status` |
| DELETE | `/admin/menu/{producto}` | `MenuController@destroy` | `admin.menu.destroy` |

### Pedidos (mesa y para llevar)

| Método | URI | Controlador | Nombre | Respuesta |
|---|---|---|---|---|
| GET | `/admin/pedidos` | `PedidoController@index` | `admin.pedidos.index` | HTML |
| PATCH | `/admin/pedidos/{pedido}/estado` | `PedidoController@updateEstado` | `admin.pedidos.update-estado` | Redirect |
| GET | `/admin/pedidos/{pedido}/detalles` | `PedidoController@detalles` | `admin.pedidos.detalles` | **JSON** |
| DELETE | `/admin/pedidos/{pedido}` | `PedidoController@destroy` | `admin.pedidos.destroy` | Redirect |

> `DELETE` no elimina el registro: cambia el estado a **Cancelado** y reintegra el stock.

### Domicilios

| Método | URI | Controlador | Nombre | Respuesta |
|---|---|---|---|---|
| GET | `/admin/domicilios` | `DomicilioController@index` | `admin.domicilios.index` | HTML |
| POST | `/admin/domicilios` | `DomicilioController@store` | `admin.domicilios.store` | Redirect |
| PATCH | `/admin/domicilios/{pedido}/estado` | `DomicilioController@updateEstado` | `admin.domicilios.update-estado` | Redirect |
| GET | `/admin/domicilios/{pedido}/detalles` | `DomicilioController@detalles` | `admin.domicilios.detalles` | **JSON** |
| DELETE | `/admin/domicilios/{pedido}` | `DomicilioController@destroy` | `admin.domicilios.destroy` | Redirect |

### Reservas

| Método | URI | Controlador | Nombre |
|---|---|---|---|
| GET | `/admin/reservas` | `ReservaController@index` | `admin.reservas.index` |
| POST | `/admin/reservas` | `ReservaController@store` | `admin.reservas.store` |
| PUT | `/admin/reservas/{reserva}` | `ReservaController@update` | `admin.reservas.update` |
| DELETE | `/admin/reservas/{reserva}` | `ReservaController@destroy` | `admin.reservas.destroy` |

### Inventario

| Método | URI | Controlador | Nombre | Respuesta |
|---|---|---|---|---|
| GET | `/admin/inventario` | `InventarioController@index` | `admin.inventario.index` | HTML |
| POST | `/admin/inventario` | `InventarioController@store` | `admin.inventario.store` | Redirect |
| POST | `/admin/inventario/{producto}/stock` | `InventarioController@actualizarStock` | `admin.inventario.actualizar-stock` | Redirect |
| GET | `/admin/inventario/{producto}/historial` | `InventarioController@historial` | `admin.inventario.historial` | **JSON** |
| GET | `/admin/inventario/sugerencias` | `InventarioController@sugerenciasStock` | `admin.inventario.sugerencias` | HTML |
| PUT | `/admin/inventario/{producto}/editar-minimos` | `InventarioController@editarMinimos` | `admin.inventario.editar-minimos` | Redirect |
| DELETE | `/admin/inventario/{producto}` | `InventarioController@destroy` | `admin.inventario.destroy` | Redirect |

### Reportes

| Método | URI | Controlador | Nombre | Respuesta |
|---|---|---|---|---|
| GET | `/admin/reportes` | `ReporteController@index` | `admin.reportes.index` | HTML |
| GET | `/admin/reportes/live-data` | `ReporteController@liveData` | `admin.reportes.live-data` | **JSON** |
| POST | `/admin/reportes/generar` | `ReporteController@generar` | `admin.reportes.generar` | Redirect / CSV |
| GET | `/admin/reportes/{reporte}/ver` | `ReporteController@ver` | `admin.reportes.ver` | **JSON** |
| DELETE | `/admin/reportes/{reporte}` | `ReporteController@destroy` | `admin.reportes.destroy` | Redirect |

---

## Panel del Cliente

Todas requieren `middleware('auth')`.

### Catálogo

| Método | URI | Controlador | Nombre |
|---|---|---|---|
| GET | `/catalogo` | `ProductoController@index` | `cliente.catalogo` |

### Pedidos y domicilios

| Método | URI | Controlador | Nombre | Respuesta |
|---|---|---|---|---|
| GET | `/mis-pedidos` | `ClientePedidoController@indexPedidos` | `pedidos.cliente` | HTML |
| GET | `/domicilios` | `ClientePedidoController@indexDomicilios` | `domicilios.cliente` | HTML |
| POST | `/cliente/pedidos` | `ClientePedidoController@store` | `cliente.pedidos.store` | **JSON** |
| POST | `/cliente/pedidos/{pedido}/cancelar` | `ClientePedidoController@cancelar` | `cliente.pedidos.cancelar` | Redirect |
| GET | `/cliente/pedidos/{pedido}/detalles` | `ClientePedidoController@detalles` | `cliente.pedidos.detalles` | **JSON** |

### Reservas

| Método | URI | Controlador | Nombre |
|---|---|---|---|
| GET | `/mis-reservas` | `ClienteController@reservas` | `reservas.cliente` |
| POST | `/mis-reservas` | `ClienteController@storeReserva` | `reservas.cliente.store` |
| PUT | `/mis-reservas/{reserva}` | `ClienteController@updateReserva` | `reservas.cliente.update` |
| DELETE | `/mis-reservas/{reserva}` | `ClienteController@destroyReserva` | `reservas.cliente.destroy` |

### Perfil

| Método | URI | Controlador | Nombre |
|---|---|---|---|
| GET | `/perfil` | `PerfilController@index` | `perfil` |
| PUT | `/perfil` | `PerfilController@update` | `perfil.update` |

### Notificaciones

| Método | URI | Controlador | Nombre | Respuesta |
|---|---|---|---|---|
| GET | `/notificaciones` | `NotificacionController@index` | `notificaciones.index` | HTML |
| GET | `/notificaciones/latest` | `NotificacionController@getLatest` | `notificaciones.latest` | **JSON** |
| POST | `/notificaciones/{id}/leer` | `NotificacionController@marcarLeida` | `notificaciones.leer` | **JSON** |
| POST | `/notificaciones/leer-todas` | `NotificacionController@marcarTodasLeidas` | `notificaciones.leer-todas` | **JSON** |
| DELETE | `/notificaciones/{id}` | `NotificacionController@destroy` | `notificaciones.destroy` | **JSON** |

---

## Endpoints JSON — referencia rápida

Estos endpoints son consumidos por el frontend JavaScript de la aplicación (no son una API REST pública, no tienen autenticación por token).

```
GET  /admin/pedidos/{id}/detalles        → detalles de un pedido (admin)
GET  /admin/domicilios/{id}/detalles     → detalles de un domicilio (admin)
GET  /admin/inventario/{id}/historial    → últimos 20 movimientos de inventario
GET  /admin/reportes/live-data           → métricas en vivo para dashboard de reportes
GET  /admin/reportes/{id}/ver            → datos JSON de un reporte guardado
POST /cliente/pedidos                    → crear pedido (retorna JSON con código #ORD-XXXXX)
GET  /cliente/pedidos/{id}/detalles      → detalles de un pedido (cliente)
GET  /notificaciones/latest              → últimas 6 notificaciones + total no leídas
POST /notificaciones/{id}/leer           → marcar notificación como leída
POST /notificaciones/leer-todas          → marcar todas como leídas
DELETE /notificaciones/{id}              → eliminar notificación
```

---

## Uso de rutas nombradas en Blade

```blade
{{-- Ejemplos de uso en vistas --}}
<a href="{{ route('admin.pedidos.index') }}">Pedidos</a>
<form action="{{ route('admin.pedidos.update-estado', $pedido) }}" method="POST">
<form action="{{ route('cliente.pedidos.cancelar', $pedido) }}" method="POST">
<a href="{{ route('notificaciones.latest') }}">API notificaciones</a>
```
