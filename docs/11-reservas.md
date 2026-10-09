# Módulo de Reservas

## Descripción

El módulo de reservas permite gestionar la ocupación de mesas con antelación. Tanto administradores como clientes pueden crear, editar y cancelar reservas. El estado de la mesa se sincroniza automáticamente con el estado de la reserva en todo momento.

---

## Actores y rutas

### Panel Admin

| Método | URI | Nombre | Acción |
|---|---|---|---|
| GET | `/admin/reservas` | `admin.reservas.index` | Listado con métricas y filtros |
| POST | `/admin/reservas` | `admin.reservas.store` | Crear reserva |
| PUT | `/admin/reservas/{reserva}` | `admin.reservas.update` | Actualizar reserva |
| DELETE | `/admin/reservas/{reserva}` | `admin.reservas.destroy` | Eliminar reserva |

### Panel Cliente

| Método | URI | Nombre | Acción |
|---|---|---|---|
| GET | `/mis-reservas` | `reservas.cliente` | Ver mis reservas |
| POST | `/mis-reservas` | `reservas.cliente.store` | Crear reserva |
| PUT | `/mis-reservas/{reserva}` | `reservas.cliente.update` | Actualizar reserva |
| DELETE | `/mis-reservas/{reserva}` | `reservas.cliente.destroy` | Eliminar reserva |

---

## Estructura de datos

### Tabla `reservas`

| Campo | Tipo | Notas |
|---|---|---|
| `cliente_id` | BIGINT FK NULLABLE | Nullable por migración posterior |
| `mesa_id` | BIGINT FK | Mesa asignada |
| `fecha_reserva` | DATE | |
| `hora_reserva` | TIME | |
| `cantidad_personas` | INTEGER | Mínimo 1, máximo 20 |
| `observaciones` | TEXT NULLABLE | Notas adicionales |
| `estado_reserva_id` | BIGINT FK | Estado actual de la reserva |

### Estados de reserva (`estado_reservas`)

| Nombre | Efecto en la mesa |
|---|---|
| Pendiente | Mesa → **Reservada** |
| Confirmada | Mesa → **Reservada** |
| Cancelada | Mesa → **Disponible** |
| Completada | Mesa → **Disponible** |
| No asistió | Mesa → **Disponible** |

---

## Sincronización automática de estado de mesa

Esta es la característica más importante del módulo. El sistema mantiene el estado de la mesa siempre coherente con sus reservas activas.

### Método helper en `Admin\ReservaController`

```php
private function estadoMesaParaReserva(string $nombreEstadoReserva): string
{
    return match(strtolower($nombreEstadoReserva)) {
        'confirmada', 'pendiente' => 'Reservada',
        default                   => 'Disponible', // Cancelada, Completada, No asistió
    };
}

private function sincronizarEstadoMesa(int $mesaId, string $nombreEstadoReserva): void
{
    $nombreMesa = $this->estadoMesaParaReserva($nombreEstadoReserva);
    $estadoMesa = EstadoMesa::where('nombre_estado', $nombreMesa)->first();
    if ($estadoMesa) {
        Mesa::where('id', $mesaId)->update(['estado_mesa_id' => $estadoMesa->id]);
    }
}
```

### Cuándo se sincroniza

| Evento | Acción |
|---|---|
| Crear reserva | Sincroniza la mesa según el estado inicial |
| Actualizar reserva | Libera la mesa anterior si cambió; sincroniza la nueva |
| Eliminar reserva | Libera la mesa si no tiene otras reservas activas |

---

## Flujo: crear una reserva

```
POST /admin/reservas (o /mis-reservas)
         │
         ▼
  Reserva::create([...])
         │
         ▼
  ¿estado es Pendiente o Confirmada?
     SÍ → Mesa estado = "Reservada"
     NO → Mesa estado = "Disponible"
```

---

## Flujo: actualizar una reserva

```
PUT /admin/reservas/{reserva}
         │
         ▼
  Guardar mesaAnteriorId = $reserva->mesa_id
         │
         ▼
  $reserva->update([nueva_mesa_id, nuevo_estado, ...])
         │
         ▼
  ¿Cambió de mesa?
     SÍ → liberarMesaAnterior(mesaAnteriorId, nuevaMesaId)
          │
          └─ ¿La mesa anterior tiene otras reservas Pendiente/Confirmada?
               NO → Mesa anterior estado = "Disponible"
               SÍ → No hacer nada (sigue Reservada)
         │
         ▼
  Sincronizar nueva mesa con nuevo estado de reserva
```

---

## Flujo: eliminar una reserva

```
DELETE /admin/reservas/{reserva}
         │
         ▼
  Guardar mesaId = $reserva->mesa_id
         │
         ▼
  $reserva->delete()
         │
         ▼
  ¿La mesa tiene otras reservas Pendiente/Confirmada?
     NO → Mesa estado = "Disponible"
     SÍ → No hacer nada
```

---

## Diferencias entre Admin y Cliente

| Aspecto | Admin (`ReservaController`) | Cliente (`ClienteController`) |
|---|---|---|
| `cliente_id` | Campo del formulario (puede elegir) | Auto-asignado con `Auth::user()->cliente->id` |
| Sincronización | Usa helpers privados reutilizables | Lógica inline equivalente |
| Alcance | Ve todas las reservas | Ve solo sus propias reservas |
| Filtros | Tabs hoy/mañana/semana + fecha + búsqueda | Sin filtros avanzados |

---

## Métricas del panel admin

Calculadas con `whereDate('fecha_reserva', $hoy)`:

| Métrica | Descripción |
|---|---|
| Reservas hoy | Total del día actual |
| Reservas mañana | Total del día siguiente |
| Confirmadas hoy | Con estado = "Confirmada" |
| Pendientes hoy | Con estado = "Pendiente" |
| Canceladas hoy | Con estado = "Cancelada" |

---

## Filtros disponibles (panel admin)

- **Tab "Hoy"**: `whereDate('fecha_reserva', today())`
- **Tab "Mañana"**: `whereDate('fecha_reserva', today() + 1 día)`
- **Tab "Esta semana"**: `whereBetween('fecha_reserva', [startOfWeek, endOfWeek])`
- **Filtro por fecha**: date picker libre
- **Buscador**: por nombre del cliente o teléfono

---

## Mesas disponibles en el seeder (10 mesas)

| # | Capacidad | Ubicación |
|---|---|---|
| 1 | 2 | Ventana |
| 2 | 4 | Ventana |
| 3 | 4 | Centro |
| 4 | 6 | Centro |
| 5 | 4 | Centro |
| 6 | 2 | Lateral |
| 7 | 4 | Lateral |
| 8 | 4 | Terraza |
| 9 | 6 | Terraza |
| 10 | 10 | Salón privado |
