# Seeders y Datos Iniciales

## Descripción

Los seeders pueblan la base de datos con datos de arranque necesarios para que el sistema funcione correctamente. Se ejecutan en un orden específico para respetar las dependencias entre tablas.

---

## Orden de ejecución (`DatabaseSeeder`)

```php
$this->call([
    RoleSeeder::class,              // 1 — Roles (sin dependencias)
    EstadoMesaSeeder::class,        // 2 — Estados de mesa
    EstadoReservaSeeder::class,     // 3 — Estados de reserva
    EstadoPedidoSeeder::class,      // 4 — Estados de pedido
    TipoPedidoSeeder::class,        // 5 — Tipos de pedido
    CategoriaProductoSeeder::class, // 6 — Categorías de productos
    MesaSeeder::class,              // 7 — Mesas (requiere EstadoMesa)
    UserSeeder::class,              // 8 — Usuarios + perfiles Cliente/Mesero (requiere Role)
    ProductoSeeder::class,          // 9 — Productos + Inventario (requiere Categorias)
]);
```

> `PedidoSeeder` existe en el directorio pero **no está incluido** en `DatabaseSeeder`. Para usarlo hay que llamarlo manualmente: `php artisan db:seed --class=PedidoSeeder`.

---

## Detalle de cada seeder

### 1. `RoleSeeder`

Crea los 3 roles del sistema usando `updateOrCreate` (idempotente — se puede ejecutar múltiples veces sin duplicar):

| id | nombre | descripción |
|---|---|---|
| — | Administrador | Acceso completo al sistema |
| — | Empleado | Acceso a las funciones operativas |
| — | Cliente | Acceso para clientes |

> Los IDs no están hardcodeados — se buscan por nombre en los seeders posteriores.

---

### 2. `EstadoMesaSeeder`

| nombre_estado |
|---|
| Disponible |
| Ocupada |
| Reservada |
| Mantenimiento |

---

### 3. `EstadoReservaSeeder`

| nombre_estado |
|---|
| Pendiente |
| Confirmada |
| Cancelada |
| Completada |
| No asistió (probable — según lógica del controlador) |

---

### 4. `EstadoPedidoSeeder`

| nombre_estado |
|---|
| Pendiente |
| En preparación |
| Listo |
| Entregado |
| Cancelado |

> El controlador busca estos estados con `LIKE '%pendiente%'`, `LIKE '%preparaci%'`, etc., lo que hace el matching tolerante a pequeñas variaciones de nombre.

---

### 5. `TipoPedidoSeeder`

| id (aprox) | nombre |
|---|---|
| 1 | Mesa |
| 2 | Domicilio |
| 3 | Para llevar |

> El `DashboardController` usa `where('tipo_pedido_id', 2)` para identificar domicilios. Si el orden de inserción cambia, esta lógica se rompe. Es una fragilidad conocida del sistema.

---

### 6. `CategoriaProductoSeeder`

Crea 10 categorías, todas con `estado = true`:

| # | Nombre |
|---|---|
| 1 | Carnes |
| 2 | Lácteos |
| 3 | Verduras |
| 4 | Frutas |
| 5 | Bebidas |
| 6 | Granos y Cereales |
| 7 | Mariscos |
| 8 | Condimentos |
| 9 | Panadería |
| 10 | Postres |

---

### 7. `MesaSeeder`

Crea 10 mesas, todas con estado `Disponible` (buscado por nombre, no por ID):

| # Mesa | Capacidad | Ubicación |
|---|---|---|
| 1 | 2 | Zona ventana |
| 2 | 2 | Zona ventana |
| 3 | 4 | Centro |
| 4 | 4 | Centro |
| 5 | 4 | Centro |
| 6 | 6 | Zona lateral |
| 7 | 6 | Zona lateral |
| 8 | 8 | Terraza |
| 9 | 8 | Terraza |
| 10 | 10 | Salón privado |

Usa `updateOrCreate(['numero_mesa' => X], [...])` — idempotente.

---

### 8. `UserSeeder`

Crea 7 usuarios. Los roles se buscan por nombre antes de asignar el ID. Después de crear cada usuario, crea automáticamente el perfil extendido según el rol:

```php
if ($user->role_id == $clienteId) {
    Cliente::firstOrCreate(['user_id' => $user->id]);
} elseif ($user->role_id == $empleadoId) {
    Mesero::firstOrCreate(['user_id' => $user->id]);
}
```

**Datos de los usuarios:**

| Nombre | Email | Teléfono | Rol |
|---|---|---|---|
| alicia robles | alicia@gmail.com | 3204417080 | Cliente |
| hades perez | hades@gmail.com | 3651916255 | Cliente |
| mario vega | mario@gmail.com | 321561651 | Cliente |
| keiner trejos | keiner@gmail.com | 3201651516 | Empleado |
| melani rojas | melani@gmail.com | 3156218 | Empleado |
| sharon tovar | tovar@gmail.com | 3204417080 | Administrador |
| david ovalle | david@gmail.com | 3204417080 | Administrador |

**Contraseña de todos:** `password123`

---

### 9. `ProductoSeeder`

Crea 19 productos distribuidos en 6 de las 10 categorías disponibles. Cada producto genera su inventario con `updateOrCreate`.

| Categoría | Productos | Stock inicial |
|---|---|---|
| Carnes | Hamburguesa Clásica ($15.000), Hamburguesa Doble Queso ($20.000), Costillas BBQ ($28.000), Pechuga a la Plancha ($18.000), Carne Mechada ($22.000) | 50, 40, 25, 30, 20 |
| Bebidas | Limonada Natural ($5.000), Jugo de Naranja ($5.000), Cerveza Artesanal ($8.000), Coca-Cola 500ml ($4.000), Agua Mineral ($3.000) | 100, 80, 60, 120, 150 |
| Postres | Tiramisú ($12.000), Cheesecake ($10.000), Helado Artesanal ($7.000) | 15, 20, 30 |
| Mariscos | Ceviche de Camarón ($25.000), Camarones Al Ajillo ($27.000) | 20, 18 |
| Verduras | Ensalada César ($12.000), Ensalada Tropical ($14.000) | 35, 30 |
| Panadería | Pan de Ajo ($6.000), Bruschetta ($8.000) | 40, 25 |

Configuración de inventario para todos:
- `stock_minimo`: 5
- `stock_maximo`: `stock_inicial * 2`
- `descripcion`: `"Delicioso {nombre} preparado en Retro Restaurant"`

---

## Comandos de seeders

```bash
# Ejecutar todos los seeders (DatabaseSeeder)
php artisan db:seed

# Ejecutar un seeder específico
php artisan db:seed --class=ProductoSeeder
php artisan db:seed --class=PedidoSeeder

# Reset completo + migraciones + seeders
php artisan migrate:fresh --seed

# Solo los seeders sin borrar tablas (datos actuales se mantienen)
# Nota: como usan updateOrCreate, no duplican registros
php artisan db:seed
```

---

## Idempotencia

Todos los seeders del proyecto usan `updateOrCreate` o `firstOrCreate` en lugar de `create`, lo que significa que **pueden ejecutarse múltiples veces sin crear duplicados**. Esto es útil en entornos de desarrollo donde se corre `php artisan db:seed` frecuentemente.
