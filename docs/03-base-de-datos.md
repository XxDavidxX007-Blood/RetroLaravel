# Esquema de Base de Datos

## Resumen de tablas

El sistema cuenta con **24 tablas** en total: 21 de dominio de negocio + 3 de infraestructura Laravel (sessions, cache, jobs).

---

## Diagrama de entidades

```
roles ──< users >──── clientes ──< reservas >── mesas
                  └── meseros          │
                  └── notificaciones   └── estado_reservas
                  └── reportes
                                    mesas ── estado_mesas

tipos_pedido ──< pedidos >── clientes
estados_pedido ─┘      └──< detalles_pedido >── productos
                        └── meseros (nullable)   │
                        └── facturas ──< detalles_factura >── productos
                                                 │
                                       productos ── categorias_productos
                                                 └── inventarios ──< movimientos_inventario
                                                                        └── users

promociones (sin relaciones implementadas)
```

---

## Tablas de dominio

### `roles`
```sql
id              BIGINT PK AUTO_INCREMENT
nombre          VARCHAR   -- Administrador | Empleado | Cliente
descripcion     TEXT
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `users`
```sql
id              BIGINT PK AUTO_INCREMENT
name            VARCHAR NOT NULL
apellidos       VARCHAR NULLABLE
email           VARCHAR UNIQUE NOT NULL
telefono        VARCHAR NULLABLE
password        VARCHAR NOT NULL
foto            VARCHAR NULLABLE       -- ruta relativa en storage
role_id         BIGINT FK → roles.id
estado          VARCHAR DEFAULT 'Activo'  -- Activo | Inactivo
remember_token  VARCHAR NULLABLE
email_verified_at TIMESTAMP NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `clientes`
```sql
id              BIGINT PK AUTO_INCREMENT
user_id         BIGINT FK → users.id (unique)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```
> Tabla puente que extiende `users` para el rol Cliente. Se crea automáticamente al registrarse.

### `meseros`
```sql
id              BIGINT PK AUTO_INCREMENT
user_id         BIGINT FK → users.id (unique)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```
> Tabla puente que extiende `users` para el rol Empleado.

---

### `estado_mesas`
```sql
id              BIGINT PK AUTO_INCREMENT
nombre_estado   VARCHAR  -- Disponible | Ocupada | Reservada | Mantenimiento
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `mesas`
```sql
id              BIGINT PK AUTO_INCREMENT
numero_mesa     INTEGER NOT NULL
capacidad       INTEGER NOT NULL
ubicacion       VARCHAR NULLABLE    -- zona del restaurante
estado_mesa_id  BIGINT FK → estado_mesas.id
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

---

### `estado_reservas`
```sql
id              BIGINT PK AUTO_INCREMENT
nombre_estado   VARCHAR  -- Pendiente | Confirmada | Cancelada | Completada
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `reservas`
```sql
id                  BIGINT PK AUTO_INCREMENT
cliente_id          BIGINT FK → clientes.id NULLABLE  -- nullable por migración posterior
mesa_id             BIGINT FK → mesas.id
fecha_reserva       DATE NOT NULL
hora_reserva        TIME NOT NULL
cantidad_personas   INTEGER NOT NULL
observaciones       TEXT NULLABLE
estado_reserva_id   BIGINT FK → estado_reservas.id
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### `categorias_productos`
```sql
id              BIGINT PK AUTO_INCREMENT
nombre          VARCHAR NOT NULL
descripcion     TEXT NULLABLE
estado          BOOLEAN DEFAULT true
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `productos`
```sql
id                      BIGINT PK AUTO_INCREMENT
categoria_producto_id   BIGINT FK → categorias_productos.id
nombre                  VARCHAR NOT NULL
descripcion             TEXT NULLABLE
precio                  DECIMAL(10,2) NOT NULL
imagen                  VARCHAR NULLABLE   -- URL externa o ruta en storage
estado                  BOOLEAN DEFAULT true  -- visible en catálogo
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

### `inventarios`
```sql
id              BIGINT PK AUTO_INCREMENT
producto_id     BIGINT FK → productos.id (unique)
cantidad        INTEGER DEFAULT 0
stock_minimo    INTEGER DEFAULT 5
stock_maximo    INTEGER DEFAULT 50
unidad          VARCHAR DEFAULT 'unidades'
created_at      TIMESTAMP
updated_at      TIMESTAMP
```
> Se crea automáticamente cuando se agrega un producto al menú.

### `movimientos_inventario`
```sql
id              BIGINT PK AUTO_INCREMENT
inventario_id   BIGINT FK → inventarios.id
tipo            VARCHAR  -- Entrada | Salida
cantidad        INTEGER NOT NULL
motivo          TEXT NULLABLE
user_id         BIGINT FK → users.id
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

---

### `tipos_pedido`
```sql
id              BIGINT PK AUTO_INCREMENT
nombre          VARCHAR  -- Mesa | Domicilio | Para llevar
descripcion     TEXT NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `estados_pedido`
```sql
id              BIGINT PK AUTO_INCREMENT
nombre_estado   VARCHAR  -- Pendiente | En preparación | Listo | Entregado | Cancelado
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `pedidos`
```sql
id                  BIGINT PK AUTO_INCREMENT
cliente_id          BIGINT FK → clientes.id (restrictOnDelete)
mesero_id           BIGINT FK → meseros.id NULLABLE (nullOnDelete)
tipo_pedido_id      BIGINT FK → tipos_pedido.id
estado_pedido_id    BIGINT FK → estados_pedido.id
total               DECIMAL(10,2) DEFAULT 0
observaciones       TEXT NULLABLE   -- ver nota abajo
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

> **Nota sobre `observaciones`:** Este campo almacena información estructurada como texto libre. Ejemplos:
> - Mesa: `"Mesa #3 (Cap. 4) | Notas: Burger: sin cebolla"`
> - Domicilio: `"Dirección: Calle 10 #23 (Chapinero) | Tel: 3001234567 | Notas: ..."`
> - Para llevar: `"Para llevar - Recoge: Juan Pérez a las 13:30 | Notas: ..."`

### `detalles_pedido`
```sql
id                  BIGINT PK AUTO_INCREMENT
pedido_id           BIGINT FK → pedidos.id (cascadeOnDelete)
producto_id         BIGINT FK → productos.id
cantidad            INTEGER NOT NULL
precio_unitario     DECIMAL(10,2) NOT NULL   -- precio al momento del pedido
subtotal            DECIMAL(10,2) NOT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```
> `precio_unitario` captura el precio en el momento del pedido. Cambios futuros al precio del producto no afectan pedidos anteriores.

---

### `facturas`
```sql
id              BIGINT PK AUTO_INCREMENT
pedido_id       BIGINT FK → pedidos.id (unique)
fecha_factura   DATE NOT NULL
subtotal        DECIMAL(10,2)
impuestos       DECIMAL(10,2)
descuento       DECIMAL(10,2) DEFAULT 0
total           DECIMAL(10,2)
metodo_pago     VARCHAR NULLABLE
estado          VARCHAR NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `detalles_factura`
```sql
id                  BIGINT PK AUTO_INCREMENT
factura_id          BIGINT FK → facturas.id (cascadeOnDelete)
producto_id         BIGINT FK → productos.id
cantidad            INTEGER NOT NULL
precio_unitario     DECIMAL(10,2) NOT NULL
subtotal            DECIMAL(10,2) NOT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### `notificaciones`
```sql
id              BIGINT PK AUTO_INCREMENT
user_id         BIGINT FK → users.id (cascadeOnDelete)
tipo            VARCHAR  -- pedido | domicilio | reserva | inventario | stock
titulo          VARCHAR NOT NULL
mensaje         TEXT NOT NULL
referencia_id   BIGINT NULLABLE  -- ID del pedido/reserva relacionado
leida           BOOLEAN DEFAULT false
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEX: (user_id, leida)
```

### `reportes`
```sql
id                  BIGINT PK AUTO_INCREMENT
user_id             BIGINT FK → users.id
generado_por        VARCHAR NULLABLE   -- nombre del usuario
filtro_usado        VARCHAR NULLABLE   -- todo | stock_bajo | sin_stock | categoria_X
formato             VARCHAR NULLABLE   -- pantalla | csv
total_productos     INTEGER NULLABLE
valor_total         DECIMAL(10,2) NULLABLE
datos_json          JSON NULLABLE      -- array con los ítems del reporte
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### `promociones`
```sql
id              BIGINT PK AUTO_INCREMENT
titulo          VARCHAR NOT NULL
descripcion     TEXT NULLABLE
descuento       DECIMAL(5,2)    -- porcentaje
fecha_inicio    DATE NULLABLE
fecha_fin       DATE NULLABLE
activa          BOOLEAN DEFAULT true
created_at      TIMESTAMP
updated_at      TIMESTAMP
```
> Sin controlador ni rutas implementadas aún.

---

## Tablas de infraestructura Laravel

### `sessions`
Almacena sesiones de usuario en base de datos (driver `database`).

### `cache`
Almacena entradas de caché (driver `database`).

### `jobs` / `failed_jobs`
Colas de trabajo en base de datos (driver `database`).

---

## Integridad referencial — resumen

| Relación | Comportamiento al eliminar |
|---|---|
| `users` → `clientes` | cascade |
| `users` → `meseros` | cascade |
| `users` → `notificaciones` | cascade |
| `pedidos` → `detalles_pedido` | cascade |
| `facturas` → `detalles_factura` | cascade |
| `pedidos.cliente_id` → `clientes` | restrict (no se puede eliminar cliente con pedidos) |
| `pedidos.mesero_id` → `meseros` | set null (el pedido queda sin mesero) |
| `reservas.cliente_id` | nullable (admite reservas sin cliente) |

---

## Comandos de base de datos

```bash
# Crear todas las tablas
php artisan migrate

# Crear tablas + poblar datos iniciales
php artisan migrate:fresh --seed

# Solo los seeders (tablas ya existentes)
php artisan db:seed

# Ver estado de migraciones
php artisan migrate:status
```
