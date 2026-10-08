@extends('layouts.admin')

@section('title', 'Gestión de Domicilios | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <span class="w-1.5 h-6 bg-retro-gold rounded-full inline-block"></span>
        <span class="font-heading font-bold text-gray-900 tracking-wider">DOMICILIOS</span>
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-7 pb-12">

    <!-- CABECERA PRINCIPAL -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <i class="fas fa-motorcycle text-3xl text-gray-900"></i>
                <h1 class="font-heading text-3xl font-bold text-gray-900 tracking-tight">
                    Domicilios
                </h1>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Gestiona y supervisa todos los pedidos a domicilio.
            </p>
        </div>

        <div>
            <button
                type="button"
                onclick="openModalNuevo()"
                class="inline-flex items-center gap-2 bg-[#0a0a0a] hover:bg-retro-gold text-white font-medium text-sm px-5 py-2.5 rounded-xl transition-all duration-300 shadow-sm"
            >
                <i class="fas fa-plus text-xs"></i>
                <span>Nuevo Domicilio</span>
            </button>
        </div>
    </div>

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

    <!-- ================================================= -->
    <!-- METRICAS SUPERIORES (5 CARDS) -->
    <!-- ================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        <!-- Domicilios hoy -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#dbeafe] flex items-center justify-center text-[#2563eb] text-xl shrink-0">
                <i class="fas fa-motorcycle"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Domicilios hoy</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $domiciliosHoy }}</h3>
                <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">{{ $textoVsAyer }}</p>
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
            </div>
        </div>

        <!-- En preparación -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#e0f2fe] flex items-center justify-center text-[#0284c7] text-xl shrink-0">
                <i class="fas fa-kitchen-set"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">En preparación</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $enPreparacion }}</h3>
            </div>
        </div>

        <!-- Entregados -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#dcfce7] flex items-center justify-center text-[#16a34a] text-xl shrink-0">
                <i class="fas fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Entregados</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $entregados }}</h3>
            </div>
        </div>

        <!-- Cancelados -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#fee2e2] flex items-center justify-center text-[#dc2626] text-xl shrink-0">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Cancelados</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $cancelados }}</h3>
            </div>
        </div>

    </div>

    <!-- ================================================= -->
    <!-- CONTENEDOR PRINCIPAL: TABS + FILTROS + TABLA -->
    <!-- ================================================= -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <!-- BARRA SUPERIOR CON TABS Y BUSCADOR/FECHA -->
        <div class="p-4 sm:px-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

            <!-- TABS -->
            <div class="flex items-center gap-2 sm:gap-6 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 text-xs sm:text-sm">
                @php
                    $tabs = [
                        'todos' => 'Todos',
                        'pendiente' => 'Pendiente',
                        'en_preparacion' => 'En preparacion',
                        'listo' => 'Listo',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado'
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <a
                        href="{{ route('admin.domicilios.index', array_merge(request()->query(), ['estado' => $key, 'page' => 1])) }}"
                        class="whitespace-nowrap pb-1 transition-all duration-200 {{ $tabActual === $key ? 'font-bold text-gray-900 border-b-2 border-black' : 'text-gray-400 hover:text-gray-700 font-medium' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- FILTRO DE FECHA Y BUSCADOR -->
            <form action="{{ route('admin.domicilios.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <input type="hidden" name="estado" value="{{ $tabActual }}">

                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="relative">
                    <input
                        type="date"
                        name="fecha"
                        value="{{ request('fecha') }}"
                        onchange="this.form.submit()"
                        class="border border-gray-200 rounded-xl px-3 py-1.5 text-xs text-gray-600 focus:outline-none focus:border-retro-gold bg-gray-50/50 hover:bg-gray-50 transition cursor-pointer"
                        placeholder="dd/mm/aaaa"
                    >
                </div>

                @if(request('fecha') || (request('estado') && request('estado') !== 'todos') || request('search'))
                    <a
                        href="{{ route('admin.domicilios.index') }}"
                        class="text-xs text-gray-400 hover:text-rose-600 transition px-2 py-1 flex items-center gap-1"
                        title="Limpiar filtros"
                    >
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </form>

        </div>

        <!-- ================================================= -->
        <!-- TABLA O ESTADO VACÍO -->
        <!-- ================================================= -->
        @if($domicilios->isEmpty())
            <!-- ESTADO VACÍO (IGUAL A LA IMAGEN) -->
            <div class="py-20 text-center px-4">
                <div class="inline-flex items-center justify-center text-5xl text-gray-400 mb-4">
                    <i class="fas fa-motorcycle"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-gray-800 font-heading mb-1">
                    Sin domicilios
                </h3>

                <p class="text-xs sm:text-sm text-gray-400 max-w-sm mx-auto mb-6">
                    No hay pedidos a domicilio con los filtros seleccionados.
                </p>

                <button
                    type="button"
                    onclick="openModalNuevo()"
                    class="inline-flex items-center gap-2 bg-[#0a0a0a] hover:bg-retro-gold text-white font-medium text-xs sm:text-sm px-6 py-2.5 rounded-full transition-all duration-300 shadow-sm"
                >
                    <i class="fas fa-plus text-xs"></i>
                    <span>Registrar domicilio</span>
                </button>
            </div>
        @else
            <!-- TABLA DE DOMICILIOS -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-[#fbfbfb] text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-6">Pedido</th>
                            <th class="py-4 px-6">Cliente</th>
                            <th class="py-4 px-6">Contacto</th>
                            <th class="py-4 px-6">Fecha</th>
                            <th class="py-4 px-6">Total</th>
                            <th class="py-4 px-6">Estado</th>
                            <th class="py-4 px-6 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs sm:text-sm text-gray-700">
                        @foreach($domicilios as $dom)
                            @php
                                $estadoNombre = $dom->estadoPedido->nombre_estado ?? 'Pendiente';
                                $badgeClass = 'bg-gray-100 text-gray-700';

                                if(str_contains(strtolower($estadoNombre), 'pendiente')) {
                                    $badgeClass = 'bg-[#fef3c7] text-[#b45309]';
                                } elseif(str_contains(strtolower($estadoNombre), 'preparaci')) {
                                    $badgeClass = 'bg-[#e0f2fe] text-[#0369a1]';
                                } elseif(str_contains(strtolower($estadoNombre), 'listo')) {
                                    $badgeClass = 'bg-[#e0e7ff] text-[#4338ca]';
                                } elseif(str_contains(strtolower($estadoNombre), 'entregado')) {
                                    $badgeClass = 'bg-[#dcfce7] text-[#15803d]';
                                } elseif(str_contains(strtolower($estadoNombre), 'cancelado')) {
                                    $badgeClass = 'bg-[#fee2e2] text-[#b91c1c]';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-4 px-6 font-bold text-gray-900 whitespace-nowrap">
                                    #DOM-{{ str_pad($dom->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-medium text-gray-900">
                                        {{ $dom->cliente->user->name ?? 'Cliente' }} {{ $dom->cliente->user->apellidos ?? '' }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $dom->cliente->user->email ?? '' }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 max-w-xs">
                                    <div class="text-xs text-gray-800 font-medium">
                                        <i class="fas fa-phone-alt text-[10px] text-gray-400 mr-1"></i>
                                        {{ $dom->cliente->user->telefono ?? 'N/A' }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 truncate" title="{{ $dom->observaciones }}">
                                        <i class="fas fa-location-dot text-[10px] text-gray-400 mr-1"></i>
                                        {{ $dom->observaciones ?? 'Sin dirección' }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-gray-500 text-xs">
                                    {{ $dom->created_at ? $dom->created_at->format('d/m/Y h:i A') : 'N/A' }}
                                </td>
                                <td class="py-4 px-6 font-bold text-gray-900 whitespace-nowrap">
                                    ${{ number_format($dom->total, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                                        {{ $estadoNombre }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-2">
                                        <!-- 1. Ver Detalles (Ojo) -->
                                        <button
                                            type="button"
                                            onclick="verDetalles({{ $dom->id }})"
                                            class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                            title="Ver detalles del domicilio"
                                        >
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>

                                        <!-- 2. Editar Estado (Lápiz) -->
                                        <button
                                            type="button"
                                            onclick="openEstadoModal({{ $dom->id }}, {{ $dom->estado_pedido_id }}, '#ORD-{{ str_pad($dom->id, 5, '0', STR_PAD_LEFT) }}')"
                                            class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                            title="Actualizar estado"
                                        >
                                            <i class="fas fa-pencil text-xs"></i>
                                        </button>

                                        <!-- 3. Cancelar Domicilio (Cruz X) -->
                                        <button
                                            type="button"
                                            onclick="confirmarCancelarDomicilio({{ $dom->id }}, '#ORD-{{ str_pad($dom->id, 5, '0', STR_PAD_LEFT) }}')"
                                            class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-rose-100 text-gray-700 hover:text-rose-700 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                            title="Cancelar domicilio"
                                        >
                                            <i class="fas fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- ================================================= -->
        <!-- PIE / PAGINACIÓN -->
        <!-- ================================================= -->
        <div class="p-4 sm:px-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-500">
            <div>
                Mostrando {{ $domicilios->firstItem() ?? 0 }} a {{ $domicilios->lastItem() ?? 0 }} de {{ $domicilios->total() }} domicilios
            </div>

            @if($domicilios->hasPages())
                <div>
                    {{ $domicilios->links('pagination::tailwind') }}
                </div>
            @else
                <div class="flex items-center gap-1">
                    <span class="w-7 h-7 bg-black text-white text-xs font-bold rounded flex items-center justify-center">
                        1
                    </span>
                </div>
            @endif
        </div>

    </div>

</div>

<!-- ========================================================= -->
<!-- MODAL: REGISTRAR NUEVO DOMICILIO -->
<!-- ========================================================= -->
<div
    id="modalNuevoDomicilio"
    class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 overflow-y-auto"
>
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden my-8">

        <!-- CABECERA MODAL -->
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-900">
                    <i class="fas fa-motorcycle text-lg"></i>
                </div>
                <div>
                    <h3 class="font-heading text-xl font-bold text-gray-900">
                        Nuevo Pedido a Domicilio
                    </h3>
                    <p class="text-xs text-gray-500">
                        Ingrese la información del cliente y los productos solicitados
                    </p>
                </div>
            </div>

            <button
                type="button"
                onclick="closeModalNuevo()"
                class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center"
            >
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <!-- FORMULARIO -->
        <form action="{{ route('admin.domicilios.store') }}" method="POST" id="formNuevoDomicilio" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- CLIENTE EXISTENTE O NOMBRE -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Cliente Existente (Opcional)
                    </label>
                    <select
                        name="cliente_id"
                        id="cliente_select"
                        onchange="onClienteSelectChange(this)"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                    >
                        <option value="">-- Seleccionar cliente registrado o escribir abajo --</option>
                        @foreach($clientes as $cli)
                            <option
                                value="{{ $cli->id }}"
                                data-nombre="{{ $cli->user->name ?? '' }} {{ $cli->user->apellidos ?? '' }}"
                                data-telefono="{{ $cli->user->telefono ?? '' }}"
                            >
                                {{ $cli->user->name ?? 'Cliente' }} {{ $cli->user->apellidos ?? '' }} ({{ $cli->user->telefono ?? 'Sin tel' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- NOMBRE CLIENTE -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nombre del Cliente <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nombre_cliente"
                        id="input_nombre_cliente"
                        required
                        placeholder="Ej: Laura Martínez"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                    >
                </div>

                <!-- TELÉFONO -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Teléfono / Contacto <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="telefono"
                        id="input_telefono"
                        required
                        placeholder="Ej: +57 300 123 4567"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                    >
                </div>

                <!-- DIRECCIÓN -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Dirección de Entrega <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="direccion"
                        required
                        placeholder="Ej: Cra 45 # 12-34 Apto 502, Torre 1"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                    >
                </div>

                <!-- OBSERVACIONES / REFERENCIAS -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Notas de Entrega / Referencias
                    </label>
                    <input
                        type="text"
                        name="observaciones"
                        placeholder="Ej: Timbre no funciona, dejar en portería"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                    >
                </div>
            </div>

            <!-- SELECCIÓN DE PRODUCTOS -->
            <div class="border-t border-gray-100 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Productos a Solicitar <span class="text-rose-500">*</span>
                    </label>
                    <button
                        type="button"
                        onclick="agregarFilaProducto()"
                        class="text-xs text-retro-gold hover:text-black font-bold flex items-center gap-1 transition"
                    >
                        <i class="fas fa-plus"></i> Añadir otro producto
                    </button>
                </div>

                <div id="contenedorProductos" class="space-y-3">
                    <!-- Fila 0 -->
                    <div class="grid grid-cols-12 gap-2 items-center fila-producto">
                        <div class="col-span-7">
                            <select
                                name="productos[0][id]"
                                required
                                onchange="recalcularTotal()"
                                class="producto-select w-full border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                            >
                                <option value="" data-precio="0">-- Seleccionar Producto --</option>
                                @foreach($productos as $prod)
                                    <option value="{{ $prod->id }}" data-precio="{{ $prod->precio }}">
                                        {{ $prod->nombre }} - ${{ number_format($prod->precio, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-3">
                            <input
                                type="number"
                                name="productos[0][cantidad]"
                                value="1"
                                min="1"
                                required
                                onchange="recalcularTotal()"
                                onkeyup="recalcularTotal()"
                                class="producto-cantidad w-full border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold text-center"
                                placeholder="Cant."
                            >
                        </div>
                        <div class="col-span-2 text-right">
                            <button
                                type="button"
                                onclick="eliminarFilaProducto(this)"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-rose-100 text-gray-400 hover:text-rose-600 transition inline-flex items-center justify-center"
                            >
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TOTAL ESTIMADO -->
                <div class="mt-4 p-3 bg-gray-50 rounded-xl flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-600 uppercase">Total a Cobrar:</span>
                    <span id="totalCalculadoTexto" class="text-base font-extrabold text-gray-900 font-heading">
                        $0
                    </span>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="pt-3 flex justify-end gap-3">
                <button
                    type="button"
                    onclick="closeModalNuevo()"
                    class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-xl bg-[#0a0a0a] hover:bg-retro-gold text-white text-xs font-bold uppercase tracking-wider transition shadow-sm"
                >
                    Registrar Domicilio
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL: DETALLES DE DOMICILIO (DISEÑO EXACTO) -->
<!-- ========================================================= -->
<div
    id="modalDetalles"
    class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 overflow-y-auto"
>
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative space-y-4 animate-fade-in border border-gray-100 my-8">
        
        <!-- HEADER: TICKET + CÓDIGO + CERRAR -->
        <div class="flex justify-between items-center pb-1">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-receipt text-black text-xl"></i>
                <h3 id="detallesCodigo" class="font-heading text-2xl font-bold text-gray-900 tracking-tight">
                    #ORD-00000
                </h3>
            </div>
            <button 
                type="button" 
                onclick="closeModalDetalles()" 
                class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition cursor-pointer"
            >
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- FILA 1: FECHA Y TIPO -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-[#f8fafc] p-3.5 rounded-2xl">
                <span class="text-xs text-gray-400 block font-medium mb-1">Fecha</span>
                <span id="detallesFecha" class="font-bold text-gray-900 text-sm block font-sans">
                    --/--/----
                </span>
            </div>
            <div class="bg-[#f8fafc] p-3.5 rounded-2xl">
                <span class="text-xs text-gray-400 block font-medium mb-1">Tipo</span>
                <span id="detallesTipo" class="font-bold text-gray-900 text-sm block font-sans lowercase">
                    domicilio
                </span>
            </div>
        </div>

        <!-- FILA 2: ESTADO -->
        <div class="bg-[#f8fafc] p-3.5 rounded-2xl">
            <span class="text-xs text-gray-400 block font-medium mb-1.5">Estado</span>
            <div>
                <span id="detallesEstadoBadge" class="inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]">
                    Pendiente
                </span>
            </div>
        </div>

        <!-- FILA 3: DIRECCIÓN DE ENTREGA -->
        <div class="bg-[#f8fafc] p-3.5 rounded-2xl">
            <span class="text-xs text-gray-400 block font-medium mb-1">
                Dirección de entrega
            </span>
            <span id="detallesDireccionVal" class="font-bold text-gray-900 text-sm block break-words">
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
                <div id="detallesListaProductos" class="divide-y divide-gray-100/80 text-xs">
                    <!-- Dinámico -->
                </div>
            </div>
        </div>

        <!-- TOTAL BANNER (BARRA OSCURA CON TOTAL EN VERDE) -->
        <div class="bg-[#1e293b] rounded-2xl p-4 px-5 flex justify-between items-center text-white shadow-sm mt-3">
            <span class="font-heading text-sm font-bold tracking-wider uppercase text-white">
                TOTAL
            </span>
            <span id="detallesTotal" class="font-heading text-xl sm:text-2xl font-black text-[#22c55e]">
                $0
            </span>
        </div>

    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL: CAMBIAR ESTADO -->
<!-- ========================================================= -->
<div
    id="modalEstado"
    class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4"
>
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-heading text-lg font-bold text-gray-900">
                Cambiar Estado
            </h3>
            <button onclick="closeModalEstado()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form id="formCambiarEstado" method="POST" action="" class="p-5 space-y-4">
            @csrf
            @method('PATCH')

            <p id="textoEstadoCodigo" class="text-xs text-gray-500 font-medium"></p>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nuevo Estado</label>
                <select
                    name="estado_pedido_id"
                    id="selectNuevoEstado"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-retro-gold"
                >
                    @foreach($estados as $est)
                        <option value="{{ $est->id }}">{{ $est->nombre_estado }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    onclick="closeModalEstado()"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-black hover:bg-retro-gold text-white text-xs font-bold uppercase tracking-wider transition"
                >
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let filaContador = 1;

    // Abrir / Cerrar Modal Nuevo
    function openModalNuevo() {
        document.getElementById('modalNuevoDomicilio').classList.remove('hidden');
        document.getElementById('modalNuevoDomicilio').classList.add('flex');
    }

    function closeModalNuevo() {
        document.getElementById('modalNuevoDomicilio').classList.add('hidden');
        document.getElementById('modalNuevoDomicilio').classList.remove('flex');
    }

    // Al seleccionar cliente registrado
    function onClienteSelectChange(select) {
        const option = select.options[select.selectedIndex];
        if (option.value) {
            document.getElementById('input_nombre_cliente').value = option.dataset.nombre || '';
            document.getElementById('input_telefono').value = option.dataset.telefono || '';
        }
    }

    // Agregar fila de producto
    function agregarFilaProducto() {
        const container = document.getElementById('contenedorProductos');
        const fila = document.createElement('div');
        fila.className = 'grid grid-cols-12 gap-2 items-center fila-producto';
        fila.innerHTML = `
            <div class="col-span-7">
                <select
                    name="productos[${filaContador}][id]"
                    required
                    onchange="recalcularTotal()"
                    class="producto-select w-full border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                >
                    <option value="" data-precio="0">-- Seleccionar Producto --</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id }}" data-precio="{{ $prod->precio }}">
                            {{ $prod->nombre }} - ${{ number_format($prod->precio, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-3">
                <input
                    type="number"
                    name="productos[${filaContador}][cantidad]"
                    value="1"
                    min="1"
                    required
                    onchange="recalcularTotal()"
                    onkeyup="recalcularTotal()"
                    class="producto-cantidad w-full border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold text-center"
                    placeholder="Cant."
                >
            </div>
            <div class="col-span-2 text-right">
                <button
                    type="button"
                    onclick="eliminarFilaProducto(this)"
                    class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-rose-100 text-gray-400 hover:text-rose-600 transition inline-flex items-center justify-center"
                >
                    <i class="fas fa-trash-can text-xs"></i>
                </button>
            </div>
        `;
        container.appendChild(fila);
        filaContador++;
    }

    function eliminarFilaProducto(btn) {
        const filas = document.querySelectorAll('.fila-producto');
        if (filas.length > 1) {
            btn.closest('.fila-producto').remove();
            recalcularTotal();
        } else {
            alert('Debe haber al menos un producto en el pedido.');
        }
    }

    function recalcularTotal() {
        let total = 0;
        const filas = document.querySelectorAll('.fila-producto');

        filas.forEach(f => {
            const select = f.querySelector('.producto-select');
            const cantInput = f.querySelector('.producto-cantidad');

            if (select && cantInput) {
                const option = select.options[select.selectedIndex];
                const precio = parseFloat(option.dataset.precio || 0);
                const cant = parseInt(cantInput.value || 0);
                total += precio * cant;
            }
        });

        document.getElementById('totalCalculadoTexto').innerText = '$' + total.toLocaleString('es-CO');
    }

    // Modal Detalles
    function verDetalles(id) {
        const lista = document.getElementById('detallesListaProductos');
        lista.innerHTML = '<div class="py-4 text-center text-gray-400">Cargando detalles...</div>';

        document.getElementById('modalDetalles').classList.remove('hidden');
        document.getElementById('modalDetalles').classList.add('flex');

        fetch(`/admin/domicilios/${id}/detalles`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('detallesCodigo').innerText = data.codigo;
                document.getElementById('detallesFecha').innerText = data.fecha;
                document.getElementById('detallesTipo').innerText = (data.tipo || 'domicilio').toLowerCase();

                // Estado badge
                const estadoBadge = document.getElementById('detallesEstadoBadge');
                estadoBadge.innerText = data.estado;
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

                // Dirección
                document.getElementById('detallesDireccionVal').innerText = data.direccion || data.observaciones || 'Sin dirección registrada';

                // Total
                document.getElementById('detallesTotal').innerText = data.total;

                let html = '';
                if (data.detalles && data.detalles.length > 0) {
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
                } else {
                    html = '<div class="py-4 text-center text-gray-400">Sin detalles de productos registrados.</div>';
                }
                lista.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                lista.innerHTML = '<div class="py-4 text-center text-rose-500">Error al cargar los detalles.</div>';
            });
    }

    function closeModalDetalles() {
        document.getElementById('modalDetalles').classList.add('hidden');
        document.getElementById('modalDetalles').classList.remove('flex');
    }

    // Modal Cambiar Estado
    function openEstadoModal(id, estadoActualId, codigo) {
        const form = document.getElementById('formCambiarEstado');
        form.action = `/admin/domicilios/${id}/estado`;
        document.getElementById('textoEstadoCodigo').innerText = `Actualizando estado para el pedido ${codigo}`;
        document.getElementById('selectNuevoEstado').value = estadoActualId;

        document.getElementById('modalEstado').classList.remove('hidden');
        document.getElementById('modalEstado').classList.add('flex');
    }

    function closeModalEstado() {
        document.getElementById('modalEstado').classList.add('hidden');
        document.getElementById('modalEstado').classList.remove('flex');
    }

    // Confirmar cancelación de domicilio
    function confirmarCancelarDomicilio(id, codigo) {
        if (confirm(`¿Estás seguro de cancelar el pedido ${codigo}? Esta acción marcará el domicilio como cancelado y devolverá el stock al inventario.`)) {
            const form = document.getElementById('formCancelarDomicilio');
            form.action = `/admin/domicilios/${id}`;
            form.submit();
        }
    }
</script>

{{-- Formulario oculto para cancelación de domicilios --}}
<form id="formCancelarDomicilio" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endpush
