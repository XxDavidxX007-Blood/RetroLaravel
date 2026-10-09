# Gestión del Perfil de Usuario

## Descripción

Todos los usuarios autenticados pueden ver y actualizar su perfil personal desde `/perfil`. La vista es compartida entre roles — el layout cambia automáticamente según si el usuario es administrador o no.

---

## Rutas

| Método | URI | Controlador | Nombre |
|---|---|---|---|
| GET | `/perfil` | `PerfilController@index` | `perfil` |
| PUT | `/perfil` | `PerfilController@update` | `perfil.update` |

Ambas requieren `middleware('auth')`.

---

## Vista del perfil

```
GET /perfil → PerfilController@index
```

```php
public function index()
{
    $user = auth()->user()->load('role');

    // Layout dinámico según rol
    $layout = ($user->role && $user->role->nombre === 'Administrador')
        ? 'layouts.admin'
        : 'layouts.app';

    return view('perfil.index', compact('user', 'layout'));
}
```

La vista `perfil/index.blade.php` extiende `$layout`, por lo que se renderiza con el sidebar de admin o el navbar de cliente/empleado según corresponda.

---

## Actualizar perfil

```
PUT /perfil → PerfilController@update
```

### Campos actualizables

| Campo | Regla de validación | Notas |
|---|---|---|
| `name` | `required|string|max:255` | Nombre |
| `apellidos` | `nullable|string|max:255` | Apellidos |
| `telefono` | `nullable|string|max:50` | Teléfono |
| `password` | `nullable|string|min:6|confirmed` | Solo se actualiza si se envía |
| `foto` | `nullable|image|max:2048` | Imagen de perfil (máx 2 MB) |

### Lógica de actualización

```php
public function update(Request $request)
{
    $user = auth()->user();

    // Validación ...

    $data = [
        'name'      => $request->input('name'),
        'apellidos' => $request->input('apellidos', ''),
        'telefono'  => $request->input('telefono'),
    ];

    // Password: solo actualiza si se envió un valor
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->input('password'));
    }

    // Foto: elimina la anterior del disco antes de guardar la nueva
    if ($request->hasFile('foto')) {
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }
        $data['foto'] = $request->file('foto')->store('perfiles', 'public');
    }

    $user->update($data);

    return back()->with('success', 'Perfil actualizado correctamente.');
}
```

---

## Gestión de la foto de perfil

### Almacenamiento

Las fotos se guardan en `storage/app/public/perfiles/`:

```
storage/app/public/perfiles/
    randomstring123.jpg
    otroimagen456.png
```

Se acceden públicamente desde:
```
http://app.test/storage/perfiles/randomstring123.jpg
```

Requiere haber ejecutado `php artisan storage:link` previamente.

### Eliminación de la foto anterior

Antes de guardar una nueva foto, el sistema elimina la anterior del disco para no acumular archivos huérfanos:

```php
if ($user->foto && Storage::disk('public')->exists($user->foto)) {
    Storage::disk('public')->delete($user->foto);
}
```

El campo `foto` en la tabla `users` almacena la ruta relativa dentro del disco `public` (ej: `perfiles/abc123.jpg`), no la URL completa.

### En la vista

```blade
{{-- Mostrar foto actual o avatar por defecto --}}
@if ($user->foto)
    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto de perfil">
@else
    <img src="{{ asset('img/avatar-default.png') }}" alt="Sin foto">
@endif
```

---

## Campos del modelo `User`

Todos los campos que el usuario puede gestionar desde el perfil:

| Campo | Tipo | Obligatorio |
|---|---|---|
| `name` | VARCHAR | Sí |
| `apellidos` | VARCHAR NULLABLE | No |
| `telefono` | VARCHAR NULLABLE | No |
| `email` | VARCHAR UNIQUE | No (no editable desde perfil) |
| `password` | VARCHAR | Solo si se quiere cambiar |
| `foto` | VARCHAR NULLABLE | No |

El `email` y el `role_id` **no son editables** desde la vista de perfil — solo el administrador puede cambiar el email o rol desde el panel de usuarios.

---

## Campos ocultos del modelo

```php
protected $hidden = ['password', 'remember_token'];
```

El password nunca se expone en las respuestas del modelo. Al comparar contraseñas se usa `Hash::check()` implícitamente por `Auth::attempt()`.

---

## Campos especiales en pedidos a domicilio

Cuando un cliente hace un pedido a domicilio y no tiene teléfono registrado, el sistema actualiza automáticamente su campo `telefono` con el que proporcionó en el formulario del pedido:

```php
// ClientePedidoController@store
if (empty($user->telefono) && !empty($validated['telefono'])) {
    $user->update(['telefono' => $validated['telefono']]);
}
```

Esto significa que completar un pedido a domicilio puede actualizar el perfil del usuario de forma transparente.
