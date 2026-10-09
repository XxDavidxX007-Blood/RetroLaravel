# Sistema de Restaurante — Visión General

## ¿Qué es este sistema?

Sistema de gestión integral para restaurantes desarrollado con Laravel 13. Cubre el ciclo completo de operación: desde que un cliente hace un pedido en línea hasta que el administrador gestiona el inventario y genera reportes. Todo en una sola aplicación web con roles diferenciados.

---

## Módulos principales

| Módulo | Descripción |
|---|---|
| **Autenticación** | Login, registro, gestión de sesiones |
| **Panel Admin** | Gestión central de todas las operaciones |
| **Panel Cliente** | Catálogo, pedidos, reservas, domicilios |
| **Panel Empleado** | Dashboard del mesero con métricas del día |
| **Pedidos** | Mesa, domicilio y para llevar con estados |
| **Domicilios** | Sub-módulo de pedidos con dirección de entrega |
| **Reservas** | Reserva de mesas con sincronización de estados |
| **Inventario** | Control de stock con movimientos automáticos |
| **Reportes** | Generación y exportación de reportes de inventario |
| **Notificaciones** | Sistema de alertas en tiempo real por rol |
| **Menú** | Catálogo de productos con imágenes y visibilidad |
| **Usuarios** | CRUD de usuarios con roles y estado Activo/Inactivo |

---

## Roles del sistema

### Administrador (`role_id = 1`)
Acceso total al sistema. Gestiona usuarios, menú, pedidos, domicilios, reservas, inventario y reportes. Recibe notificaciones de cada nuevo pedido creado por cualquier cliente.

### Empleado / Mesero (`role_id = 2`)
Accede a un dashboard con métricas operativas del día: reservas, pedidos y domicilios. Puede gestionar reservas desde su panel.

### Cliente (`role_id = 3`)
Navega el catálogo, agrega productos al carrito y realiza pedidos (mesa, domicilio, llevar). Consulta sus propios pedidos, reservas y notificaciones.

---

## Flujo operativo general

```
Cliente navega catálogo
        │
        ▼
  Agrega al carrito (JS)
        │
        ▼
  Elige tipo de pedido
  ┌─────┼──────┐
Mesa  Llevar  Domicilio
  └─────┼──────┘
        │
        ▼
POST /cliente/pedidos ──▶ Descuenta inventario
        │                ──▶ Registra movimiento
        │                ──▶ Notifica al cliente
        │                ──▶ Notifica a admins
        ▼
Admin recibe notificación
        │
        ▼
Cambia estado del pedido
(Pendiente → En preparación → Listo → Entregado)
```

---

## Páginas principales

| URL | Rol | Descripción |
|---|---|---|
| `/` | Público | Landing page |
| `/register` | Público | Registro de clientes |
| `/login` | Público | Inicio de sesión |
| `/dashboard` | Todos | Redirige al dashboard según rol |
| `/catalogo` | Cliente | Catálogo con carrito |
| `/mis-pedidos` | Cliente | Historial de pedidos |
| `/mis-reservas` | Cliente | Gestión de reservas |
| `/domicilios` | Cliente | Pedidos a domicilio |
| `/perfil` | Todos | Ver y editar perfil |
| `/notificaciones` | Todos | Centro de notificaciones |
| `/admin/pedidos` | Admin | Gestión de pedidos |
| `/admin/menu` | Admin | Gestión del menú |
| `/admin/inventario` | Admin | Control de inventario |
| `/admin/reservas` | Admin | Gestión de reservas |
| `/admin/domicilios` | Admin | Pedidos a domicilio |
| `/admin/reportes` | Admin | Generación de reportes |
| `/admin/usuarios` | Admin | CRUD de usuarios |

---

## Datos iniciales (Seeders)

Al ejecutar `php artisan db:seed` se crean automáticamente:

- **3 roles**: Administrador, Empleado, Cliente
- **4 estados de mesa**: Disponible, Ocupada, Reservada, Mantenimiento
- **5 estados de pedido**: Pendiente, En preparación, Listo, Entregado, Cancelado
- **3 tipos de pedido**: Mesa, Domicilio, Para llevar
- **10 mesas** en distintas zonas del restaurante
- **7 usuarios de prueba** (2 admins, 2 empleados, 3 clientes)
- Categorías de productos y productos del menú

---

## Funcionalidades pendientes / parciales

| Funcionalidad | Estado |
|---|---|
| Promociones | Modelo y migración listos, sin controlador ni vistas |
| Facturación | Tablas listas (`facturas`, `detalles_factura`), sin flujo implementado |
| MeseroController | Archivo existe, sin rutas expuestas |
| API REST | No existe `routes/api.php`, todo es web con Blade |
