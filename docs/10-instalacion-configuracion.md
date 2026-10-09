# Instalación y Configuración

## Requisitos previos

| Herramienta | Versión mínima |
|---|---|
| PHP | 8.3 |
| Composer | 2.x |
| Node.js | 18.x o superior |
| npm | 9.x o superior |
| SQLite | (incluido en PHP) |
| MySQL / MariaDB | 8.0+ (para producción) |

Para desarrollo local se recomienda [Laragon](https://laragon.org/) (Windows) o Laravel Herd (Mac/Windows).

---

## Instalación paso a paso

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd RetroLaravel/sistema_restaurante
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias JavaScript

```bash
npm install
```

### 4. Configurar el archivo de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con los valores del entorno local (ver sección de configuración abajo).

### 5. Crear la base de datos

**Para SQLite (desarrollo):**
```bash
# El archivo ya existe en database/database.sqlite
# Solo ejecutar las migraciones
php artisan migrate --seed
```

**Para MySQL (producción):**
```sql
CREATE DATABASE sistema_restaurante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Luego actualizar `.env` y ejecutar:
```bash
php artisan migrate --seed
```

### 6. Crear el enlace de almacenamiento

```bash
php artisan storage:link
```

Esto crea `public/storage` apuntando a `storage/app/public`, necesario para mostrar imágenes subidas.

### 7. Compilar assets

```bash
# Desarrollo (con watcher)
npm run dev

# Producción (build optimizado)
npm run build
```

### 8. Iniciar el servidor de desarrollo

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`.

---

## Configuración del archivo `.env`

### Base de datos

**SQLite (por defecto para desarrollo):**
```env
DB_CONNECTION=sqlite
# DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD no son necesarios
```

**MySQL (producción):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_restaurante
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### Aplicación

```env
APP_NAME="Sistema Restaurante"
APP_ENV=local          # local | production
APP_KEY=               # generado con php artisan key:generate
APP_DEBUG=true         # false en producción
APP_URL=http://localhost:8000
APP_LOCALE=es
APP_TIMEZONE=America/Bogota
```

### Sesiones, caché y colas

```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

> Todas usan la base de datos por defecto. Para producción con alto tráfico se recomienda Redis:
> ```env
> SESSION_DRIVER=redis
> CACHE_STORE=redis
> QUEUE_CONNECTION=redis
> ```

### Correo

```env
MAIL_MAILER=log        # En desarrollo: los correos se escriben en storage/logs/laravel.log
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=correo@dominio.com
MAIL_PASSWORD=app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@restaurante.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Comandos útiles

### Base de datos

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar migraciones + seeders (borra todo y recrea)
php artisan migrate:fresh --seed

# Solo los seeders (sin borrar tablas)
php artisan db:seed

# Ver estado de migraciones
php artisan migrate:status

# Crear una nueva migración
php artisan make:migration create_tabla_nueva

# Revertir última migración
php artisan migrate:rollback
```

### Desarrollo

```bash
# Iniciar servidor de desarrollo
php artisan serve

# Compilar assets en modo watch
npm run dev

# Build para producción
npm run build

# Limpiar caché de configuración
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Ver todas las rutas registradas
php artisan route:list

# Abrir Tinker (REPL interactivo)
php artisan tinker
```

### Generadores

```bash
# Crear modelo con migración
php artisan make:model NuevoModelo -m

# Crear controlador
php artisan make:controller Admin/NuevoController

# Crear seeder
php artisan make:seeder NuevoSeeder

# Crear factory
php artisan make:factory NuevoFactory
```

---

## Credenciales de prueba

Una vez ejecutado `php artisan migrate:fresh --seed`, estos usuarios están disponibles:

| Email | Contraseña | Rol |
|---|---|---|
| sharon@restaurante.com | password123 | Administrador |
| david@restaurante.com | password123 | Administrador |
| keiner@restaurante.com | password123 | Empleado |
| melani@restaurante.com | password123 | Empleado |
| alicia@cliente.com | password123 | Cliente |
| hades@cliente.com | password123 | Cliente |
| mario@cliente.com | password123 | Cliente |

---

## Estructura de almacenamiento de archivos

Las imágenes de perfil y productos se almacenan en:

```
storage/app/public/
├── fotos/          ← Fotos de perfil de usuarios
└── productos/      ← Imágenes de productos del menú
```

Accesibles públicamente desde:
```
http://localhost:8000/storage/fotos/imagen.jpg
http://localhost:8000/storage/productos/imagen.jpg
```

Requiere haber ejecutado `php artisan storage:link`.

---

## Configuración para producción

### Variables de entorno críticas

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=<clave-generada>
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=<host-bd>
DB_DATABASE=sistema_restaurante
DB_USERNAME=<usuario>
DB_PASSWORD=<password-seguro>
```

### Optimizaciones

```bash
# Cachear configuración, rutas y vistas
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar autoloader de Composer
composer install --optimize-autoloader --no-dev

# Build de assets
npm run build
```

### Permisos de directorios (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Verificación de la instalación

Después de la instalación, verificar que:

- [ ] `http://localhost:8000` muestra la landing page
- [ ] `http://localhost:8000/login` permite iniciar sesión
- [ ] El login con `sharon@restaurante.com` / `password123` redirige al dashboard de administrador
- [ ] El panel admin en `/admin/menu` muestra los productos del seeder
- [ ] Las imágenes de productos se muestran correctamente (requiere `storage:link`)
- [ ] El registro de un nuevo usuario crea el perfil en `/dashboard`
- [ ] `http://localhost:8000/up` responde `200 OK` (health check de Laravel)
