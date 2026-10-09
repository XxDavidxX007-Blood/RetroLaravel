# Dashboards Diferenciados por Rol

## Descripción

El sistema tiene un único punto de entrada `/dashboard` que detecta el rol del usuario autenticado y renderiza una vista completamente diferente para cada uno. No hay una sola vista de dashboard — hay tres, cada una con datos y acciones propias de su rol.

---

## Punto de entrada

```
GET /dashboard → DashboardController@index
```

```php
public function index()
{
    $usuario = Auth::user();

    switch ($usuario->role_id) {
        case 1: return view('admin.dashboard');
        case 2: return $this->empleadoDashboard($usuario);
        case 3: return $this->clienteDashboard($usuario);
        default: abort(403, 'Rol no autorizado');
    }
}
```

---

## Dashboard del Administrador (role_id = 1)

**Vista:** `admin/dashboard.blade.php`  
**Layout:** `layouts/Admin.blade.php`

El dashboard de admin no recibe datos dinámicos desde el controlador — la vista los carga directamente mediante JavaScript y llamadas AJAX a los endpoints de reportes y datos en vivo.

### Acceso rápido desde el sidebar

- Usuarios
- Menú
- Pedidos
- Domicilios
- Reservas
- Inventario
- Reportes
- Notificaciones

---

## Dashboard del Empleado / Mesero (role_id = 2)

**Vista:** `empleado/dashboard.blade.php`  
**Layout:** `layouts/App.blade.php`

### Datos que recibe la vista

```php
private function empleadoDashboard($usuario)
{
    $hoy = Carbon::today();

    $reservasHoy    = Reserva::whereDate('fecha_reserva', $hoy)->count();

    $pedidosHoy     = Pedido::whereDate('created_at', $hoy)
                            ->where('tipo_pedido_id', '!=', 2)  // excluye domicilios
                            ->count();

    $domiciliosHoy  = Pedido::whereDate('created_at', $hoy)
                            ->where('tipo_pedido_id', 2)
                            ->count();

    // Datos para el modal de nueva reserva
    $mesas    = Mesa::with('estadoMesa')->orderBy('numero_mesa')->get();
    $estados  = EstadoReserva::all();
    $clientes = Cliente::with('user')->get();

    return view('empleado.dashboard', compact(...));
}
```

| Variable | Descripción |
|---|---|
| `$reservasHoy` | Total de reservas programadas para hoy |
| `$pedidosHoy` | Pedidos de mesa + para llevar creados hoy |
| `$domiciliosHoy` | Pedidos a domicilio creados hoy |
| `$mesas` | Todas las mesas con su estado actual |
| `$estados` | Estados de reserva disponibles |
| `$clientes` | Todos los clientes para el modal de nueva reserva |

El empleado puede crear reservas directamente desde su dashboard mediante un modal que incluye la grilla de mesas con su estado visual.

---

## Dashboard del Cliente (role_id = 3)

**Vista:** `cliente/dashboard.blade.php`  
**Layout:** `layouts/App.blade.php`

### Datos que recibe la vista

```php
private function clienteDashboard($usuario)
{
    $cliente = $usuario->cliente;

    // Próximas reservas (hoy o futuras), máximo 5
    $reservasProximas = $cliente
        ? Reserva::where('cliente_id', $cliente->id)
            ->whereDate('fecha_reserva', '>=', Carbon::today())
            ->with(['mesa', 'estadoReserva'])
            ->orderBy('fecha_reserva')
            ->orderBy('hora_reserva')
            ->take(5)
            ->get()
        : collect();

    $totalReservas   = $reservasProximas->count();

    // Total histórico de pedidos del cliente
    $totalPedidos    = $cliente
        ? Pedido::where('cliente_id', $cliente->id)->count()
        : 0;

    // Total histórico de domicilios (tipo_pedido_id = 2)
    $totalDomicilios = $cliente
        ? Pedido::where('cliente_id', $cliente->id)
                ->where('tipo_pedido_id', 2)->count()
        : 0;

    // Total de productos visibles en el menú
    $totalProductos = Producto::where('estado', true)->count();

    return view('cliente.dashboard', compact(...));
}
```

| Variable | Descripción |
|---|---|
| `$reservasProximas` | Hasta 5 reservas futuras o de hoy del cliente |
| `$totalReservas` | Conteo de las anteriores |
| `$totalPedidos` | Historial completo de pedidos del cliente |
| `$totalDomicilios` | Historial de pedidos a domicilio |
| `$totalProductos` | Cuántos platos hay disponibles en el menú |

### Acciones disponibles desde el dashboard del cliente

- Ver sus próximas reservas con mesa y estado
- Navegar al catálogo para hacer un nuevo pedido
- Ver el historial de pedidos (`/mis-pedidos`)
- Ver el historial de domicilios (`/domicilios`)
- Gestionar sus reservas (`/mis-reservas`)

---

## Comparativa de los tres dashboards

| Aspecto | Administrador | Empleado | Cliente |
|---|---|---|---|
| Vista | `admin/dashboard` | `empleado/dashboard` | `cliente/dashboard` |
| Layout | `layouts.admin` | `layouts.app` | `layouts.app` |
| Datos del controlador | Ninguno (carga vía JS) | Métricas del día + datos para modal | Reservas próximas + contadores históricos |
| Acciones principales | Gestión completa del sistema | Ver métricas + crear reservas | Ver pedidos + crear pedidos + reservas |
| Sidebar / Navbar | Sidebar con módulos admin | Navbar de empleado | Navbar de cliente |

---

## Manejo de usuario sin perfil de cliente

Si un usuario con `role_id = 3` no tiene registro en `clientes` (caso raro pero posible), el dashboard lo maneja con `collect()` vacío y contadores en `0`, evitando errores:

```php
$cliente = $usuario->cliente; // puede ser null

$reservasProximas = $cliente ? Reserva::where(...)->get() : collect();
$totalPedidos     = $cliente ? Pedido::where(...)->count() : 0;
```
