<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

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
                return view('cliente.dashboard');

            default:
                abort(403, 'Rol no autorizado');
        }
    }
}