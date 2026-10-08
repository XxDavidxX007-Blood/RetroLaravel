<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;
use App\Models\Mesa;
use App\Models\EstadoMesa;
use App\Models\EstadoReserva;
use App\Models\Cliente;
use Carbon\Carbon;

class ClienteController extends Controller
{
    /**
     * Muestra la página de reservas con el modal de nueva reserva listo.
     */
    public function reservas()
    {
        $usuario = Auth::user();
        $cliente = $usuario->cliente ?? null;

        // Reservas del usuario autenticado (si es cliente)
        $reservas = $cliente
            ? Reserva::where('cliente_id', $cliente->id)
                ->with(['mesa', 'estadoReserva'])
                ->orderByDesc('fecha_reserva')
                ->orderByDesc('hora_reserva')
                ->get()
            : collect();

        // Datos para el modal
        $mesas    = Mesa::with('estadoMesa')->orderBy('numero_mesa')->get();
        $estados  = EstadoReserva::all();
        $clientes = Cliente::with('user')->get();

        return view('cliente.reservas', compact(
            'reservas',
            'mesas',
            'estados',
            'clientes'
        ));
    }

    /**
     * Guarda una nueva reserva y bloquea la mesa reservada.
     */
    public function storeReserva(Request $request)
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

        // Auto-asignar cliente si el usuario autenticado tiene perfil de cliente
        $clienteId = $request->cliente_id ?: (Auth::user()->cliente->id ?? null);

        // Crear la reserva
        Reserva::create([
            'cliente_id'        => $clienteId,
            'mesa_id'           => $request->mesa_id,
            'fecha_reserva'     => $request->fecha_reserva,
            'hora_reserva'      => $request->hora_reserva,
            'cantidad_personas' => $request->cantidad_personas,
            'observaciones'     => $request->observaciones,
            'estado_reserva_id' => $request->estado_reserva_id,
        ]);

        // Marcar la mesa como "Reservada"
        $estadoReservada = EstadoMesa::where('nombre_estado', 'Reservada')->first();
        if ($estadoReservada) {
            Mesa::where('id', $request->mesa_id)
                ->update(['estado_mesa_id' => $estadoReservada->id]);
        }

        return redirect()->route('reservas.cliente')
            ->with('success', 'Reserva creada correctamente.');
    }

    /**
     * Actualiza una reserva existente y sincroniza el estado de la mesa.
     */
    public function updateReserva(Request $request, Reserva $reserva)
    {
        $request->validate([
            'mesa_id'           => 'required|exists:mesas,id',
            'fecha_reserva'     => 'required|date',
            'hora_reserva'      => 'required',
            'cantidad_personas' => 'required|integer|min:1|max:20',
            'estado_reserva_id' => 'required|exists:estado_reservas,id',
        ]);

        $mesaAnteriorId = $reserva->mesa_id;

        $reserva->update([
            'mesa_id'           => $request->mesa_id,
            'fecha_reserva'     => $request->fecha_reserva,
            'hora_reserva'      => $request->hora_reserva,
            'cantidad_personas' => $request->cantidad_personas,
            'estado_reserva_id' => $request->estado_reserva_id,
        ]);

        // Liberar mesa anterior si cambió
        if ($mesaAnteriorId != $request->mesa_id) {
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

        // Sincronizar estado de la mesa seleccionada
        $nombreEstado = $reserva->fresh()->estadoReserva->nombre_estado;
        $nombreMesa   = in_array($nombreEstado, ['Pendiente', 'Confirmada']) ? 'Reservada' : 'Disponible';
        $estadoMesa   = EstadoMesa::where('nombre_estado', $nombreMesa)->first();
        if ($estadoMesa) {
            Mesa::where('id', $request->mesa_id)->update(['estado_mesa_id' => $estadoMesa->id]);
        }

        return redirect()->route('reservas.cliente')
            ->with('success', 'Reserva actualizada correctamente.');
    }

    /**
     * Elimina una reserva y libera la mesa si no tiene otras activas.
     */
    public function destroyReserva(Reserva $reserva)
    {
        $mesaId = $reserva->mesa_id;
        $reserva->delete();

        $reservasActivas = Reserva::where('mesa_id', $mesaId)
            ->whereHas('estadoReserva', fn($q) => $q->whereIn('nombre_estado', ['Pendiente', 'Confirmada']))
            ->count();

        if ($reservasActivas === 0) {
            $disponible = EstadoMesa::where('nombre_estado', 'Disponible')->first();
            if ($disponible) {
                Mesa::where('id', $mesaId)->update(['estado_mesa_id' => $disponible->id]);
            }
        }

        return redirect()->route('reservas.cliente')
            ->with('success', 'Reserva eliminada correctamente.');
    }
}
