# Autenticación y Roles

## Mecanismo de autenticación

El sistema usa la autenticación nativa de Laravel (`Illuminate\Auth`) con sesiones almacenadas en base de datos (`SESSION_DRIVER=database`).

No se usa Laravel Breeze, Jetstream ni Fortify — la autenticación está implementada manualmente en `AuthController` y `RegisterController`.

---

## Registro de usuarios

```
GET  /register → RegisterController@showRegister
POST /register → RegisterController@register
```

### Proceso de registro

```php
// RegisterController@register
$validatedData = $request->validate([
    'name'     => 'required|string|max:255',
    'email'    => 'required|email|unique:users',
    'password' => 'required|string|min:8|confirmed',
]);

// Busca el rol "Cliente" por nombre (no asume ID fijo)
$rolCliente = Role::where('nombre', 'Cliente')->first();

$usuario = User::create([
    'name'     => $validatedData['name'],
    'email'    => $validatedData['email'],
    'password' => bcrypt($validatedData['password']),
    'role_id'  => $rolCliente->id,
    'estado'   => 'Activo',
]);

// Crea el registro de cliente automáticamente
Cliente::create(['user_id' => $usuario->id]);

Auth::login($usuario);
return redirect()->route('dashboard');
```

El registro público siempre crea usuarios con rol **Cliente**. Los roles Admin y Empleado solo los puede asignar un administrador desde el panel.

---

## Inicio de sesión

```
GET  /login → AuthController@showLogin
POST /login → AuthController@login
```

### Proceso de login

```php
// AuthController@login
$credentials = $request->validate([
    'email'    => 'required|email',
    'password' => 'required',
]);

// Verificar si el usuario existe y está activo ANTES de intentar login
$usuario = User::where('email', $credentials['email'])->first();

if ($usuario && $usuario->estado === 'Inactivo') {
    return back()->withErrors([
        'email' => 'Tu cuenta está desactivada. Contacta al administrador.',
    ]);
}

if (Auth::attempt($credentials, $request->boolean('remember'))) {
    $request->session()->regenerate();
    return redirect()->intended(route('dashboard'));
}

return back()->withErrors(['email' => 'Credenciales incorrectas.']);
```

Puntos clave:
- Se verifica el campo `estado` del usuario antes de `Auth::attempt()`
- Soporta "recordarme" (`remember_token`)
- Redirige a `dashboard` que luego enruta según rol

---

## Cierre de sesión

```
POST /logout → AuthController@logout
```

```php
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
return redirect()->route('login');
```

---

## Roles del sistema

| `role_id` | Nombre | Descripción |
|---|---|---|
| 1 | Administrador | Acceso total al sistema |
| 2 | Empleado | Dashboard de operaciones del día |
| 3 | Cliente | Catálogo, pedidos, reservas, perfil |

### Redirección por rol (`DashboardController`)

```php
switch (auth()->user()->role_id) {
    case 1:
        return view('admin.dashboard');
    case 2:
        // carga métricas del día para el mesero
        return view('empleado.dashboard', compact(...));
    case 3:
        // carga reservas próximas y contadores del cliente
        return view('cliente.dashboard', compact(...));
    default:
        abort(403);
}
```

---

## Protección de rutas

### Middleware `auth`
Protege todas las rutas que requieren sesión iniciada. Se aplica en `routes/web.php`:

```php
// Panel admin
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // ...
});

// Panel cliente
Route::middleware('auth')->group(function () {
    // ...
});
```

### ⚠️ Ausencia de middleware de roles
Las rutas admin están protegidas únicamente con `auth`. No existe un middleware que verifique que el usuario tenga `role_id = 1` para acceder a `/admin/*`. El control de roles se realiza manualmente dentro de los controladores mediante `auth()->user()->role_id`.

**Implicación:** Un cliente o empleado autenticado que conozca las URLs del panel admin podría acceder a ellas. Esta es una mejora de seguridad pendiente.

**Solución recomendada:** Agregar un middleware de rol:

```php
// Opción 1: usando Gate en AppServiceProvider
Gate::define('is-admin', fn ($user) => $user->role_id === 1);

// Opción 2: middleware personalizado
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Acceso denegado.');
        }
        return $next($request);
    }
}
```

---

## Gestión de usuarios (admin)

```
GET    /admin/usuarios          → UserController@index
POST   /admin/usuarios          → UserController@store
PUT    /admin/usuarios/{id}     → UserController@update
PATCH  /admin/usuarios/{id}/estado → UserController@toggleStatus
DELETE /admin/usuarios/{id}    → UserController@destroy
```

### Crear usuario (admin)

```php
// UserController@store — lógica simplificada
$usuario = User::create([...]);

if ($request->role_id == 3) {
    Cliente::create(['user_id' => $usuario->id]);
} elseif ($request->role_id == 2) {
    Mesero::create(['user_id' => $usuario->id]);
}
```

Al crear un usuario el admin también crea el perfil extendido (`Cliente` o `Mesero`) según el rol asignado.

### Activar / Desactivar usuario

```
PATCH /admin/usuarios/{usuario}/estado → UserController@toggleStatus
```

Alterna el campo `estado` entre `'Activo'` e `'Inactivo'`. Un usuario inactivo no puede iniciar sesión (verificado en `AuthController@login`).

### Eliminar usuario

```php
// No permite auto-eliminación
if ($usuario->id === auth()->id()) {
    return back()->with('error', 'No puedes eliminar tu propia cuenta.');
}
$usuario->delete();
```

---

## Estado de cuenta

| `estado` | Efecto |
|---|---|
| `Activo` | Puede iniciar sesión normalmente |
| `Inactivo` | El login devuelve error sin intentar autenticar |

---

## Perfil de usuario

```
GET /perfil → PerfilController@index
PUT /perfil → PerfilController@update
```

Campos actualizables:
- `name`, `apellidos`, `telefono`
- `password` (opcional — solo si se envía un valor)
- `foto` (upload a `storage/` — elimina la imagen anterior automáticamente)

```php
// PerfilController@update — password opcional
if ($request->filled('password')) {
    $data['password'] = bcrypt($request->password);
}

// Foto: elimina la anterior del disco
if ($request->hasFile('foto')) {
    if ($user->foto && Storage::exists($user->foto)) {
        Storage::delete($user->foto);
    }
    $data['foto'] = $request->file('foto')->store('fotos', 'public');
}
```

El layout de la vista de perfil se elige según el rol:

```php
$layout = auth()->user()->role_id == 1 ? 'layouts.admin' : 'layouts.app';
return view('perfil.index', compact('layout', 'user'));
```

---

## Usuarios de prueba (seeder)

Todos tienen `password: password123`.

| Email | Rol | Nombre |
|---|---|---|
| sharon@restaurante.com | Administrador | Sharon (admin principal) |
| david@restaurante.com | Administrador | David |
| keiner@restaurante.com | Empleado | Keiner |
| melani@restaurante.com | Empleado | Melani |
| alicia@cliente.com | Cliente | Alicia |
| hades@cliente.com | Cliente | Hades |
| mario@cliente.com | Cliente | Mario |
