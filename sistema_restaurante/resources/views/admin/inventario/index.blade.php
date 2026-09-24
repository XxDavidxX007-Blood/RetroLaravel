@extends('layouts.admin')

@section('title', 'Gestión de Inventarios | Administrador')

@section('header', 'GESTIÓN DE INVENTARIOS')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ============================================= --}}
    {{-- ENCABEZADO + ACCIONES --}}
    {{-- ============================================= --}}

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h2 class="font-heading text-3xl font-bold text-gray-900 tracking-tight">
                Gestión de Inventarios
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Administra y controla todos los insumos de tu restaurante
            </p>
        </div>

        <div class="flex items-center gap-3">

            {{-- EXPORTAR --}}
            <a
                href="#"
                onclick="exportarCSV(); return false;"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition shadow-sm"
            >
                <i class="fas fa-download text-xs"></i>
                Exportar
            </a>

            {{-- NUEVO PRODUCTO --}}
            <a
                href="{{ route('admin.menu.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white text-sm font-semibold transition shadow-sm"
            >
                <i class="fas fa-plus text-xs"></i>
                Nuevo Producto
            </a>

        </div>

    </div>


    {{-- ============================================= --}}
    {{-- TARJETAS DE METRICAS --}}
    {{-- ============================================= --}}

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">

        {{-- TOTAL PRODUCTOS --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <i class="fas fa-boxes-stacked text-emerald-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 leading-tight">Total de<br>Productos</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $totalProductos }}</p>
                <p class="text-[10px] text-gray-400">insumos registrados</p>
            </div>
        </div>

        {{-- VALOR INVENTARIO --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <i class="fas fa-clipboard-list text-blue-400 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 leading-tight">Valor Inventario</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">${{ number_format($valorStock, 0, ',', '.') }}</p>
                <p class="text-[10px] text-gray-400">Valor total</p>
            </div>
        </div>

        {{-- STOCK BAJO --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <i class="fas fa-triangle-exclamation text-amber-400 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 leading-tight">Stock Bajo</p>
                <p class="text-2xl font-bold text-amber-500 mt-0.5">{{ $stockBajo }}</p>
                <p class="text-[10px] text-gray-400">Productos bajos</p>
            </div>
        </div>

        {{-- SIN STOCK --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <i class="fas fa-circle-xmark text-red-400 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 leading-tight">Sin Stock</p>
                <p class="text-2xl font-bold text-red-500 mt-0.5">{{ $sinStock }}</p>
                <p class="text-[10px] text-gray-400">Agotados</p>
            </div>
        </div>

        {{-- MOVIMIENTOS --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                <i class="fas fa-arrow-right-arrow-left text-purple-400 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 leading-tight">Movimientos</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $operacionesMes }}</p>
                <p class="text-[10px] text-gray-400">Este mes</p>
            </div>
        </div>

    </div>


    {{-- ============================================= --}}
    {{-- FILTROS --}}
    {{-- ============================================= --}}

    <form method="GET" action="{{ route('admin.inventario.index') }}" id="filtrosForm">
        <div class="flex flex-col sm:flex-row gap-3 items-center">

            {{-- BUSCADOR --}}
            <div class="relative flex-1">
                <i class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    value="{{ request('search') }}"
                    placeholder="Buscar ingrediente o producto..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                >
            </div>

            {{-- CATEGORIAS --}}
            <select
                name="categoria_id"
                onchange="this.form.submit()"
                class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-400 transition cursor-pointer"
            >
                <option value="">Todas las categorias</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>

            {{-- ESTADO --}}
            <select
                name="filtro_stock"
                onchange="this.form.submit()"
                class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-400 transition cursor-pointer"
            >
                <option value="">Todos los estados</option>
                <option value="con_stock"  {{ request('filtro_stock') === 'con_stock'  ? 'selected' : '' }}>Con Stock</option>
                <option value="stock_bajo" {{ request('filtro_stock') === 'stock_bajo' ? 'selected' : '' }}>Stock Bajo</option>
                <option value="sin_stock"  {{ request('filtro_stock') === 'sin_stock'  ? 'selected' : '' }}>Sin Stock</option>
            </select>

            {{-- LIMPIAR --}}
            @if(request()->hasAny(['search', 'categoria_id', 'filtro_stock']))
                <a
                    href="{{ route('admin.inventario.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-600 hover:bg-gray-50 transition whitespace-nowrap"
                >
                    <i class="fas fa-filter-circle-xmark text-gray-400"></i>
                    Limpiar Filtros
                </a>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-400 whitespace-nowrap cursor-default">
                    <i class="fas fa-filter text-gray-300"></i>
                    Limpiar Filtros
                </span>
            @endif

        </div>
    </form>


    {{-- ============================================= --}}
    {{-- TABLA --}}
    {{-- ============================================= --}}

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="mx-6 mt-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
                <i class="fas fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-2">
                <i class="fas fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                {{-- CABECERA --}}
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Producto</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Categoria</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Unidad</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Stock Actual</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Stock Minimo</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Estado</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Valor Unitario</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Valor Total</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">
                    @forelse($productos as $producto)

                        @php
                            $inv      = $producto->inventario;
                            $cantidad = $inv->cantidad     ?? 0;
                            $minimo   = $inv->stock_minimo ?? 0;
                            $unidad   = $inv->unidad       ?? '--';
                            $precio   = $producto->precio  ?? 0;
                            $total    = $cantidad * $precio;

                            if ($cantidad == 0) {
                                $estadoLabel = 'Sin stock';
                                $estadoClass = 'bg-red-100 text-red-600';
                                $stockColor  = 'text-red-500 font-bold';
                            } elseif ($cantidad <= $minimo) {
                                $estadoLabel = 'Stock bajo';
                                $estadoClass = 'bg-amber-100 text-amber-600';
                                $stockColor  = 'text-amber-500 font-bold';
                            } else {
                                $estadoLabel = 'Con stock';
                                $estadoClass = 'bg-green-100 text-green-600';
                                $stockColor  = 'text-gray-700 font-semibold';
                            }
                        @endphp

                        <tr class="hover:bg-gray-50/60 transition" id="fila-{{ $producto->id }}">

                            {{-- PRODUCTO --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0 text-gray-400 overflow-hidden">
                                        @if($producto->imagen)
                                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-utensils text-xs"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 leading-tight">{{ $producto->nombre }}</p>
                                        <p class="text-gray-400" style="font-size:10px">ID: ING-{{ str_pad($producto->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- CATEGORIA --}}
                            <td class="px-4 py-4 text-gray-600">
                                {{ $producto->categoria->nombre ?? '--' }}
                            </td>

                            {{-- UNIDAD --}}
                            <td class="px-4 py-4 text-gray-600">{{ $unidad }}</td>

                            {{-- STOCK ACTUAL --}}
                            <td class="px-4 py-4">
                                <span class="{{ $stockColor }}">{{ $cantidad }}</span>
                            </td>

                            {{-- STOCK MINIMO --}}
                            <td class="px-4 py-4 text-gray-600">{{ $minimo }}</td>

                            {{-- ESTADO --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $estadoClass }}">
                                    {{ $estadoLabel }}
                                </span>
                            </td>

                            {{-- VALOR UNITARIO --}}
                            <td class="px-4 py-4 text-gray-700">
                                ${{ number_format($precio, 0, ',', '.') }}
                            </td>

                            {{-- VALOR TOTAL --}}
                            <td class="px-4 py-4 text-gray-700 font-medium">
                                ${{ number_format($total, 0, ',', '.') }}
                            </td>

                            {{-- ACCIONES --}}
                            <td class="px-4 py-4">
                                <div class="relative inline-block">
                                    <button
                                        type="button"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition"
                                        onclick="toggleMenu({{ $producto->id }})"
                                    >
                                        <i class="fas fa-ellipsis-vertical"></i>
                                    </button>

                                    <div
                                        id="menu-{{ $producto->id }}"
                                        class="hidden absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl border border-gray-100 z-30 py-1.5 text-sm"
                                    >
                                        <button
                                            type="button"
                                            onclick="abrirModalStock({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', {{ $cantidad }}, '{{ addslashes($unidad) }}')"
                                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 flex items-center gap-2.5"
                                        >
                                            <i class="fas fa-arrow-right-arrow-left text-blue-400 w-4"></i>
                                            Actualizar Stock
                                        </button>
                                        <button
                                            type="button"
                                            onclick="verHistorial({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')"
                                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 flex items-center gap-2.5"
                                        >
                                            <i class="fas fa-clock-rotate-left text-purple-400 w-4"></i>
                                            Ver Historial
                                        </button>
                                        <hr class="my-1 border-gray-100">
                                        <button
                                            type="button"
                                            onclick="abrirModalEditar(
                                                {{ $producto->id }},
                                                '{{ addslashes($producto->nombre) }}',
                                                {{ $producto->categoria_producto_id ?? 'null' }},
                                                {{ $precio }},
                                                '{{ addslashes($unidad) }}',
                                                {{ $cantidad }},
                                                {{ $minimo }}
                                            )"
                                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 flex items-center gap-2.5"
                                        >
                                            <i class="fas fa-pen text-amber-400 w-4"></i>
                                            Editar Producto
                                        </button>
                                    </div>
                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <i class="fas fa-boxes-stacked text-4xl text-gray-200"></i>
                                    <p class="text-sm font-medium">No se encontraron productos</p>
                                    <p class="text-xs">Intenta ajustar los filtros o agrega un nuevo producto</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- FOOTER: contador + paginacion --}}
        <div class="px-6 py-4 border-t border-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Mostrando {{ $productos->count() }} producto{{ $productos->count() !== 1 ? 's' : '' }}
                @if($productos->total() !== $productos->count())
                    de {{ $productos->total() }}
                @endif
            </p>
            @if($productos->hasPages())
                <div>
                    {{ $productos->links() }}
                </div>
            @endif
        </div>

    </div>


    {{-- ============================================= --}}
    {{-- BANNER CONSEJO --}}
    {{-- ============================================= --}}

    <div class="bg-green-50 border border-green-100 rounded-2xl px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0 mt-0.5">
                <i class="fas fa-lightbulb text-green-500 text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">Consejo</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    Manten tu <a href="{{ route('admin.inventario.sugerencias') }}" class="text-green-600 font-medium hover:underline">inventario actualizado</a> para evitar faltantes y optimizar costos.
                </p>
            </div>
        </div>
        <a
            href="{{ route('admin.inventario.sugerencias') }}"
            class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm whitespace-nowrap"
        >
            Ver Reportes de Inventario
        </a>
    </div>

</div>


{{-- ============================================================ --}}
{{-- MODAL: ACTUALIZAR STOCK --}}
{{-- ============================================================ --}}

<div id="modalStock" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

        <div class="px-7 py-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-heading text-xl font-bold text-gray-900">Actualizar Stock</h3>
                <p id="modalStockNombre" class="text-sm text-gray-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="cerrarModal('modalStock')" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="formStock" method="POST">
            @csrf
            <div class="px-7 py-6 space-y-5">

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-500">Stock actual</span>
                    <span id="stockActualLabel" class="text-lg font-bold text-gray-800">--</span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de movimiento</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-green-400 transition">
                            <input type="radio" name="tipo" value="entrada" class="accent-green-500" required>
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Entrada</p>
                                <p class="text-xs text-gray-400">Agregar stock</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-red-400 transition">
                            <input type="radio" name="tipo" value="salida" class="accent-red-500" required>
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Salida</p>
                                <p class="text-xs text-gray-400">Descontar stock</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Cantidad <span id="unidadLabel" class="text-gray-400 font-normal text-xs"></span>
                    </label>
                    <input
                        type="number"
                        name="cantidad"
                        min="1"
                        required
                        placeholder="Ej: 10"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Motivo <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <input
                        type="text"
                        name="motivo"
                        placeholder="Ej: Compra semanal"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm"
                    >
                </div>

            </div>

            <div class="px-7 py-5 border-t border-gray-100 flex gap-3">
                <button type="button" onclick="cerrarModal('modalStock')" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white text-sm font-semibold transition">
                    Registrar Movimiento
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ============================================================ --}}
{{-- MODAL: HISTORIAL --}}
{{-- ============================================================ --}}

<div id="modalHistorial" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">

        <div class="px-7 py-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-heading text-xl font-bold text-gray-900">Historial de Movimientos</h3>
                <p id="modalHistorialNombre" class="text-sm text-gray-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="cerrarModal('modalHistorial')" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <div id="historialBody" class="px-7 py-5 max-h-96 overflow-y-auto space-y-3">
            <div class="text-center py-10 text-gray-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                <p class="text-sm">Cargando historial...</p>
            </div>
        </div>

        <div class="px-7 py-5 border-t border-gray-100">
            <button type="button" onclick="cerrarModal('modalHistorial')" class="w-full py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                Cerrar
            </button>
        </div>

    </div>
</div>


{{-- ============================================================ --}}
{{-- MODAL: EDITAR PRODUCTO --}}
{{-- ============================================================ --}}

<div id="modalEditar" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-4 overflow-hidden">

        {{-- CABECERA --}}
        <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Editar Producto</h3>
            <button type="button" onclick="cerrarModal('modalEditar')" class="text-gray-400 hover:text-gray-700 text-xl leading-none transition">&times;</button>
        </div>

        <form id="formEditar" method="POST">
            @csrf
            @method('PUT')

            <div class="px-7 py-6 space-y-5">

                {{-- FILA 1: Nombre + Categoría --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre del Producto</label>
                        <input
                            type="text"
                            name="nombre"
                            id="editarNombre"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Categoría</label>
                        <select
                            name="categoria_id"
                            id="editarCategoria"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm bg-white"
                        >
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- FILA 2: Precio Unitario + Unidad --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Precio Unitario ($)</label>
                        <input
                            type="number"
                            name="precio"
                            id="editarPrecio"
                            min="0"
                            step="0.01"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unidad (ej. kg, L, und)</label>
                        <input
                            type="text"
                            name="unidad"
                            id="editarUnidad"
                            placeholder="Ej: kg, L, und"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                        >
                    </div>
                </div>

                {{-- FILA 3: Stock Actual + Stock Mínimo --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stock Actual</label>
                        <input
                            type="number"
                            name="cantidad"
                            id="editarCantidad"
                            min="0"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stock Mínimo</label>
                        <input
                            type="number"
                            name="stock_minimo"
                            id="editarMinimo"
                            min="0"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                        >
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="px-7 py-5 border-t border-gray-100 flex justify-end gap-3">
                <button
                    type="button"
                    onclick="cerrarModal('modalEditar')"
                    class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition shadow-sm"
                >
                    Actualizar Producto
                </button>
            </div>
        </form>
    </div>
</div>


@endsection

@section('scripts')
<script>

    // ===== BUSQUEDA CON ENTER =====
    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') document.getElementById('filtrosForm').submit();
    });

    // ===== TOGGLE MENU DROPDOWN =====
    let menuAbierto = null;

    function toggleMenu(id) {
        const menu = document.getElementById('menu-' + id);
        if (menuAbierto && menuAbierto !== menu) {
            menuAbierto.classList.add('hidden');
            menuAbierto.classList.remove('block');
        }
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            menu.classList.add('block');
            menuAbierto = menu;
        } else {
            menu.classList.add('hidden');
            menu.classList.remove('block');
            menuAbierto = null;
        }
    }

    document.addEventListener('click', function(e) {
        if (menuAbierto) {
            const btn = e.target.closest('button[onclick^="toggleMenu"]');
            const men = e.target.closest('[id^="menu-"]');
            if (!btn && !men) {
                menuAbierto.classList.add('hidden');
                menuAbierto.classList.remove('block');
                menuAbierto = null;
            }
        }
    });

    // ===== MODALES: ABRIR / CERRAR =====
    function cerrarModal(id) {
        const m = document.getElementById(id);
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    function abrirModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    ['modalStock', 'modalHistorial', 'modalEditar'].forEach(function(id) {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) cerrarModal(id);
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['modalStock','modalHistorial','modalEditar'].forEach(cerrarModal);
        }
    });

    // ===== MODAL: ACTUALIZAR STOCK =====
    function abrirModalStock(productoId, nombre, stockActual, unidad) {
        if (menuAbierto) { menuAbierto.classList.add('hidden'); menuAbierto.classList.remove('block'); menuAbierto = null; }

        document.getElementById('modalStockNombre').textContent = nombre;
        document.getElementById('stockActualLabel').textContent = stockActual + (unidad && unidad !== '--' ? ' ' + unidad : '');
        document.getElementById('unidadLabel').textContent = unidad && unidad !== '--' ? '(' + unidad + ')' : '';

        const form = document.getElementById('formStock');
        form.action = '/admin/inventario/' + productoId + '/stock';
        form.reset();

        abrirModal('modalStock');
    }

    // ===== MODAL: HISTORIAL =====
    function verHistorial(productoId, nombre) {
        if (menuAbierto) { menuAbierto.classList.add('hidden'); menuAbierto.classList.remove('block'); menuAbierto = null; }

        document.getElementById('modalHistorialNombre').textContent = nombre;
        document.getElementById('historialBody').innerHTML =
            '<div class="text-center py-10 text-gray-400"><i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i><p class="text-sm">Cargando historial...</p></div>';

        abrirModal('modalHistorial');

        fetch('/admin/inventario/' + productoId + '/historial')
            .then(r => r.json())
            .then(data => {
                if (!data.length) {
                    document.getElementById('historialBody').innerHTML =
                        '<div class="text-center py-10 text-gray-300"><i class="fas fa-clock-rotate-left text-3xl mb-2 block"></i><p class="text-sm">Sin movimientos registrados</p></div>';
                    return;
                }
                document.getElementById('historialBody').innerHTML = data.map(m => {
                    const isEntrada = m.tipo === 'entrada';
                    const colorIcon = isEntrada ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500';
                    const iconName  = isEntrada ? 'fa-arrow-down' : 'fa-arrow-up';
                    const colorAmt  = isEntrada ? 'text-green-600' : 'text-red-500';
                    const sign      = isEntrada ? '+' : '-';
                    return `<div class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 ${colorIcon}">
                            <i class="fas ${iconName} text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-semibold text-gray-700 capitalize">${m.tipo}</span>
                                <span class="text-xs font-bold ${colorAmt}">${sign}${m.cantidad}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">${m.motivo ?? 'Sin motivo'}</p>
                            <p class="text-gray-300 mt-1" style="font-size:10px">${m.fecha}</p>
                        </div>
                    </div>`;
                }).join('');
            })
            .catch(() => {
                document.getElementById('historialBody').innerHTML =
                    '<div class="text-center py-10 text-red-400"><i class="fas fa-circle-exclamation text-2xl mb-2 block"></i><p class="text-sm">Error al cargar el historial</p></div>';
            });
    }

    // ===== MODAL: EDITAR PRODUCTO =====
    function abrirModalEditar(productoId, nombre, categoriaId, precio, unidad, cantidad, minimo) {
        if (menuAbierto) { menuAbierto.classList.add('hidden'); menuAbierto.classList.remove('block'); menuAbierto = null; }

        document.getElementById('editarNombre').value   = nombre;
        document.getElementById('editarPrecio').value   = precio;
        document.getElementById('editarUnidad').value   = (unidad && unidad !== '--') ? unidad : '';
        document.getElementById('editarCantidad').value = cantidad;
        document.getElementById('editarMinimo').value   = minimo;

        // Seleccionar categoría
        const sel = document.getElementById('editarCategoria');
        if (categoriaId) {
            sel.value = categoriaId;
        }

        document.getElementById('formEditar').action = '/admin/inventario/' + productoId + '/editar-minimos';

        abrirModal('modalEditar');
    }

    // ===== EXPORTAR CSV =====
    function exportarCSV() {
        const filas = [['Producto','ID','Categoria','Unidad','Stock Actual','Stock Minimo','Estado','Valor Unitario','Valor Total']];
        document.querySelectorAll('tbody tr[id^="fila-"]').forEach(tr => {
            const td = tr.querySelectorAll('td');
            if (!td.length) return;
            filas.push([
                td[0]?.querySelector('p.font-semibold')?.textContent?.trim() ?? '',
                td[0]?.querySelector('p.text-gray-400')?.textContent?.trim() ?? '',
                td[1]?.textContent?.trim() ?? '',
                td[2]?.textContent?.trim() ?? '',
                td[3]?.textContent?.trim() ?? '',
                td[4]?.textContent?.trim() ?? '',
                td[5]?.textContent?.trim() ?? '',
                td[6]?.textContent?.trim() ?? '',
                td[7]?.textContent?.trim() ?? '',
            ]);
        });
        const csv  = filas.map(r => r.map(v => '"' + v.replace(/"/g,'""') + '"').join(',')).join('\n');
        const blob = new Blob(['\uFEFF' + csv], {type:'text/csv;charset=utf-8;'});
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href = url; a.download = 'inventario_' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
        URL.revokeObjectURL(url);
    }

</script>
@endsection
