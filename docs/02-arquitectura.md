# Arquitectura y Stack Técnico

## Stack

| Capa | Tecnología | Versión |
|---|---|---|
| Framework | Laravel | 13.17 |
| Lenguaje | PHP | ^8.3 |
| Base de datos | SQLite (dev) / MySQL (prod) | — |
| Frontend | Blade + Vite | — |
| Servidor de desarrollo | Laravel Artisan (`php artisan serve`) | — |
| Gestor de paquetes PHP | Composer | 2.x |
| Gestor de paquetes JS | npm | — |

### Dependencias de producción (`composer.json`)

```json
"require": {
    "php": "^8.3",
    "laravel/framework": "^13.17",
    "laravel/tinker": "^2.10.1"
}
```

El proyecto no tiene paquetes de terceros adicionales — sin Spatie, Sanctum, Breeze ni similares.

### Dependencias de desarrollo

| Paquete | Propósito |
|---|---|
| `laravel/pint` | Formateador de código (PSR-12) |
| `laravel/pail` | Visualizador de logs en tiempo real |
| `phpunit/phpunit ^12` | Suite de pruebas |
| `fakerphp/faker` | Datos falsos para factories |
| `mockery/mockery` | Mocking en tests |
| `nunomaduro/collision` | Mensajes de error mejorados en CLI |

---

## Estructura de directorios

```
sistema_restaurante/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/              ← Controladores del panel admin
│   │       │   ├── DomicilioController.php
│   │       │   ├── InventarioController.php
│   │       │   ├── MenuController.php
│   │       │   ├── PedidoController.php
│   │       │   ├── ReporteController.php
│   │       │   ├── ReservaController.php
│   │       │   └── UserController.php
│   │       ├── AuthController.php
│   │       ├── ClienteController.php
│   │       ├── ClientePedidoController.php
│   │       ├── DashboardController.php
│   │       ├── MeseroController.php
│   │       ├── NotificacionController.php
│   │       ├── PerfilController.php
│   │       ├── ProductoController.php
│   │       └── RegisterController.php
│   ├── Models/                     ← 21 modelos Eloquent
│   └── Providers/
│       └── AppServiceProvider.php  ← Sin lógica personalizada
├── bootstrap/
│   └── app.php                     ← Configuración central de la app
├── config/                         ← Archivos de configuración Laravel
├── database/
│   ├── migrations/                 ← 28 archivos de migración
│   ├── seeders/                    ← 10 seeders
│   ├── factories/
│   │   └── UserFactory.php
│   └── database.sqlite             ← Base de datos de desarrollo
├── resources/
│   └── views/                      ← Plantillas Blade
│       ├── layouts/
│       │   ├── App.blade.php       ← Layout clientes/empleados
│       │   └── Admin.blade.php     ← Layout administradores
│       ├── admin/
│       ├── cliente/
│       ├── empleado/
│       └── auth/
├── routes/
│   ├── web.php                     ← Todas las rutas (no existe api.php)
│   └── console.php
├── public/                         ← Directorio raíz del servidor web
├── storage/                        ← Imágenes subidas, logs, caché
└── vendor/                         ← Dependencias Composer
```

---

## Bootstrap de la aplicación (`bootstrap/app.php`)

```php
Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sin middlewares personalizados registrados
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Responde JSON cuando la ruta es api/* o el cliente espera JSON
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
```

No hay middlewares de grupo personalizados. El único middleware de protección de rutas es el `auth` nativo de Laravel, aplicado en las rutas que lo requieren.

---

## Patrón de arquitectura

El proyecto sigue el patrón **MVC** estándar de Laravel:

```
Request HTTP
     │
     ▼
 routes/web.php          ← Definición de rutas
     │
     ▼
 Controller              ← Lógica de negocio (sin capa de servicios ni repositorios)
     │
     ├──▶ Model (Eloquent) ◀──▶ Base de datos
     │
     └──▶ View (Blade)     ──▶ Response HTML
                           ──▶ Response JSON (para endpoints AJAX)
```

**No hay capa de servicios ni repositorios.** Toda la lógica de negocio vive directamente en los controladores. Esto simplifica la lectura pero concentra la complejidad en archivos como `ClientePedidoController.php`.

---

## Configuración de servicios en `.env`

| Servicio | Driver | Nota |
|---|---|---|
| Base de datos | `sqlite` | En producción cambiar a `mysql` |
| Sesiones | `database` | Tabla `sessions` en BD |
| Caché | `database` | Tabla `cache` en BD |
| Colas | `database` | Tabla `jobs` en BD |
| Correo | `log` | Los correos se escriben en `storage/logs/laravel.log` |

---

## Layouts Blade

### `layouts/App.blade.php`
Usado por clientes y empleados. Incluye navbar con:
- Logo del restaurante
- Menú de navegación según rol
- Dropdown de notificaciones (consulta `/notificaciones/latest` vía AJAX)
- Menú de usuario (perfil, logout)

### `layouts/Admin.blade.php`
Usado por administradores. Incluye sidebar con acceso a todos los módulos admin y el mismo dropdown de notificaciones.

---

## Control de acceso

El sistema no usa middleware de roles — el control es **manual por controlador**:

```php
// Ejemplo en DashboardController
switch (auth()->user()->role_id) {
    case 1: return view('admin.dashboard');
    case 2: return view('empleado.dashboard', $data);
    case 3: return view('cliente.dashboard', $data);
    default: abort(403);
}
```

Las rutas del panel admin (`/admin/*`) solo están protegidas con `middleware('auth')`. Un cliente autenticado que conozca la URL podría acceder — esto es una mejora de seguridad pendiente.

---

## Convenciones de nomenclatura

| Elemento | Convención | Ejemplo |
|---|---|---|
| Tablas | snake_case plural | `detalles_pedido`, `estados_pedido` |
| Modelos | PascalCase singular | `DetallePedido`, `EstadoPedido` |
| Controladores | PascalCase + Controller | `PedidoController` |
| Rutas nombradas | dot.notation | `admin.pedidos.index` |
| Vistas | snake_case en carpetas | `admin/pedidos/index.blade.php` |
| Códigos de pedido | `#ORD-XXXXX` | `#ORD-00042` (ID con padding de 5 dígitos) |
