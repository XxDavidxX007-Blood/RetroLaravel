@extends('layouts.admin')

@section('title', 'Gestión de Inventario | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="w-7 h-7 rounded-full bg-retro-gold/20 text-retro-gold flex items-center justify-center hover:bg-retro-gold hover:text-black transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <span class="text-gray-400">|</span>
        <span>GESTIÓN DE INVENTARIO</span>
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-8">

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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        <!-- Total Productos -->
        <div class="bg-white border-2 border-black rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</p>
                    <h3 class="text-2xl font-extrabold text-gray-900 mt-1 font-heading">{{ $totalProductos }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-gray-800 text-lg">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
        </div>

        <!-- Valor en Stock -->
        <div class="bg-white border-2 border-black rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Stock</p>
                    <h3 class="text-2xl font-extrabold text-gray-900 mt-1 font-heading">${{ number_format($valorStock, 0, ',', '.') }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-[#fef3c7] flex items-center justify-center text-[#d97706] text-lg font-bold">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white border-2 border-black rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Bajo</p>
                    <h3 class="text-2xl font-extrabold text-amber-600 mt-1 font-heading">{{ $stockBajo }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 text-lg">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>

        <!-- Sin Stock -->
        <div class="bg-white border-2 border-black rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sin Stock</p>
                    <h3 class="text-2xl font-extrabold text-rose-600 mt-1 font-heading">{{ $sinStock }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 text-lg">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
        </div>

        <!-- Operaciones Mes -->
        <div class="bg-white border-2 border-black rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Movimientos Mes</p>
                    <h3 class="text-2xl font-extrabold text-blue-600 mt-1 font-heading">{{ $operacionesMes }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 text-lg">
                    <i class="fas fa-arrow-right-arrow-left"></i>
                </div>
            </div>
        </div>

    </div>


    <!-- ================================================= -->
    <!-- TABLA PRINCIPAL DE INVENTARIO -->
    <!-- ================================================= -->
    <div class="bg-white border-2 border-black rounded-3xl p-7 shadow-xl space-y-6">

        <!-- CABECERA -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-boxes-stacked text-2xl text-black"></i>
                <h2 class="font-heading text-2xl font-bold tracking-tight text-gray-900 uppercase">
                    INVENTARIO DE PRODUCTOS
                </h2>
            </div>

            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('admin.inventario.sugerencias') }}"
                    class="bg-[#fef3c7] text-[#92400e] hover:bg-[#fde68a] font-semibold px-4 py-2 rounded-full flex items-center gap-2 border border-[#f59e0b] transition text-xs uppercase tracking-wider"
                >
                    <i class="fas fa-lightbulb text-[#d97706]"></i>
                    <span>Sugerencias de Reposición</span>
                    @if($stockBajo + $sinStock > 0)
                        <span class="bg-[#d97706] text-white rounded-full px-1.5 py-0.2 text-[10px]">{{ $stockBajo + $sinStock }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- FILTROS Y BUSCADOR -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-2">
            <form method="GET" action="{{ route('admin.inventario.index') }}" class="w-full flex flex-col md:flex-row items-center gap-3">
                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Buscar por producto o categoría..." 
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-black"
                    >
                </div>

                <select name="categoria_id" onchange="this.form.submit()" class="w-full md:w-48 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="">Todas las Categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>

                <select name="filtro_stock" onchange="this.form.submit()" class="w-full md:w-48 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="">Todos los Estados</option>
                    <option value="con_stock" {{ request('filtro_stock') == 'con_stock' ? 'selected' : '' }}>Con Stock Normal</option>
                    <option value="stock_bajo" {{ request('filtro_stock') == 'stock_bajo' ? 'selected' : '' }}>Stock Bajo</option>
                    <option value="sin_stock" {{ request('filtro_stock') == 'sin_stock' ? 'selected' : '' }}>Sin Stock</option>
                </select>

                @if(request()->hasAny(['search', 'categoria_id', 'filtro_stock']))
                    <a href="{{ route('admin.inventario.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- TABLA -->
        <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#0a0a0a] text-retro-gold font-heading text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 font-semibold">Producto</th>
                        <th class="py-4 px-6 font-semibold">Categoría</th>
                        <th class="py-4 px-6 font-semibold">Precio Unit.</th>
                        <th class="py-4 px-6 font-semibold">Stock Actual</th>
                        <th class="py-4 px-6 font-semibold">Límites (Mín/Máx)</th>
                        <th class="py-4 px-6 font-semibold">Estado</th>
                        <th class="py-4 px-6 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-sm">
                    @forelse($productos as $producto)
                        @php
                            $cant = $producto->inventario->cantidad ?? 0;
                            $min = $producto->inventario->stock_minimo ?? 0;
                            $max = $producto->inventario->stock_maximo ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <!-- Producto -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-600 shrink-0 font-bold">
                                        <i class="fas fa-utensils text-sm"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900 block">
                                            {{ $producto->nombre }}
                                        </span>
                                        <span class="text-xs text-gray-400">ID: #{{ $producto->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Categoría -->
                            <td class="py-4 px-6 text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </td>

                            <!-- Precio -->
                            <td class="py-4 px-6 font-semibold text-gray-900">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </td>

                            <!-- Stock Actual -->
                            <td class="py-4 px-6">
                                <span class="text-base font-extrabold {{ $cant == 0 ? 'text-rose-600' : ($cant <= $min ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $cant }}
                                </span>
                                <span class="text-xs text-gray-400">uds</span>
                            </td>

                            <!-- Límites -->
                            <td class="py-4 px-6 text-gray-500 text-xs">
                                <span>Mín: <strong>{{ $min }}</strong></span>
                                @if($max)
                                    <span class="text-gray-300 mx-1">|</span>
                                    <span>Máx: <strong>{{ $max }}</strong></span>
                                @endif
                            </td>

                            <!-- Estado -->
                            <td class="py-4 px-6">
                                @if($cant == 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Agotado
                                    </span>
                                @elseif($cant <= $min)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Stock Bajo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Disponible
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Ajustar Stock -->
                                    <button 
                                        type="button"
                                        onclick="openStockModal({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', {{ $cant }})"
                                        class="px-3 py-1.5 rounded-xl bg-[#0a0a0a] text-white hover:bg-black hover:text-retro-gold flex items-center gap-1.5 text-xs font-semibold transition shadow-sm"
                                        title="Ajustar Stock"
                                    >
                                        <i class="fas fa-plus-minus text-[10px]"></i>
                                        <span>Ajustar</span>
                                    </button>

                                    <!-- Ver Historial -->
                                    <button 
                                        type="button"
                                        onclick="openHistorialModal({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')"
                                        class="w-8 h-8 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center transition shadow-sm"
                                        title="Historial de movimientos"
                                    >
                                        <i class="fas fa-clock-rotate-left text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <i class="fas fa-box-open text-3xl mb-2 text-gray-300 block"></i>
                                No se encontraron productos en el inventario.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        @if($productos->hasPages())
            <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-500 font-medium">
                    Mostrando <span class="font-bold text-gray-900">{{ $productos->firstItem() }}</span> a <span class="font-bold text-gray-900">{{ $productos->lastItem() }}</span> de <span class="font-bold text-gray-900">{{ $productos->total() }}</span> productos
                </p>
                <div class="pagination-custom">
                    {{ $productos->links() }}
                </div>
            </div>
        @endif

    </div>

</div>


<!-- ================================================= -->
<!-- MODAL: AJUSTAR STOCK -->
<!-- ================================================= -->
<div id="stockModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-md w-full p-7 shadow-2xl space-y-6 relative animate-fade-in">
        
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-boxes-packing text-sm"></i>
                </div>
                <div>
                    <h3 class="font-heading text-lg font-bold uppercase tracking-wider text-gray-900">Ajustar Stock</h3>
                    <p id="modal_producto_nombre" class="text-xs text-gray-500 font-medium"></p>
                </div>
            </div>
            <button onclick="closeStockModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form id="stockForm" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-xs font-semibold uppercase text-gray-600 block mb-1">Tipo de Movimiento</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-emerald-500 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 transition">
                        <input type="radio" name="tipo" value="entrada" checked class="accent-emerald-600">
                        <span class="text-xs font-bold text-emerald-700 uppercase"><i class="fas fa-arrow-down mr-1"></i> Entrada</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-rose-500 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 transition">
                        <input type="radio" name="tipo" value="salida" class="accent-rose-600">
                        <span class="text-xs font-bold text-rose-700 uppercase"><i class="fas fa-arrow-up mr-1"></i> Salida</span>
                    </label>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600">Cantidad</label>
                <input type="number" name="cantidad" min="1" required placeholder="Ej. 10" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                <p class="text-[11px] text-gray-400">Stock actual: <span id="modal_stock_actual" class="font-bold text-gray-700">0</span> unidades</p>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600">Motivo / Observación (Opcional)</label>
                <input type="text" name="motivo" placeholder="Ej. Compra a proveedor, Merma, Ajuste manual" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeStockModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#0a0a0a] text-white hover:bg-black border border-black hover:border-retro-gold transition shadow">
                    Guardar Ajuste
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ================================================= -->
<!-- MODAL: HISTORIAL DE MOVIMIENTOS -->
<!-- ================================================= -->
<div id="historialModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-2xl w-full p-7 shadow-2xl space-y-6 relative animate-fade-in">
        
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-clock-rotate-left text-sm"></i>
                </div>
                <div>
                    <h3 class="font-heading text-lg font-bold uppercase tracking-wider text-gray-900">Historial de Movimientos</h3>
                    <p id="historial_producto_nombre" class="text-xs text-gray-500 font-medium"></p>
                </div>
            </div>
            <button onclick="closeHistorialModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div id="historialContenido" class="max-h-80 overflow-y-auto space-y-3 pr-2">
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-spinner fa-spin text-2xl"></i>
                <p class="text-xs mt-2">Cargando movimientos...</p>
            </div>
        </div>

        <div class="pt-4 flex justify-end border-t border-gray-100">
            <button type="button" onclick="closeHistorialModal()" class="px-5 py-2 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                Cerrar
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openStockModal(productoId, productoNombre, stockActual) {
        const form = document.getElementById('stockForm');
        form.action = `/admin/inventario/${productoId}/stock`;
        
        document.getElementById('modal_producto_nombre').textContent = productoNombre;
        document.getElementById('modal_stock_actual').textContent = stockActual;

        document.getElementById('stockModal').classList.remove('hidden');
    }

    function closeStockModal() {
        document.getElementById('stockModal').classList.add('hidden');
    }

    function openHistorialModal(productoId, productoNombre) {
        document.getElementById('historial_producto_nombre').textContent = productoNombre;
        const contenedor = document.getElementById('historialContenido');
        contenedor.innerHTML = `
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-spinner fa-spin text-2xl"></i>
                <p class="text-xs mt-2">Cargando movimientos...</p>
            </div>
        `;
        document.getElementById('historialModal').classList.remove('hidden');

        fetch(`/admin/inventario/${productoId}/historial`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    contenedor.innerHTML = `
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-box-open text-3xl mb-2 text-gray-300 block"></i>
                            <p class="text-sm">No hay movimientos registrados para este producto.</p>
                        </div>
                    `;
                    return;
                }

                let html = '<div class="divide-y divide-gray-100">';
                data.forEach(m => {
                    const isEntrada = m.tipo === 'entrada';
                    const badgeClass = isEntrada ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700';
                    const icon = isEntrada ? 'fa-arrow-down' : 'fa-arrow-up';

                    html += `
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold ${badgeClass}">
                                    <i class="fas ${icon}"></i>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 capitalize">${m.tipo}: ${m.cantidad} unidades</p>
                                    <p class="text-[11px] text-gray-400">${m.motivo ? m.motivo : 'Sin motivo especificado'}</p>
                                </div>
                            </div>
                            <span class="text-[11px] text-gray-400 font-mono">${m.fecha}</span>
                        </div>
                    `;
                });
                html += '</div>';
                contenedor.innerHTML = html;
            })
            .catch(err => {
                contenedor.innerHTML = `
                    <div class="text-center py-6 text-rose-500 text-xs">
                        <i class="fas fa-circle-exclamation text-lg mb-1 block"></i>
                        Error al cargar el historial.
                    </div>
                `;
            });
    }

    function closeHistorialModal() {
        document.getElementById('historialModal').classList.add('hidden');
    }
</script>
@endsection