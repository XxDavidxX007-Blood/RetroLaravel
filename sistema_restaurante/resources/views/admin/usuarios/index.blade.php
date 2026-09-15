@extends('layouts.admin')

@section('title', 'Gestión de Usuarios | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="w-7 h-7 rounded-full bg-retro-gold/20 text-retro-gold flex items-center justify-center hover:bg-retro-gold hover:text-black transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <span class="text-gray-400">|</span>
        <span>GESTION DE USUARIOS</span>
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-8">

    <!-- ALERTAS / NOTIFICACIONES -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-circle-check text-emerald-600 text-lg"></i>
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-circle-exclamation text-rose-600 text-lg"></i>
                <p class="text-sm font-medium text-rose-800">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl space-y-1 shadow-sm">
            <div class="flex items-center gap-2 text-amber-800 font-semibold text-sm">
                <i class="fas fa-triangle-exclamation"></i>
                <span>Por favor verifica los siguientes errores:</span>
            </div>
            <ul class="list-disc list-inside text-xs text-amber-700 pl-4 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ================================================= -->
    <!-- METRICAS SUPERIORES -->
    <!-- ================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Card Total Usuarios -->
        <div class="bg-white border-2 border-black rounded-2xl p-6 flex justify-between items-center shadow-sm hover:shadow-md transition">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Usuarios</p>
                <h3 class="text-3xl font-extrabold text-gray-900 mt-2 font-heading">{{ $totalUsuarios }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-800 text-2xl">
                <i class="fas fa-user-group"></i>
            </div>
        </div>

        <!-- Card Ventas Hoy -->
        <div class="bg-white border-2 border-black rounded-2xl p-6 flex justify-between items-center shadow-sm hover:shadow-md transition">
            <div>
                <p class="text-sm font-medium text-gray-500">Ventas Hoy</p>
                <h3 class="text-3xl font-extrabold text-gray-900 mt-2 font-heading">{{ $ventasHoy }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-[#fef3c7] flex items-center justify-center text-[#d97706] text-2xl font-bold">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>

        <!-- Card Órdenes Pendientes -->
        <div class="bg-white border-2 border-black rounded-2xl p-6 flex justify-between items-center shadow-sm hover:shadow-md transition">
            <div>
                <p class="text-sm font-medium text-gray-500">Órdenes Pendientes</p>
                <h3 class="text-3xl font-extrabold text-gray-900 mt-2 font-heading">{{ $ordenesPendientes }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-[#dbeafe] flex items-center justify-center text-[#2563eb] text-xl">
                <i class="fas fa-bell"></i>
            </div>
        </div>

    </div>


    <!-- ================================================= -->
    <!-- TABLA PRINCIPAL DE GESTIÓN DE USUARIOS -->
    <!-- ================================================= -->
    <div class="bg-white border-2 border-black rounded-3xl p-7 shadow-xl space-y-6">

        <!-- CABECERA DE LA SECCIÓN Y BOTÓN NUEVO -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-user-gear text-2xl text-black"></i>
                <h2 class="font-heading text-2xl font-bold tracking-tight text-gray-900 uppercase">
                    GESTIÓN DE USUARIOS
                </h2>
            </div>

            <button 
                onclick="openCreateModal()"
                class="bg-[#0a0a0a] text-white hover:bg-black font-semibold px-6 py-2.5 rounded-full flex items-center gap-2 shadow transition border border-black hover:border-retro-gold group"
            >
                <i class="fas fa-plus text-xs text-retro-gold group-hover:scale-125 transition"></i>
                <span class="text-sm tracking-wider uppercase">NUEVO USUARIO</span>
            </button>
        </div>

        <!-- BUSCADOR Y FILTROS -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-2">
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="w-full flex flex-col md:flex-row items-center gap-3">
                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Buscar por nombre, email o teléfono..." 
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-black"
                    >
                </div>

                <select name="role_id" onchange="this.form.submit()" class="w-full md:w-48 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="">Todos los Roles</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->nombre }}
                        </option>
                    @endforeach
                </select>

                <select name="estado" onchange="this.form.submit()" class="w-full md:w-40 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="">Todos los Estados</option>
                    <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ request('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>

                @if(request()->hasAny(['search', 'role_id', 'estado']))
                    <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- TABLA ESTILIZADA -->
        <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#0a0a0a] text-retro-gold font-heading text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 font-semibold">Usuario</th>
                        <th class="py-4 px-6 font-semibold">Email</th>
                        <th class="py-4 px-6 font-semibold">Teléfono</th>
                        <th class="py-4 px-6 font-semibold">Rol</th>
                        <th class="py-4 px-6 font-semibold">Estado</th>
                        <th class="py-4 px-6 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-sm">
                    @forelse($usuarios as $user)
                        <tr class="hover:bg-gray-50/80 transition">
                            <!-- Usuario -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 shrink-0">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900 capitalize">
                                        {{ strtolower($user->name) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-4 px-6 text-gray-600">
                                {{ $user->email }}
                            </td>

                            <!-- Teléfono -->
                            <td class="py-4 px-6 text-gray-600 font-mono text-xs">
                                {{ $user->telefono ?? 'N/A' }}
                            </td>

                            <!-- Rol -->
                            <td class="py-4 px-6">
                                @php
                                    $rolNombre = strtolower($user->role->nombre ?? 'cliente');
                                @endphp

                                @if($rolNombre === 'administrador' || $rolNombre === 'admin')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#ffe4e6] text-[#e11d48]">
                                        Admin
                                    </span>
                                @elseif($rolNombre === 'empleado')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#dbeafe] text-[#2563eb]">
                                        Empleado
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#16a34a]">
                                        Cliente
                                    </span>
                                @endif
                            </td>

                            <!-- Estado -->
                            <td class="py-4 px-6">
                                @if(($user->estado ?? 'Activo') === 'Activo')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#16a34a]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Botón Editar -->
                                    <button 
                                        type="button"
                                        onclick='openEditModal(@json($user))'
                                        class="w-8 h-8 rounded-full bg-[#eff6ff] text-[#3b82f6] hover:bg-[#dbeafe] flex items-center justify-center transition shadow-sm"
                                        title="Editar usuario"
                                    >
                                        <i class="fas fa-pen text-xs"></i>
                                    </button>

                                    <!-- Botón Cambiar Estado (Desactivar/Activar) -->
                                    <form method="POST" action="{{ route('admin.usuarios.toggle-status', $user->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit"
                                            class="w-8 h-8 rounded-full bg-[#fefce8] text-[#eab308] hover:bg-[#fef9c3] flex items-center justify-center transition shadow-sm"
                                            title="{{ ($user->estado ?? 'Activo') === 'Activo' ? 'Desactivar usuario' : 'Activar usuario' }}"
                                        >
                                            <i class="fas fa-ban text-xs"></i>
                                        </button>
                                    </form>

                                    <!-- Botón Eliminar -->
                                    <form method="POST" action="{{ route('admin.usuarios.destroy', $user->id) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar a este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit"
                                            class="w-8 h-8 rounded-full bg-[#fef2f2] text-[#ef4444] hover:bg-[#fee2e2] flex items-center justify-center transition shadow-sm"
                                            title="Eliminar usuario"
                                        >
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                <i class="fas fa-users-slash text-3xl mb-2 text-gray-300 block"></i>
                                No se encontraron usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        @if($usuarios->hasPages())
            <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-500 font-medium">
                    Mostrando <span class="font-bold text-gray-900">{{ $usuarios->firstItem() }}</span> a <span class="font-bold text-gray-900">{{ $usuarios->lastItem() }}</span> de <span class="font-bold text-gray-900">{{ $usuarios->total() }}</span> usuarios
                </p>
                <div class="pagination-custom">
                    {{ $usuarios->links() }}
                </div>
            </div>
        @endif

    </div>

</div>


<!-- ================================================= -->
<!-- MODAL: CREAR NUEVO USUARIO -->
<!-- ================================================= -->
<div id="createModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-lg w-full p-7 shadow-2xl space-y-6 relative animate-fade-in">
        
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-user-plus text-sm"></i>
                </div>
                <h3 class="font-heading text-xl font-bold uppercase tracking-wider text-gray-900">Nuevo Usuario</h3>
            </div>
            <button onclick="closeCreateModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600">Nombre Completo</label>
                <input type="text" name="name" required placeholder="Ej. Alicia Robles" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Email</label>
                    <input type="email" name="email" required placeholder="alicia@gmail.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Teléfono</label>
                    <input type="text" name="telefono" placeholder="3204417080" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Rol</label>
                    <select name="role_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="">Seleccionar rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Estado</label>
                    <select name="estado" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="Activo" selected>Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600">Contraseña</label>
                <input type="password" name="password" required minlength="6" placeholder="******" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#0a0a0a] text-white hover:bg-black border border-black hover:border-retro-gold transition shadow">
                    Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ================================================= -->
<!-- MODAL: EDITAR USUARIO -->
<!-- ================================================= -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-lg w-full p-7 shadow-2xl space-y-6 relative animate-fade-in">
        
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-user-pen text-sm"></i>
                </div>
                <h3 class="font-heading text-xl font-bold uppercase tracking-wider text-gray-900">Editar Usuario</h3>
            </div>
            <button onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600">Nombre Completo</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Email</label>
                    <input type="email" id="edit_email" name="email" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Teléfono</label>
                    <input type="text" id="edit_telefono" name="telefono" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Rol</label>
                    <select id="edit_role_id" name="role_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Estado</label>
                    <select id="edit_estado" name="estado" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600">Nueva Contraseña <span class="normal-case text-gray-400 font-normal">(Opcional)</span></label>
                <input type="password" name="password" minlength="6" placeholder="Dejar en blanco para no cambiar" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#0a0a0a] text-white hover:bg-black border border-black hover:border-retro-gold transition shadow">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(user) {
        const form = document.getElementById('editForm');
        form.action = `/admin/usuarios/${user.id}`;
        
        document.getElementById('edit_name').value = user.name || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_telefono').value = user.telefono || '';
        document.getElementById('edit_role_id').value = user.role_id || '';
        document.getElementById('edit_estado').value = user.estado || 'Activo';

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
