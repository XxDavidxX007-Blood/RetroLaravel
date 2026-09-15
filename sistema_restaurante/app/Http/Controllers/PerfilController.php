<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('role');

        $layout = ($user->role && $user->role->nombre === 'Administrador')
            ? 'layouts.admin'
            : 'layouts.app';

        return view('perfil.index', compact('user', 'layout'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->input('name'),
            'apellidos' => $request->input('apellidos', ''),
            'telefono' => $request->input('telefono'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('perfiles', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
