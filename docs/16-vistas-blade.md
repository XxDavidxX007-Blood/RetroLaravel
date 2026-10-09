# Estructura de Vistas y Layouts Blade

## Árbol completo de vistas

```
resources/views/
│
├── Index.blade.php                    ← Landing page pública (ruta /)
├── welcome.blade.php                  ← Vista welcome de Laravel (no usada en producción)
├── Dashboard.blade.php                ← Archivo legacy (el sistema usa vistas por rol)
│
├── layouts/
│   ├── App.blade.php                  ← Layout general: clientes y empleados
│   └── Admin.blade.php                ← Layout panel admin
│
├── auth/
│   ├── Login.blade.php                ← Formulario de inicio de sesión
│   └── Register.blade.php             ← Formulario de registro
│
├── admin/
│   ├── dashboard.blade.php            ← Dashboard del administrador
│   ├── usuarios/
│   │   └── index.blade.php            ← CRUD de usuarios
│   ├── menu/
│   │   └── index.blade.php            ← Gestión del catálogo de productos
│   ├── pedidos/
│   │   └── index.blade.php            ← Gestión de pedidos (mesa / para llevar)
│   ├── domicilios/
│   │   └── index.blade.php            ← Gestión de pedidos a domicilio
│   ├── reservas/
│   │   └── index.blade.php            ← Gestión de reservas
│   ├── inventario/
│   │   ├── index.blade.php            ← Panel principal de inventario
│   │   └── sugerencias.blade.php      ← Productos con stock bajo
│   ├── reportes/
│   │   └── index.blade.php            ← Generador de reportes
│   └── notificaciones/
│       └── index.blade.php            ← Centro de notificaciones (admin)
│
├── cliente/
│   ├── dashboard.blade.php            ← Dashboard del cliente
│   ├── catalogo.blade.php             ← Catálogo de productos con carrito
│   ├── pedidos.blade.php              ← Historial: pedidos de mesa y para llevar
│   ├── domicilios.blade.php           ← Historial: pedidos a domicilio
│   ├── reservas.blade.php             ← Gestión de reservas del cliente
│   ├── notificaciones.blade.php       ← Centro de notificaciones (cliente)
│   ├── perfil.blade.php               ← Vista de perfil (solo cliente)
│   └── _mesas_grid.blade.php          ← Partial: grilla visual de mesas
│
├── empleado/
│   └── dashboard.blade.php            ← Dashboard del mesero
│
├── perfil/
│   └── index.blade.php                ← Vista compartida de perfil (todos los roles)
│
└── productos/
    └── index.blade.php                ← Vista resource de productos (ProductoController)
```

---

## Layouts

### `layouts/App.blade.php`
Usado por **clientes** y **empleados**.

Estructura típica:
```html
<html>
  <head>...</head>
  <body>
    <nav>           ← Navbar con logo, enlaces según rol, dropdown notificaciones, menú usuario
    <main>
      @yield('content')   ← Contenido de cada vista
    </main>
    <footer>
    @stack('scripts')     ← Scripts JS específicos de cada vista
  </body>
</html>
```

El navbar consulta `/notificaciones/latest` vía `fetch()` de forma periódica para actualizar el contador de no leídas.

---

### `layouts/Admin.blade.php`
Usado exclusivamente por **administradores**.

Estructura típica:
```html
<html>
  <head>...</head>
  <body>
    <aside>         ← Sidebar con enlaces a todos los módulos admin
    <div class="main-content">
      <header>      ← Topbar con nombre de usuario, notificaciones, logout
      <main>
        @yield('content')
      </main>
    </div>
    @stack('scripts')
  </body>
</html>
```

---

## Patrones comunes en las vistas

### Herencia de layout

Todas las vistas extienden su layout correspondiente:

```blade
{{-- Vista de cliente --}}
@extends('layouts.app')

@section('content')
  {{-- contenido --}}
@endsection

@push('scripts')
  <script>
    // JS específico de la vista
  </script>
@endpush
```

```blade
{{-- Vista de admin --}}
@extends('layouts.admin')

@section('content')
  {{-- contenido --}}
@endsection
```

---

### Mensajes flash

Las vistas muestran mensajes de éxito y error provenientes de `session('success')` y `session('error')`:

```blade
@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
```

---

### Paginación

Las vistas de listado usan el componente de paginación de Laravel. La mayoría pagina con **7 elementos por página**:

```php
// En el controlador
$pedidos = $query->latest()->paginate(7)->withQueryString();
```

```blade
{{-- En la vista --}}
{{ $pedidos->links() }}
```

`withQueryString()` preserva los filtros activos al cambiar de página.

---

### Formularios con CSRF y método spoofing

```blade
{{-- PUT / PATCH / DELETE requieren spoofing en formularios HTML --}}
<form action="{{ route('admin.reservas.update', $reserva) }}" method="POST">
  @csrf
  @method('PUT')
  ...
</form>

<form action="{{ route('admin.pedidos.destroy', $pedido) }}" method="POST">
  @csrf
  @method('DELETE')
  ...
</form>
```

---

### Partial `_mesas_grid.blade.php`

Vista parcial incluida en las vistas de reservas. Muestra una grilla visual de las 10 mesas con su estado actual (colores distintos para Disponible, Ocupada, Reservada, Mantenimiento). Se incluye con:

```blade
@include('cliente._mesas_grid', ['mesas' => $mesas])
```

---

## Vista del perfil — layout dinámico

La vista `perfil/index.blade.php` es compartida por todos los roles. El controlador le pasa la variable `$layout` con el nombre correcto:

```php
// PerfilController@index
$layout = ($user->role->nombre === 'Administrador')
    ? 'layouts.admin'
    : 'layouts.app';

return view('perfil.index', compact('user', 'layout'));
```

```blade
{{-- perfil/index.blade.php --}}
@extends($layout)
```

---

## Convenciones de nomenclatura de vistas

| Convención | Ejemplo |
|---|---|
| Carpetas en minúscula | `admin/`, `cliente/`, `layouts/` |
| Archivos blade con mayúscula o minúscula | `App.blade.php`, `index.blade.php` |
| Partials con prefijo `_` | `_mesas_grid.blade.php` |
| Vistas de módulo en subcarpeta | `admin/pedidos/index.blade.php` |

---

## Módulos con una sola vista (index único)

Todos los módulos del sistema tienen una sola vista `index.blade.php` que concentra el listado, los filtros, los modales de creación/edición y las métricas en una sola página. No hay vistas separadas de `create`, `edit` o `show` — todo se gestiona con modales dentro del index.
