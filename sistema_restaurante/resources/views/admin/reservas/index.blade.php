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
<div id="nuevaReservaModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden" style="max-height:92vh;">

        {{-- CABECERA --}}
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <i class="fas fa-plus text-gray-800 text-base"></i>
                <h3 class="text-lg font-bold text-gray-900">Nueva Reserva</h3>
            </div>
            <button onclick="closeNuevaReservaModal()" class="text-gray-400 hover:text-gray-700 transition text-xl leading-none">&times;</button>
        </div>

        {{-- FORMULARIO --}}
        <form method="POST" action="{{ route('admin.reservas.store') }}" class="px-6 py-5 space-y-4 overflow-y-auto" style="max-height:calc(92vh - 60px);">
            @csrf

            {{-- Cliente --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cliente</label>
                <select
                    name="cliente_id"
                    class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                >
                    <option value="">Sin cliente asignado</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}">{{ $c->user->name ?? 'Sin nombre' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fecha + Hora --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fecha</label>
                    <input
                        type="date"
                        name="fecha_reserva"
                        required
                        value="{{ now()->toDateString() }}"
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Hora</label>
                    <input
                        type="time"
                        name="hora_reserva"
                        required
                        value="12:00"
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                </div>
            </div>

            {{-- Personas + Estado --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Personas</label>
                    <input
                        type="number"
                        name="cantidad_personas"
                        min="1"
                        max="20"
                        required
                        value="2"
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Estado</label>
                    <select
                        name="estado_reserva_id"
                        required
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                        @foreach($estados as $e)
                            <option value="{{ $e->id }}" {{ $e->nombre_estado === 'Pendiente' ? 'selected' : '' }}>
                                {{ $e->nombre_estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Mesa --}}
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <label class="text-sm font-semibold text-gray-700">Mesa <span class="text-red-500">*</span></label>
                    <span class="text-xs text-gray-400">— las rojas ya tienen reserva en ese horario</span>
                </div>
                <div class="flex items-center gap-4 mb-2">
                    <span class="flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> Disponible
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span> Ocupada
                    </span>
                </div>

                {{-- Grid de mesas scrolleable --}}
                <div class="grid grid-cols-4 gap-2 overflow-y-auto pr-1" style="max-height:180px;">
                    @foreach($mesas as $m)
                        @php
                            $estadoNombre = strtolower($m->estadoMesa->nombre_estado ?? '');
                            $ocupada = $m->estadoMesa && (
                                Str::contains($estadoNombre, 'ocup') ||
                                Str::contains($estadoNombre, 'reserv') ||
                                Str::contains($estadoNombre, 'mant')
                            );
                        @endphp
                        <label class="{{ $ocupada ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                            <input type="radio" name="mesa_id" value="{{ $m->id }}"
                                {{ $ocupada ? 'disabled' : 'required' }}
                                class="sr-only peer"
                                {{ ($loop->first && !$ocupada) ? 'checked' : '' }}>
                            <div class="flex flex-col items-center justify-center gap-1 p-2 rounded-xl border-2 text-center transition
                                {{ $ocupada ? 'border-red-200 bg-red-50 text-red-400' : 'border-gray-200 bg-white text-gray-500 hover:border-gray-400' }}
                                peer-checked:border-gray-800 peer-checked:bg-gray-100 peer-checked:text-gray-800"
                            >
                                <i class="fas fa-chair text-lg"></i>
                                <span class="text-xs font-semibold leading-tight">Mesa {{ $m->numero_mesa }}</span>
                                <span class="text-[10px] leading-tight text-gray-400">Cap. {{ $m->capacidad }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                {{-- Input hidden para compatibilidad cuando no hay mesas --}}
                @if($mesas->isEmpty())
                    <p class="text-xs text-gray-400 mt-2">No hay mesas disponibles.</p>
                @endif
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button
                    type="button"
                    onclick="closeNuevaReservaModal()"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-gray-900 text-white hover:bg-black transition shadow"
                >
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ================================================= -->
<!-- MODAL: EDITAR RESERVA -->
<!-- ================================================= -->
<div id="editReservaModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden" style="max-height:92vh;">

        {{-- CABECERA --}}
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <i class="fas fa-pen text-blue-500 text-base"></i>
                <h3 class="text-lg font-bold text-gray-900">Editar Reserva</h3>
            </div>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-700 transition text-xl leading-none">&times;</button>
        </div>

        {{-- FORMULARIO --}}
        <form id="editReservaForm" method="POST" action="" class="px-6 py-5 space-y-4 overflow-y-auto" style="max-height:calc(92vh - 60px);">
            @csrf
            @method('PUT')

            {{-- Personas + Estado --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Personas</label>
                    <input
                        type="number"
                        id="edit_cantidad_personas"
                        name="cantidad_personas"
                        min="1"
                        max="20"
                        required
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Estado</label>
                    <select
                        id="edit_estado_reserva_id"
                        name="estado_reserva_id"
                        required
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                        @foreach($estados as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre_estado }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fecha + Hora --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fecha</label>
                    <input
                        type="date"
                        id="edit_fecha_reserva"
                        name="fecha_reserva"
                        required
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Hora</label>
                    <input
                        type="time"
                        id="edit_hora_reserva"
                        name="hora_reserva"
                        required
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                </div>
            </div>

            {{-- Mesa --}}
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <label class="text-sm font-semibold text-gray-700">Mesa <span class="text-red-500">*</span></label>
                    <span class="text-xs text-gray-400">— las rojas ya están reservadas en ese horario</span>
                </div>
                <div class="flex items-center gap-4 mb-2">
                    <span class="flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> Disponible
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span> Ocupada
                    </span>
                </div>

                {{-- Grid de mesas scrolleable --}}
                <div class="grid grid-cols-4 gap-2 overflow-y-auto pr-1" style="max-height:180px;">
                    @foreach($mesas as $m)
                        @php
                            $estadoNombre = strtolower($m->estadoMesa->nombre_estado ?? '');
                            $ocupada = $m->estadoMesa && (
                                Str::contains($estadoNombre, 'ocup') ||
                                Str::contains($estadoNombre, 'reserv') ||
                                Str::contains($estadoNombre, 'mant')
                            );
                        @endphp
                        <label class="{{ $ocupada ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                            <input
                                type="radio"
                                name="mesa_id"
                                value="{{ $m->id }}"
                                {{ $ocupada ? 'disabled' : 'required' }}
                                class="sr-only peer edit-mesa-radio"
                                data-mesa-id="{{ $m->id }}"
                            >
                            <div class="flex flex-col items-center justify-center gap-1 p-2 rounded-xl border-2 text-center transition
                                {{ $ocupada ? 'border-red-200 bg-red-50 text-red-400' : 'border-gray-200 bg-white text-gray-500 hover:border-gray-400' }}
                                peer-checked:border-gray-800 peer-checked:bg-gray-100 peer-checked:text-gray-800"
                            >
                                <i class="fas fa-chair text-lg"></i>
                                <span class="text-xs font-semibold leading-tight">Mesa {{ $m->numero_mesa }}</span>
                                <span class="text-[10px] leading-tight text-gray-400">Cap. {{ $m->capacidad }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @if($mesas->isEmpty())
                    <p class="text-xs text-gray-400 mt-2">No hay mesas disponibles.</p>
                @endif
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition shadow"
                >
                    Actualizar
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

        document.getElementById('edit_cantidad_personas').value  = reserva.cantidad_personas;
        document.getElementById('edit_estado_reserva_id').value  = reserva.estado_reserva_id;

        // Fecha
        if (reserva.fecha_reserva) {
            document.getElementById('edit_fecha_reserva').value = reserva.fecha_reserva.substring(0, 10);
        }

        // Hora — puede venir como "HH:mm:ss", "YYYY-MM-DD HH:mm:ss" o con T
        if (reserva.hora_reserva) {
            let hora = reserva.hora_reserva;
            if (hora.includes('T')) hora = hora.split('T')[1];
            document.getElementById('edit_hora_reserva').value = hora.substring(0, 5);
        }

        // Marcar el radio de la mesa correspondiente
        document.querySelectorAll('.edit-mesa-radio').forEach(function(radio) {
            radio.checked = (parseInt(radio.dataset.mesaId) === parseInt(reserva.mesa_id));
        });

        document.getElementById('editReservaModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editReservaModal').classList.add('hidden');
    }
</script>
@endsection
