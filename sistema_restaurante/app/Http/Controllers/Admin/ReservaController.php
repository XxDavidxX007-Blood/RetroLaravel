<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\EstadoReserva;
use App\Models\Mesa;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $query = Reserva::with(['cliente.user', 'mesa', 'estadoReserva']);

        // Filtro por pestaña de período
        $tab = $request->input('tab', 'todas');
        if ($tab === 'hoy') {
            $query->whereDate('fecha_reserva', now()->toDateString());
        } elseif ($tab === 'manana') {
            $query->whereDate('fecha_reserva', now()->addDay()->toDateString());
        } elseif ($tab === 'semana') {
            $query->whereBetween('fecha_reserva', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        // Filtro por fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->input('fecha'));
        }

        // Búsqueda por cliente o teléfono
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->whereHas('cliente.user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('telefono', 'like', "%{$s}%");
            });
        }

        $reservas = $query->latest('fecha_reserva')->paginate(7)->withQueryString();
        $estados = EstadoReserva::all();
        $mesas = Mesa::orderBy('numero_mesa')->get();
        $clientes = Cliente::with('user')->get();

        // ===== MÉTRICAS =====
        $hoy = now()->toDateString();
        $manana = now()->addDay()->toDateString();

        $reservasHoy = Reserva::whereDate('fecha_reserva', $hoy)->count();
        $reservasManana = Reserva::whereDate('fecha_reserva', $manana)->count();

        $confirmadas = Reserva::whereDate('fecha_reserva', $hoy)
            ->whereHas('estadoReserva', fn($q) => $q->where('nombre_estado', 'Confirmada'))
            ->count();

        $pendientes = Reserva::whereDate('fecha_reserva', $hoy)
            ->whereHas('estadoReserva', fn($q) => $q->where('nombre_estado', 'Pendiente'))
            ->count();

        $canceladas = Reserva::whereDate('fecha_reserva', $hoy)
            ->whereHas('estadoReserva', fn($q) => $q->where('nombre_estado', 'Cancelada'))
            ->count();

        return view('admin.reservas.index', compact(
            'reservas', 'estados', 'mesas', 'clientes',
            'reservasHoy', 'reservasManana', 'confirmadas', 'pendientes', 'canceladas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'       => 'required|exists:clientes,id',
            'mesa_id'          => 'required|exists:mesas,id',
            'fecha_reserva'    => 'required|date',
            'hora_reserva'     => 'required',
            'cantidad_personas'=> 'required|integer|min:1|max:20',
            'observaciones'    => 'nullable|string|max:500',
            'estado_reserva_id'=> 'required|exists:estado_reservas,id',
        ]);

        Reserva::create($request->only([
            'cliente_id', 'mesa_id', 'fecha_reserva',
            'hora_reserva', 'cantidad_personas', 'observaciones', 'estado_reserva_id',
        ]));

        return redirect()->route('admin.reservas.index')
            ->with('success', 'Reserva creada correctamente.');
    }

    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'cliente_id'       => 'required|exists:clientes,id',
            'mesa_id'          => 'required|exists:mesas,id',
            'fecha_reserva'    => 'required|date',
            'hora_reserva'     => 'required',
            'cantidad_personas'=> 'required|integer|min:1|max:20',
            'observaciones'    => 'nullable|string|max:500',
            'estado_reserva_id'=> 'required|exists:estado_reservas,id',
        ]);

        $reserva->update($request->only([
            'cliente_id', 'mesa_id', 'fecha_reserva',
            'hora_reserva', 'cantidad_personas', 'observaciones', 'estado_reserva_id',
        ]));

        return redirect()->route('admin.reservas.index')
            ->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        $reserva->delete();
        return redirect()->route('admin.reservas.index')
            ->with('success', 'Reserva eliminada correctamente.');
    }
}
