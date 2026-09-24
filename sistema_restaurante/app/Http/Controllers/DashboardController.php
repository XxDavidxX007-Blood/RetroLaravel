<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;
use App\Models\Pedido;
use App\Models\Producto;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        switch ($usuario->role_id) {

            case 1:
                return view('admin.dashboard');

            case 2:
                return view('empleado.dashboard');

            case 3:
                return $this->clienteDashboard($usuario);

            default:
                abort(403, 'Rol no autorizado');
        }
    }

    private function clienteDashboard($usuario)
    {
        $cliente = $usuario->cliente;

        // Reservas próximas (futuras o de hoy)
        $reservasProximas = $cliente
            ? Reserva::where('cliente_id', $cliente->id)
                ->whereDate('fecha_reserva', '>=', Carbon::today())
                ->with(['mesa', 'estadoReserva'])
                ->orderBy('fecha_reserva')
                ->orderBy('hora_reserva')
                ->take(5)
                ->get()
            : collect();

        $totalReservas = $reservasProximas->count();

        // Pedidos del cliente
        $totalPedidos = $cliente
            ? Pedido::where('cliente_id', $cliente->id)->count()
            : 0;

        // Domicilios del cliente (tipo_pedido_id = 2 suele ser domicilio)
        $totalDomicilios = $cliente
            ? Pedido::where('cliente_id', $cliente->id)
                ->where('tipo_pedido_id', 2)
                ->count()
            : 0;

        // Total de productos activos en el menú
        $totalProductos = Producto::where('estado', true)->count();

        return view('cliente.dashboard', compact(
            'reservasProximas',
            'totalReservas',
            'totalPedidos',
            'totalDomicilios',
            'totalProductos'
        ));
    }
}