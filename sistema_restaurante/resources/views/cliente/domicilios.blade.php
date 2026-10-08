@extends('layouts.app')

@section('title', 'Mis Domicilios | Retro Menú')

@section('header', 'DOMICILIOS')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">

    {{-- CABECERA --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-[#c5a059] flex items-center justify-center text-lg">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div>
                    <h1 class="font-heading text-2xl sm:text-3xl font-bold text-gray-900">
                        Mis Domicilios
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500">
                        Seguimiento en tiempo real y detalles de tus pedidos para entrega a domicilio.
                    </p>
                </div>
            </div>
        </div>

        <a
            href="{{ route('productos.index') }}"
            class="bg-[#c5a059] hover:bg-[#b38f48] text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition flex items-center gap-2 shadow-sm hover:shadow"
        >
            <i class="fas fa-motorcycle"></i>
            <span>Pedir a Domicilio</span>
        </a>
    </div>

    {{-- ALERTAS DE SESIÓN --}}
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

    {{-- MÉTRICAS RESUMEN --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-[#c5a059] flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-motorcycle"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Total</span>
                <span class="font-heading text-xl sm:text-2xl font-bold text-gray-900">{{ $totalDomicilios ?? 0 }}</span>
            </div>
        </div>

        {{-- Pendientes --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Pendientes</span>
                <span class="font-heading text-xl sm:text-2xl font-bold text-gray-900">{{ $pendientes ?? 0 }}</span>
            </div>
        </div>

        {{-- En Camino / Cocina --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-route"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">En Camino</span>
                <span class="font-heading text-xl sm:text-2xl font-bold text-gray-900">{{ $enCamino ?? 0 }}</span>
            </div>
        </div>

        {{-- Entregados --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-house-chimney-check"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Entregados</span>
                <span class="font-heading text-xl sm:text-2xl font-bold text-gray-900">{{ $entregados ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- FILTROS Y TABS --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-3 sm:p-4 shadow-xs flex flex-col sm:flex-row justify-between items-center gap-3">
        {{-- Tabs de estado --}}
        <div class="flex items-center gap-1 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
            @php
                $tabActual = request('estado', 'todos');
            @endphp
            <a
                href="{{ route('domicilios.cliente', ['estado' => 'todos', 'search' => request('search')]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $tabActual === 'todos' ? 'bg-[#c5a059] text-white shadow-xs' : 'text-gray-500 hover:bg-gray-100' }}"
            >
                Todos ({{ $totalDomicilios ?? 0 }})
            </a>
            <a
                href="{{ route('domicilios.cliente', ['estado' => 'pendiente', 'search' => request('search')]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $tabActual === 'pendiente' ? 'bg-[#c5a059] text-white shadow-xs' : 'text-gray-500 hover:bg-gray-100' }}"
            >
                Pendientes ({{ $pendientes ?? 0 }})
            </a>
            <a
                href="{{ route('domicilios.cliente', ['estado' => 'preparacion', 'search' => request('search')]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $tabActual === 'preparacion' ? 'bg-[#c5a059] text-white shadow-xs' : 'text-gray-500 hover:bg-gray-100' }}"
            >
                En Camino / Cocina ({{ $enCamino ?? 0 }})
            </a>
            <a
                href="{{ route('domicilios.cliente', ['estado' => 'entregado', 'search' => request('search')]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $tabActual === 'entregado' ? 'bg-[#c5a059] text-white shadow-xs' : 'text-gray-500 hover:bg-gray-100' }}"
            >
                Entregados ({{ $entregados ?? 0 }})
            </a>
        </div>

        {{-- Formulario de Búsqueda --}}
        <form method="GET" action="{{ route('domicilios.cliente') }}" class="w-full sm:w-64">
            @if(request('estado'))
                <input type="hidden" name="estado" value="{{ request('estado') }}">
            @endif
            <div class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar dirección, pedido #..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3 py-1.5 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#c5a059] focus:bg-white transition"
                >
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                @if(request('search'))
                    <a href="{{ route('domicilios.cliente', ['estado' => request('estado')]) }}" class="absolute right-3 top-2 text-gray-400 hover:text-gray-600 text-xs">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- LISTA DE DOMICILIOS --}}
    @if(isset($domicilios) && $domicilios->count() > 0)
        <div class="space-y-4">
            @foreach($domicilios as $d)
                @php
                    $codigo = '#ORD-' . str_pad($d->id, 5, '0', STR_PAD_LEFT);
                    $estadoStr = strtolower($d->estadoPedido->nombre_estado ?? 'pendiente');
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition overflow-hidden">
                    {{-- CABECERA DE LA TARJETA --}}
                    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-wrap justify-between items-center gap-3 bg-gray-50/50">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-sm sm:text-base font-extrabold text-gray-900">
                                {{ $codigo }}
                            </span>

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/50">
                                <i class="fas fa-motorcycle text-[10px]"></i>
                                Entrega a domicilio
                            </span>

                            {{-- FECHA --}}
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <i class="fas fa-calendar-alt text-[10px]"></i>
                                {{ $d->created_at ? $d->created_at->format('d/m/Y - h:i A') : 'Reciente' }}
                            </span>
                        </div>

                        {{-- ESTADO PILL --}}
                        <div class="flex items-center gap-2">
                            @if(str_contains($estadoStr, 'pendiente'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                                    Pedido recibido
                                </span>
                            @elseif(str_contains($estadoStr, 'prepara'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    <i class="fas fa-fire-burner text-[10px]"></i>
                                    En preparación
                                </span>
                            @elseif(str_contains($estadoStr, 'listo'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                    <i class="fas fa-motorcycle text-[10px]"></i>
                                    En camino a tu casa
                                </span>
                            @elseif(str_contains($estadoStr, 'entregado'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    <i class="fas fa-check-double text-[10px]"></i>
                                    Entregado
                                </span>
                            @elseif(str_contains($estadoStr, 'cancelado'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                    <i class="fas fa-xmark text-[10px]"></i>
                                    Cancelado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                    {{ $d->estadoPedido->nombre_estado ?? 'Pendiente' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- LÍNEA DE SEGUIMIENTO (TRACKING BAR) --}}
                    @php
                        $step = 1;
                        if (str_contains($estadoStr, 'prepara')) $step = 2;
                        elseif (str_contains($estadoStr, 'listo')) $step = 3;
                        elseif (str_contains($estadoStr, 'entregado')) $step = 4;
                    @endphp
                    @if(!str_contains($estadoStr, 'cancelado'))
                        <div class="px-5 py-3 bg-white border-b border-gray-100">
                            <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-bold">
                                <div class="{{ $step >= 1 ? 'text-[#c5a059]' : 'text-gray-300' }} flex flex-col items-center">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center mb-1 {{ $step >= 1 ? 'bg-[#c5a059] text-white' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fas fa-receipt text-[10px]"></i>
                                    </div>
                                    <span>Recibido</span>
                                </div>
                                <div class="{{ $step >= 2 ? 'text-[#c5a059]' : 'text-gray-300' }} flex flex-col items-center">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center mb-1 {{ $step >= 2 ? 'bg-[#c5a059] text-white' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fas fa-kitchen-set text-[10px]"></i>
                                    </div>
                                    <span>En cocina</span>
                                </div>
                                <div class="{{ $step >= 3 ? 'text-[#c5a059]' : 'text-gray-300' }} flex flex-col items-center">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center mb-1 {{ $step >= 3 ? 'bg-[#c5a059] text-white' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fas fa-motorcycle text-[10px]"></i>
                                    </div>
                                    <span>En camino</span>
                                </div>
                                <div class="{{ $step >= 4 ? 'text-emerald-600' : 'text-gray-300' }} flex flex-col items-center">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center mb-1 {{ $step >= 4 ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                    <span>Entregado</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- CUERPO: DIRECCIÓN Y PLATILLOS --}}
                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- CAJA DE INFORMACIÓN DE ENTREGA --}}
                        <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-100 text-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div class="flex items-start gap-2.5">
                                <i class="fas fa-location-dot text-[#c5a059] text-sm mt-0.5"></i>
                                <div>
                                    <span class="font-bold text-gray-900 block">
                                        {{ Str::contains($d->observaciones ?? '', 'Dirección:') ? Str::after(Str::before($d->observaciones, '|'), 'Dirección:') : ($d->observaciones ?: 'Dirección registrada') }}
                                    </span>
                                    <span class="text-[11px] text-gray-500">
                                        {{ Str::contains($d->observaciones ?? '', 'Tel:') ? 'Contacto: ' . Str::after(Str::before($d->observaciones, 'Notas:'), 'Tel:') : '' }}
                                    </span>
                                </div>
                            </div>

                            @if(Str::contains($d->observaciones ?? '', 'Notas:'))
                                <div class="bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/50 text-amber-800 text-[11px]">
                                    <i class="fas fa-note-sticky mr-1"></i>
                                    {{ Str::after($d->observaciones, 'Notas:') }}
                                </div>
                            @endif
                        </div>

                        {{-- PLATILLOS --}}
                        <div class="divide-y divide-gray-100">
                            @foreach($d->detalles as $det)
                                <div class="py-2.5 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        @if($det->producto && $det->producto->imagen)
                                            <img
                                                src="{{ $det->producto->imagen }}"
                                                alt="{{ $det->producto->nombre }}"
                                                class="w-10 h-10 rounded-xl object-cover shrink-0 border border-gray-100 shadow-2xs"
                                            >
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#c5a059] flex items-center justify-center shrink-0">
                                                <i class="fas fa-utensils text-xs"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900">
                                                    {{ $det->producto->nombre ?? 'Platillo' }}
                                                </span>
                                                <span class="text-[11px] font-semibold text-gray-400">
                                                    x{{ $det->cantidad }}
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-gray-400 block">
                                                ${{ number_format($det->precio_unitario, 0, ',', '.') }} c/u
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-xs font-bold text-gray-900">
                                            ${{ number_format($det->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- PIE DE LA TARJETA: TOTAL Y ACCIONES --}}
                    <div class="p-4 sm:p-5 bg-gray-50/40 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">Total a pagar en entrega</span>
                            <span class="font-heading text-lg sm:text-xl font-black text-[#c5a059]">
                                ${{ number_format($d->total, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Botón Inspeccionar Domicilio --}}
                            <button
                                type="button"
                                onclick="openOrderDetailsModal({{ $d->id }})"
                                class="px-3.5 py-2 text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
                                title="Inspeccionar detalles del domicilio"
                            >
                                <i class="fas fa-eye text-xs text-[#c5a059]"></i>
                                <span>Inspeccionar</span>
                            </button>

                            @if(str_contains($estadoStr, 'pendiente'))
                                <form method="POST" action="{{ route('cliente.pedidos.cancelar', $d->id) }}" onsubmit="return confirm('¿Estás seguro de cancelar este domicilio?')">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="px-3.5 py-2 text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition cursor-pointer"
                                    >
                                        <i class="fas fa-ban mr-1"></i> Cancelar
                                    </button>
                                </form>
                            @endif

                            <a
                                href="{{ route('productos.index') }}"
                                class="bg-gray-900 hover:bg-black text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                            >
                                <i class="fas fa-rotate-right text-[10px]"></i>
                                <span>Pedir de nuevo</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- PAGINACIÓN --}}
            <div class="pt-4">
                {{ $domicilios->links() }}
            </div>
        </div>
    @else
        {{-- ESTADO VACÍO --}}
        <div class="bg-white rounded-3xl border border-gray-100 p-10 sm:p-14 text-center shadow-xs">
            <div class="w-20 h-20 rounded-full bg-amber-50 text-[#c5a059] flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fas fa-motorcycle"></i>
            </div>
            <h2 class="font-heading text-2xl font-bold text-gray-900">
                Pide a domicilio
            </h2>
            <p class="text-sm text-gray-500 max-w-md mx-auto mt-2">
                Explora nuestro menú, elige la opción Domicilio en el carrito de compras y recíbelo directamente en tu puerta.
            </p>
            <a
                href="{{ route('productos.index') }}"
                class="inline-flex items-center gap-2 mt-6 bg-[#c5a059] hover:bg-[#b38f48] text-white px-6 py-3 rounded-2xl font-bold text-xs uppercase tracking-wider transition shadow-sm hover:shadow"
            >
                <i class="fas fa-utensils"></i>
                <span>Explorar Menú</span>
            </a>
        </div>
    @endif

</div>

{{-- ======================================================== --}}
{{-- MODAL: DETALLES DEL PEDIDO (EXACTO A LA IMAGEN) --}}
{{-- ======================================================== --}}
<div id="orderDetailsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative space-y-4 animate-fade-in border border-gray-100">
        
        <!-- HEADER: TICKET + CÓDIGO + CERRAR -->
        <div class="flex justify-between items-center pb-1">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-receipt text-black text-xl"></i>
                <h3 id="modal_pedido_codigo" class="font-heading text-2xl font-bold text-gray-900 tracking-tight">
                    #ORD-00000
                </h3>
            </div>
            <button 
                type="button" 
                onclick="closeOrderDetailsModal()" 
                class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition cursor-pointer"
            >
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- FILA 1: FECHA Y TIPO -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-[#f8fafc] p-3.5 rounded-2xl">
                <span class="text-xs text-gray-400 block font-medium mb-1">Fecha</span>
                <span id="modal_pedido_fecha" class="font-bold text-gray-900 text-sm block font-sans">
                    --/--/----
                </span>
            </div>
            <div class="bg-[#f8fafc] p-3.5 rounded-2xl">
                <span class="text-xs text-gray-400 block font-medium mb-1">Tipo</span>
                <span id="modal_pedido_tipo" class="font-bold text-gray-900 text-sm block font-sans lowercase">
                    domicilio
                </span>
            </div>
        </div>

        <!-- FILA 2: ESTADO -->
        <div class="bg-[#f8fafc] p-3.5 rounded-2xl">
            <span class="text-xs text-gray-400 block font-medium mb-1.5">Estado</span>
            <div>
                <span id="modal_pedido_estado_badge" class="inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]">
                    Pendiente
                </span>
            </div>
        </div>

        <!-- FILA 3: DIRECCIÓN / MESA -->
        <div class="bg-[#f8fafc] p-3.5 rounded-2xl" id="modal_pedido_direccion_box">
            <span id="modal_pedido_direccion_label" class="text-xs text-gray-400 block font-medium mb-1">
                Dirección de entrega
            </span>
            <span id="modal_pedido_direccion_val" class="font-bold text-gray-900 text-sm block break-words">
                --
            </span>
        </div>

        <!-- FILA 4: PRODUCTOS -->
        <div class="pt-1">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 font-sans">
                PRODUCTOS
            </h4>

            <div class="bg-[#f8fafc] rounded-2xl p-4 pt-3 overflow-hidden">
                <!-- Cabecera de la lista -->
                <div class="grid grid-cols-12 text-xs font-heading font-bold text-gray-700 uppercase py-2 border-b border-gray-200/60">
                    <div class="col-span-6">PRODUCTO</div>
                    <div class="col-span-2 text-center">CANT.</div>
                    <div class="col-span-4 text-right">SUBTOTAL</div>
                </div>

                <!-- Filas de productos inyectadas con JS -->
                <div id="modal_detalles_lista" class="divide-y divide-gray-100/80 text-xs">
                    <!-- Dinámico -->
                </div>
            </div>
        </div>

        <!-- TOTAL BANNER (BARRA OSCURA CON TOTAL EN VERDE) -->
        <div class="bg-[#1e293b] rounded-2xl p-4 px-5 flex justify-between items-center text-white shadow-sm mt-3">
            <span class="font-heading text-sm font-bold tracking-wider uppercase text-white">
                TOTAL
            </span>
            <span id="modal_pedido_total_val" class="font-heading text-xl sm:text-2xl font-black text-[#22c55e]">
                $0
            </span>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function openOrderDetailsModal(pedidoId) {
        const lista = document.getElementById('modal_detalles_lista');
        lista.innerHTML = '<div class="py-4 text-center text-gray-400">Cargando detalles...</div>';

        document.getElementById('orderDetailsModal').classList.remove('hidden');

        fetch(`/cliente/pedidos/${pedidoId}/detalles`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modal_pedido_codigo').textContent = data.codigo;
                document.getElementById('modal_pedido_fecha').textContent = data.fecha;
                document.getElementById('modal_pedido_tipo').textContent = (data.tipo || 'domicilio').toLowerCase();

                // Estado badge
                const estadoBadge = document.getElementById('modal_pedido_estado_badge');
                estadoBadge.textContent = data.estado;
                const estLower = (data.estado || '').toLowerCase();
                if (estLower.includes('pendiente')) {
                    estadoBadge.className = 'inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]';
                } else if (estLower.includes('prepara')) {
                    estadoBadge.className = 'inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800';
                } else if (estLower.includes('listo') || estLower.includes('camino')) {
                    estadoBadge.className = 'inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800';
                } else if (estLower.includes('entregado')) {
                    estadoBadge.className = 'inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800';
                } else {
                    estadoBadge.className = 'inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700';
                }

                // Dirección / Ubicación
                const labelDir = document.getElementById('modal_pedido_direccion_label');
                const valDir = document.getElementById('modal_pedido_direccion_val');
                labelDir.textContent = 'Dirección de entrega';
                valDir.textContent = data.direccion || data.observaciones || 'Sin dirección registrada';

                // Total en el banner inferior
                document.getElementById('modal_pedido_total_val').textContent = data.total;

                // Lista de productos
                if (data.detalles && data.detalles.length > 0) {
                    let html = '';
                    data.detalles.forEach(d => {
                        html += `
                            <div class="grid grid-cols-12 items-center py-2.5">
                                <div class="col-span-6 font-medium text-gray-800 lowercase truncate pr-2">
                                    ${d.producto}
                                </div>
                                <div class="col-span-2 text-center text-gray-700 font-normal">
                                    ${d.cantidad}
                                </div>
                                <div class="col-span-4 text-right font-bold text-gray-900 font-sans">
                                    ${d.subtotal}
                                </div>
                            </div>
                        `;
                    });
                    lista.innerHTML = html;
                } else {
                    lista.innerHTML = '<div class="py-4 text-center text-gray-400">No hay productos registrados en este pedido.</div>';
                }
            })
            .catch(err => {
                lista.innerHTML = '<div class="py-4 text-center text-rose-500">Error al cargar los detalles.</div>';
            });
    }

    function closeOrderDetailsModal() {
        document.getElementById('orderDetailsModal').classList.add('hidden');
    }
</script>
@endpush

@endsection