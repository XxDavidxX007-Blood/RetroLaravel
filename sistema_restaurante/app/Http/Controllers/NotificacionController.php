<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Vista completa de todas las notificaciones
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = Notificacion::where('user_id', $userId);

        if ($request->input('filtro') === 'no_leidas') {
            $query->where('leida', false);
        } elseif ($request->input('filtro') === 'leidas') {
            $query->where('leida', true);
        }

        $notificaciones = $query->latest()->paginate(10)->withQueryString();
        $totalNoLeidas = Notificacion::where('user_id', $userId)->where('leida', false)->count();
        $total = Notificacion::where('user_id', $userId)->count();

        $view = auth()->user()->role && auth()->user()->role->nombre === 'Administrador'
            ? 'admin.notificaciones.index'
            : 'cliente.notificaciones';

        return view($view, compact('notificaciones', 'totalNoLeidas', 'total'));
    }

    /**
     * Obtener las últimas notificaciones en formato JSON para el dropdown
     */
    public function getLatest()
    {
        $userId = auth()->id();
        $notificaciones = Notificacion::where('user_id', $userId)
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'tipo' => $n->tipo,
                    'titulo' => $n->titulo,
                    'mensaje' => $n->mensaje,
                    'leida' => (bool)$n->leida,
                    'fecha' => $n->created_at ? $n->created_at->diffForHumans() : '',
                    'url' => $this->getUrlPorTipo($n),
                ];
            });

        $totalNoLeidas = Notificacion::where('user_id', $userId)->where('leida', false)->count();

        return response()->json([
            'notificaciones' => $notificaciones,
            'totalNoLeidas' => $totalNoLeidas,
        ]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::where('user_id', auth()->id())->findOrFail($id);
        $notificacion->update(['leida' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas las notificaciones del usuario como leídas
     */
    public function marcarTodasLeidas()
    {
        Notificacion::where('user_id', auth()->id())
            ->where('leida', false)
            ->update(['leida' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Eliminar una notificación
     */
    public function destroy($id)
    {
        $notificacion = Notificacion::where('user_id', auth()->id())->findOrFail($id);
        $notificacion->delete();

        return back()->with('success', 'Notificación eliminada.');
    }

    /**
     * Resolver URL según el tipo de notificación
     */
    private function getUrlPorTipo($n)
    {
        $tipo = strtolower($n->tipo);
        $isAdmin = auth()->user()->role && auth()->user()->role->nombre === 'Administrador';

        if (str_contains($tipo, 'domicilio')) {
            return $isAdmin ? route('admin.domicilios.index') : route('domicilios.cliente');
        } elseif (str_contains($tipo, 'pedido')) {
            return $isAdmin ? route('admin.pedidos.index') : route('pedidos.cliente');
        } elseif (str_contains($tipo, 'reserva')) {
            return $isAdmin ? route('admin.reservas.index') : route('reservas.cliente');
        } elseif (str_contains($tipo, 'inventario') || str_contains($tipo, 'stock')) {
            return $isAdmin ? route('admin.inventario.index') : '#';
        }

        return '#';
    }
}
