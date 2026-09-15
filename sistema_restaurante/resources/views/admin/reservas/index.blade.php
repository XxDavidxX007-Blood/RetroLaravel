@extends('layouts.admin')

@section('title', 'Gestión de Reservas | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="w-7 h-7 rounded-full bg-retro-gold/20 text-retro-gold flex items-center justify-center hover:bg-retro-gold hover:text-black transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <span class="text-gray-400">|</span>
        <span>GESTIÓN DE RESERVAS</span>
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-7">

    <!-- CABECERA -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <i class="fas fa-calendar-check text-3xl text-gray-900"></i>
                <h1 class="font-heading text-3xl font-bold text-gray-900 tracking-tight">
                    Gestión de Reservas
                </h1>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Administra y supervisa todas las reservas del restaurante.
            </p>
        </div>
        <button
            onclick="openNuevaReservaModal()"
            class="inline-flex items-center gap-2 bg-[#0a0a0a] text-white font-semibold px-5 py-2.5 rounded-xl hover:bg-black border border-black hover:border-retro-gold transition shadow text-sm whitespace-nowrap"
        >
            <i class="fas fa-plus text-xs text-retro-gold"></i>
            Nueva Reserva
        </button>
    </div>

    <!-- ALERTAS -->
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

    <!-- ================================================= -->
    <!-- MÉTRICAS (5 CARDS) -->
    <!-- ================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        <!-- Reservas hoy -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#ede9fe] flex items-center justify-center text-[#7c3aed] text-xl shrink-0">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Reservas hoy</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $reservasHoy }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">+0% vs ayer</p>
            </div>
        </div>

        <!-- Reservas mañana -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#fef3c7] flex items-center justify-center text-[#d97706] text-xl shrink-0">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Reservas mañana</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $reservasManana }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">+0% vs hoy</p>
            </div>
        </div>

        <!-- Confirmadas -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#dcfce7] flex items-center justify-center text-[#16a34a] text-xl shrink-0">
                <i class="fas fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Confirmadas</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $confirmadas }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Hoy</p>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#fef3c7] flex items-center justify-center text-[#d97706] text-xl shrink-0">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Pendientes</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $pendientes }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Hoy</p>
            </div>
        </div>

        <!-- Canceladas -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#ffe4e6] flex items-center justify-center text-[#e11d48] text-xl shrink-0">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Canceladas</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $canceladas }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Hoy</p>
            </div>
        </div>

    </div>

    <!-- ================================================= -->
    <!-- TABS + FILTRO FECHA -->
    <!-- ================================================= -->
    @php $currentTab = request('tab', 'todas'); @endphp
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 flex flex-col md:flex-row items-center justify-between gap-4">

        <!-- Pestañas -->
        <div class="flex items-center gap-1 sm:gap-4 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 text-sm font-medium border-b md:border-b-0 border-gray-100">
            @foreach (['todas' => 'Todas', 'hoy' => 'Hoy', 'manana' => 'Mañana', 'semana' => 'Esta semana'] as $key => $label)
                <a
                    href="{{ route('admin.reservas.index', array_merge(request()->except(['tab', 'page']), ['tab' => $key])) }}"
                    class="px-3 py-1.5 transition whitespace-nowrap {{ $currentTab === $key ? 'text-black font-bold border-b-2 border-black -mb-[2px]' : 'text-gray-500 hover:text-gray-900' }}"
                >{{ $label }}</a>
            @endforeach
        </div>

        <!-- Filtro fecha -->
        <form method="GET" action="{{ route('admin.reservas.index') }}" class="flex items-center gap-2">
            @if(request('tab'))
                <input type="hidden" name="tab" value="{{ request('tab') }}">
            @endif
            <input
                type="date"
                name="fecha"
                value="{{ request('fecha') }}"
                onchange="this.form.submit()"
                class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-black text-gray-700 cursor-pointer"
            >
            @if(request('fecha'))
                <a href="{{ route('admin.reservas.index') }}" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition" title="Limpiar">
                    <i class="fas fa-filter-circle-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- ================================================= -->
    <!-- TABLA DE RESERVAS -->
    <!-- ================================================= -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-600 font-heading text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="py-4 px-5 font-semibold">HORA</th>
                        <th class="py-4 px-5 font-semibold">CLIENTE</th>
                        <th class="py-4 px-5 font-semibold">CONTACTO</th>
                        <th class="py-4 px-5 font-semibold">PERSONAS</th>
                        <th class="py-4 px-5 font-semibold">MESA</th>
                        <th class="py-4 px-5 font-semibold">FECHA</th>
                        <th class="py-4 px-5 font-semibold">ESTADO</th>
                        <th class="py-4 px-5 font-semibold text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-sm">
                    @forelse($reservas as $r)
                        @php
                            $estado = strtolower($r->estadoReserva->nombre_estado ?? 'pendiente');
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <!-- Hora -->
                            <td class="py-4 px-5 font-bold text-gray-900 font-mono text-sm whitespace-nowrap">
                                {{ $r->hora_reserva ? \Carbon\Carbon::parse($r->hora_reserva)->format('H:i') : '—' }}
                            </td>

                            <!-- Cliente -->
                            <td class="py-4 px-5">
                                <span class="font-bold text-gray-900 block capitalize">
                                    {{ strtolower($r->cliente->user->name ?? 'Cliente') }}
                                </span>
                            </td>

                            <!-- Contacto -->
                            <td class="py-4 px-5 text-gray-500 text-xs font-mono">
                                {{ $r->cliente->user->telefono ?? '—' }}
                            </td>

                            <!-- Personas -->
                            <td class="py-4 px-5 font-semibold text-gray-800">
                                {{ $r->cantidad_personas }}
                            </td>

                            <!-- Mesa -->
                            <td class="py-4 px-5 text-gray-700">
                                <span class="font-semibold">Mesa {{ $r->mesa->numero_mesa ?? '—' }}</span>
                                @if($r->mesa?->ubicacion)
                                    <span class="block text-xs text-gray-400">{{ $r->mesa->ubicacion }}</span>
                                @endif
                            </td>

                            <!-- Fecha -->
                            <td class="py-4 px-5 text-xs font-mono text-gray-600 whitespace-nowrap">
                                {{ $r->fecha_reserva ? \Carbon\Carbon::parse($r->fecha_reserva)->format('d/m/Y') : '—' }}
                            </td>

                            <!-- Estado -->
                            <td class="py-4 px-5">
                                @if(Str::contains($estado, 'confirmada'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#16a34a]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span> Confirmada
                                    </span>
                                @elseif(Str::contains($estado, 'pendiente'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span> Pendiente
                                    </span>
                                @elseif(Str::contains($estado, 'cancelada'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#ffe4e6] text-[#e11d48]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#e11d48]"></span> Cancelada
                                    </span>
                                @elseif(Str::contains($estado, 'completada'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#dbeafe] text-[#2563eb]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span> Completada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> {{ $r->estadoReserva->nombre_estado ?? 'N/A' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Editar -->
                                    <button
                                        type="button"
                                        onclick="openEditModal({{ json_encode($r) }})"
                                        class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition shadow-xs"
                                        title="Editar reserva"
                                    >
                                        <i class="fas fa-pen text-xs"></i>
                                    </button>

                                    <!-- Eliminar -->
                                    <form method="POST" action="{{ route('admin.reservas.destroy', $r->id) }}" onsubmit="return confirm('¿Eliminar esta reserva?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition shadow-xs" title="Eliminar reserva">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center text-gray-400">
                                <i class="fas fa-calendar-xmark text-5xl mb-3 text-gray-200 block"></i>
                                <p class="font-semibold text-gray-500">Sin reservas</p>
                                <p class="text-xs mt-1">No hay reservas con los filtros seleccionados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PIE DE TABLA Y PAGINACIÓN -->
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-500 font-medium">
                Mostrando <span class="font-bold text-gray-900">{{ $reservas->firstItem() ?? 0 }}</span> a <span class="font-bold text-gray-900">{{ $reservas->lastItem() ?? 0 }}</span> de <span class="font-bold text-gray-900">{{ $reservas->total() }}</span> reservas
            </p>
            @if($reservas->hasPages())
                <div class="pagination-custom">
                    {{ $reservas->links() }}
                </div>
            @endif
        </div>
    </div>

</div>


<!-- ================================================= -->
<!-- MODAL: NUEVA RESERVA -->
<!-- ================================================= -->
<div id="nuevaReservaModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-lg w-full p-7 shadow-2xl space-y-5 relative animate-fade-in max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-calendar-plus text-sm"></i>
                </div>
                <h3 class="font-heading text-xl font-bold uppercase tracking-wider text-gray-900">Nueva Reserva</h3>
            </div>
            <button onclick="closeNuevaReservaModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.reservas.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Cliente -->
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-600">Cliente *</label>
                    <select name="cliente_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="">Seleccionar cliente...</option>
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}">{{ $c->user->name ?? 'Sin nombre' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Mesa -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Mesa *</label>
                    <select name="mesa_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="">Seleccionar mesa...</option>
                        @foreach($mesas as $m)
                            <option value="{{ $m->id }}">Mesa {{ $m->numero_mesa }} ({{ $m->capacidad }} pers.) — {{ $m->ubicacion }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Cantidad de personas -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Cantidad de Personas *</label>
                    <input type="number" name="cantidad_personas" min="1" max="20" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black" placeholder="Ej: 4">
                </div>

                <!-- Fecha -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Fecha de Reserva *</label>
                    <input type="date" name="fecha_reserva" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <!-- Hora -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Hora *</label>
                    <input type="time" name="hora_reserva" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <!-- Estado -->
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-600">Estado *</label>
                    <select name="estado_reserva_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        @foreach($estados as $e)
                            <option value="{{ $e->id }}" {{ $e->nombre_estado === 'Pendiente' ? 'selected' : '' }}>{{ $e->nombre_estado }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Observaciones -->
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-600">Observaciones</label>
                    <textarea name="observaciones" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black resize-none" placeholder="Notas adicionales (opcional)..."></textarea>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeNuevaReservaModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#0a0a0a] text-white hover:bg-black border border-black hover:border-retro-gold transition shadow">
                    Crear Reserva
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ================================================= -->
<!-- MODAL: EDITAR RESERVA -->
<!-- ================================================= -->
<div id="editReservaModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-lg w-full p-7 shadow-2xl space-y-5 relative animate-fade-in max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-pen text-sm"></i>
                </div>
                <h3 class="font-heading text-xl font-bold uppercase tracking-wider text-gray-900">Editar Reserva</h3>
            </div>
            <button onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form id="editReservaForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Cliente -->
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-600">Cliente *</label>
                    <select id="edit_cliente_id" name="cliente_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}">{{ $c->user->name ?? 'Sin nombre' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Mesa -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Mesa *</label>
                    <select id="edit_mesa_id" name="mesa_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        @foreach($mesas as $m)
                            <option value="{{ $m->id }}">Mesa {{ $m->numero_mesa }} ({{ $m->capacidad }} pers.) — {{ $m->ubicacion }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Cantidad personas -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Personas *</label>
                    <input id="edit_cantidad_personas" type="number" name="cantidad_personas" min="1" max="20" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <!-- Fecha -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Fecha *</label>
                    <input id="edit_fecha_reserva" type="date" name="fecha_reserva" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <!-- Hora -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Hora *</label>
                    <input id="edit_hora_reserva" type="time" name="hora_reserva" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <!-- Estado -->
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-600">Estado *</label>
                    <select id="edit_estado_reserva_id" name="estado_reserva_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        @foreach($estados as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre_estado }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Observaciones -->
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-600">Observaciones</label>
                    <textarea id="edit_observaciones" name="observaciones" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black resize-none"></textarea>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#0a0a0a] text-white hover:bg-black border border-black hover:border-retro-gold transition shadow">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ---- Modal Nueva Reserva ----
    function openNuevaReservaModal() {
        document.getElementById('nuevaReservaModal').classList.remove('hidden');
    }
    function closeNuevaReservaModal() {
        document.getElementById('nuevaReservaModal').classList.add('hidden');
    }

    // ---- Modal Editar Reserva ----
    function openEditModal(reserva) {
        const form = document.getElementById('editReservaForm');
        form.action = `/admin/reservas/${reserva.id}`;

        document.getElementById('edit_cliente_id').value = reserva.cliente_id;
        document.getElementById('edit_mesa_id').value = reserva.mesa_id;
        document.getElementById('edit_cantidad_personas').value = reserva.cantidad_personas;
        document.getElementById('edit_estado_reserva_id').value = reserva.estado_reserva_id;
        document.getElementById('edit_observaciones').value = reserva.observaciones ?? '';

        // fecha_reserva viene como "YYYY-MM-DD" del cast
        if (reserva.fecha_reserva) {
            document.getElementById('edit_fecha_reserva').value = reserva.fecha_reserva.substring(0, 10);
        }

        // hora_reserva viene como "YYYY-MM-DD HH:mm:ss" por el cast datetime
        if (reserva.hora_reserva) {
            // Extraer HH:mm de la cadena
            const hora = reserva.hora_reserva.includes('T')
                ? reserva.hora_reserva.split('T')[1].substring(0, 5)
                : reserva.hora_reserva.substring(11, 16);
            document.getElementById('edit_hora_reserva').value = hora;
        }

        document.getElementById('editReservaModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editReservaModal').classList.add('hidden');
    }
</script>
@endsection
