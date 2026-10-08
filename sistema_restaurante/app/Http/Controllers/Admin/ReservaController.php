<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\EstadoReserva;
use App\Models\EstadoMesa;
use App\Models\Mesa;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // -------------------------------------------------------
    // Helpers para sincronizar el estado de la mesa
    // -------------------------------------------------------

    /**
     * Dado el nombre del estado de reserva, devuelve el nombre
     * que debe tener el estado de la mesa.
     */
    private function estadoMesaParaReserva(string $nombreEstadoReserva): string
    {
        return match(strtolower($nombreEstadoReserva)) {
            'confirmada', 'pendiente' => 'Reservada',
            default                   => 'Disponible', // Cancelada, Completada, No asistió
        };
    }

    /**
     * Actualiza estado_mesa_id de la mesa según el estado de la reserva.
     */
    private function sincronizarEstadoMesa(int $mesaId, string $nombreEstadoReserva): void
    {
        $nombreMesa = $this->estadoMesaParaReserva($nombreEstadoReserva);
        $estadoMesa = EstadoMesa::where('nombre_estado', $nombreMesa)->first();
        if ($estadoMesa) {
            Mesa::where('id', $mesaId)->update(['estado_mesa_id' => $estadoMesa->id]);
        }
    }

    /**
     * Si cambió de mesa, libera la mesa anterior.
     */
    private function liberarMesaAnterior(int $mesaAnteriorId, int $mesaNuevaId): void
    {
        if ($mesaAnteriorId === $mesaNuevaId) return;

        // Verificar que la mesa anterior no tenga otras reservas activas
        $reservasActivas = Reserva::where('mesa_id', $mesaAnteriorId)
            ->whereHas('estadoReserva', fn($q) => $q->whereIn('nombre_estado', ['Pendiente', 'Confirmada']))
            ->count();

        if ($reservasActivas === 0) {
            $disponible = EstadoMesa::where('nombre_estado', 'Disponible')->first();
            if ($disponible) {
                Mesa::where('id', $mesaAnteriorId)->update(['estado_mesa_id' => $disponible->id]);
            }
        }
    }

    // -------------------------------------------------------
    // CRUD
    // -------------------------------------------------------

    public function index(Request $request)
    {
        $query = Reserva::with(['cliente.user', 'mesa', 'estadoReserva']);

        $tab = $request->input('tab', 'todas');
        if ($tab === 'hoy') {
            $query->whereDate('fecha_reserva', now()->toDateString());
        } elseif ($tab === 'manana') {
            $query->whereDate('fecha_reserva', now()->addDay()->toDateString());
        } elseif ($tab === 'semana') {
            $query->whereBetween('fecha_reserva', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->input('fecha'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->whereHas('cliente.user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('telefono', 'like', "%{$s}%");
            });
        }

        $reservas  = $query->latest('fecha_reserva')->paginate(7)->withQueryString();
        $estados   = EstadoReserva::all();
        $mesas     = Mesa::with('estadoMesa')->orderBy('numero_mesa')->get();
        $clientes  = Cliente::with('user')->get();

        $hoy    = now()->toDateString();
        $manana = now()->addDay()->toDateString();

        $reservasHoy    = Reserva::whereDate('fecha_reserva', $hoy)->count();
        $reservasManana = Reserva::whereDate('fecha_reserva', $manana)->count();

        $confirmadas = Reserva::whereDate('fecha_reserva', $hoy)
            ->whereHas('estadoReserva', fn($q) => $q->where('nombre_estado', 'Confirmada'))->count();

        $pendientes = Reserva::whereDate('fecha_reserva', $hoy)
            ->whereHas('estadoReserva', fn($q) => $q->where('nombre_estado', 'Pendiente'))->count();

        $canceladas = Reserva::whereDate('fecha_reserva', $hoy)
            ->whereHas('estadoReserva', fn($q) => $q->where('nombre_estado', 'Cancelada'))->count();

        return view('admin.reservas.index', compact(
            'reservas', 'estados', 'mesas', 'clientes',
            'reservasHoy', 'reservasManana', 'confirmadas', 'pendientes', 'canceladas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'        => 'nullable|exists:clientes,id',
            'mesa_id'           => 'required|exists:mesas,id',
            'fecha_reserva'     => 'required|date',
            'hora_reserva'      => 'required',
            'cantidad_personas' => 'required|integer|min:1|max:20',
            'observaciones'     => 'nullable|string|max:500',
            'estado_reserva_id' => 'required|exists:estado_reservas,id',
        ]);

        $reserva = Reserva::create($request->only([
            'cliente_id', 'mesa_id', 'fecha_reserva',
            'hora_reserva', 'cantidad_personas', 'observaciones', 'estado_reserva_id',
        ]));

        // Sincronizar estado de la mesa
        $this->sincronizarEstadoMesa(
            $request->mesa_id,
            $reserva->estadoReserva->nombre_estado
        );

        return redirect()->route('admin.reservas.index')
            ->with('success', 'Reserva creada correctamente.');
    }

    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'cliente_id'        => 'nullable|exists:clientes,id',
            'mesa_id'           => 'required|exists:mesas,id',
            'fecha_reserva'     => 'required|date',
            'hora_reserva'      => 'required',
            'cantidad_personas' => 'required|integer|min:1|max:20',
            'observaciones'     => 'nullable|string|max:500',
            'estado_reserva_id' => 'required|exists:estado_reservas,id',
        ]);

        $mesaAnteriorId = $reserva->mesa_id;

        $reserva->update($request->only([
            'cliente_id', 'mesa_id', 'fecha_reserva',
            'hora_reserva', 'cantidad_personas', 'observaciones', 'estado_reserva_id',
        ]));

        // Si cambió de mesa, liberar la anterior
        $this->liberarMesaAnterior($mesaAnteriorId, (int) $request->mesa_id);

        // Sincronizar estado de la nueva mesa
        $this->sincronizarEstadoMesa(
            (int) $request->mesa_id,
            $reserva->fresh()->estadoReserva->nombre_estado
        );

        return redirect()->route('admin.reservas.index')
            ->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        $mesaId = $reserva->mesa_id;
        $reserva->delete();

        // Liberar mesa si no tiene otras reservas activas
        $reservasActivas = Reserva::where('mesa_id', $mesaId)
            ->whereHas('estadoReserva', fn($q) => $q->whereIn('nombre_estado', ['Pendiente', 'Confirmada']))
            ->count();

        if ($reservasActivas === 0) {
            $disponible = EstadoMesa::where('nombre_estado', 'Disponible')->first();
            if ($disponible) {
                Mesa::where('id', $mesaId)->update(['estado_mesa_id' => $disponible->id]);
            }
        }

        return redirect()->route('admin.reservas.index')
            ->with('success', 'Reserva eliminada correctamente.');
    }
}
