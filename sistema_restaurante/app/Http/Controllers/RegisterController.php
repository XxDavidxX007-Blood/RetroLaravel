<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Buscar el rol Cliente
        $rolCliente = Role::where('nombre', 'Cliente')->first();

        if (!$rolCliente) {
            return back()->withErrors([
                'email' => 'No existe el rol Cliente en la base de datos.',
            ])->withInput();
        }

        $usuario = User::create([
            'name' => $datos['name'],
            'apellidos' => $datos['apellidos'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'] ?? null,
            'password' => Hash::make($datos['password']),
            'role_id' => $rolCliente->id,
            'estado' => 'Activo',
        ]);

        \App\Models\Cliente::create([
            'user_id' => $usuario->id,
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Cuenta creada correctamente. Ahora puedes iniciar sesión.');
    }
}