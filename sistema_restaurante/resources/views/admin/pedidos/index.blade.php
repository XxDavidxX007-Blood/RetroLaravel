@extends('layouts.admin')

@section('title', 'Gestión de Pedidos | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="w-7 h-7 rounded-full bg-retro-gold/20 text-retro-gold flex items-center justify-center hover:bg-retro-gold hover:text-black transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <span class="text-gray-400">|</span>
        <span>PEDIDOS</span>
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-7">

    <!-- CABECERA PRINCIPAL -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <i class="fas fa-receipt text-3xl text-gray-900"></i>
                <h1 class="font-heading text-3xl font-bold text-gray-900 tracking-tight">
                    Pedidos
                </h1>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Gestiona y supervisa todos los pedidos del restaurante
            </p>
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

        <!-- Pedidos hoy -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#dbeafe] flex items-center justify-center text-[#2563eb] text-xl shrink-0">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Pedidos hoy</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $pedidosHoy }}</h3>
                <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">+0% que ayer</p>
            </div>
        </div>

        <!-- En preparación -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#fef3c7] flex items-center justify-center text-[#d97706] text-xl shrink-0">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">En preparación</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $enPreparacion }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Pedidos activos</p>
            </div>
        </div>

        <!-- Listos para entregar -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#e0e7ff] flex items-center justify-center text-[#4f46e5] text-xl shrink-0">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Listos para entregar</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $listos }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Listos para servir</p>
            </div>
        </div>

        <!-- Completados hoy -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#dcfce7] flex items-center justify-center text-[#16a34a] text-xl shrink-0">
                <i class="fas fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Completados hoy</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $completadosHoy }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Pedidos entregados</p>
            </div>
        </div>

        <!-- Cancelados hoy -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-[#ffe4e6] flex items-center justify-center text-[#e11d48] text-xl shrink-0">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-500">Cancelados hoy</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $canceladosHoy }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Pedidos cancelados</p>
            </div>
        </div>

    </div>

    <!-- ================================================= -->
    <!-- TABS DE ESTADO Y FILTRO POR FECHA -->
    <!-- ================================================= -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Tabs de Estado -->
        @php
            $currentEstado = request('estado', 'todos');
        @endphp
        <div class="flex items-center gap-1 sm:gap-4 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 text-sm font-medium border-b md:border-b-0 border-gray-100">
            <a 
                href="{{ route('admin.pedidos.index', array_merge(request()->except('estado', 'page'), ['estado' => 'todos'])) }}" 
                class="px-3 py-1.5 transition whitespace-nowrap {{ $currentEstado === 'todos' ? 'text-black font-bold border-b-2 border-black -mb-[2px]' : 'text-gray-500 hover:text-gray-900' }}"
            >
                Todos
            </a>
            <a 
                href="{{ route('admin.pedidos.index', array_merge(request()->except('estado', 'page'), ['estado' => 'pendiente'])) }}" 
                class="px-3 py-1.5 transition whitespace-nowrap {{ $currentEstado === 'pendiente' ? 'text-black font-bold border-b-2 border-black -mb-[2px]' : 'text-gray-500 hover:text-gray-900' }}"
            >
                Pendiente
            </a>
            <a 
                href="{{ route('admin.pedidos.index', array_merge(request()->except('estado', 'page'), ['estado' => 'en_preparacion'])) }}" 
                class="px-3 py-1.5 transition whitespace-nowrap {{ ($currentEstado === 'en_preparacion' || $currentEstado === 'en preparacion') ? 'text-black font-bold border-b-2 border-black -mb-[2px]' : 'text-gray-500 hover:text-gray-900' }}"
            >
                En preparacion
            </a>
            <a 
                href="{{ route('admin.pedidos.index', array_merge(request()->except('estado', 'page'), ['estado' => 'listo'])) }}" 
                class="px-3 py-1.5 transition whitespace-nowrap {{ $currentEstado === 'listo' ? 'text-black font-bold border-b-2 border-black -mb-[2px]' : 'text-gray-500 hover:text-gray-900' }}"
            >
                Listo
            </a>
            <a 
                href="{{ route('admin.pedidos.index', array_merge(request()->except('estado', 'page'), ['estado' => 'entregado'])) }}" 
                class="px-3 py-1.5 transition whitespace-nowrap {{ $currentEstado === 'entregado' ? 'text-black font-bold border-b-2 border-black -mb-[2px]' : 'text-gray-500 hover:text-gray-900' }}"
            >
                Entregado
            </a>
            <a 
                href="{{ route('admin.pedidos.index', array_merge(request()->except('estado', 'page'), ['estado' => 'cancelado'])) }}" 
                class="px-3 py-1.5 transition whitespace-nowrap {{ $currentEstado === 'cancelado' ? 'text-black font-bold border-b-2 border-black -mb-[2px]' : 'text-gray-500 hover:text-gray-900' }}"
            >
                Cancelado
            </a>
        </div>

        <!-- Filtro por Fecha y Buscador -->
        <form method="GET" action="{{ route('admin.pedidos.index') }}" class="flex items-center gap-2 w-full md:w-auto justify-end">
            @if(request('estado'))
                <input type="hidden" name="estado" value="{{ request('estado') }}">
            @endif

            <div class="relative">
                <input 
                    type="date" 
                    name="fecha" 
                    value="{{ request('fecha') }}"
                    onchange="this.form.submit()"
                    class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-black text-gray-700 shadow-xs cursor-pointer"
                >
            </div>

            @if(request()->hasAny(['fecha', 'estado', 'search']) && (request('estado') !== 'todos' || request('fecha') || request('search')))
                <a href="{{ route('admin.pedidos.index') }}" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition" title="Limpiar filtros">
                    <i class="fas fa-filter-circle-xmark"></i>
                </a>
            @endif
        </form>

    </div>

    <!-- ================================================= -->
    <!-- TABLA DE PEDIDOS -->
    <!-- ================================================= -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-600 font-heading text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="py-4 px-6 font-semibold">PEDIDO</th>
                        <th class="py-4 px-6 font-semibold">CLIENTE</th>
                        <th class="py-4 px-6 font-semibold">MESA / DELIVERY</th>
                        <th class="py-4 px-6 font-semibold">ESTADO</th>
                        <th class="py-4 px-6 font-semibold">TOTAL</th>
                        <th class="py-4 px-6 font-semibold">FECHA</th>
                        <th class="py-4 px-6 font-semibold text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-sm">
                    @forelse($pedidos as $p)
                        @php
                            $nombreEstado = strtolower($p->estadoPedido->nombre_estado ?? 'pendiente');
                            $codigo = '#ORD-' . str_pad($p->id, 5, '0', STR_PAD_LEFT);
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <!-- Pedido -->
                            <td class="py-4 px-6">
                                <span class="font-bold text-gray-900 font-mono text-sm block">
                                    {{ $codigo }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $p->created_at ? $p->created_at->format('d/m/Y') : '' }}
                                </span>
                            </td>

                            <!-- Cliente -->
                            <td class="py-4 px-6">
                                <span class="font-bold text-gray-900 capitalize block">
                                    {{ strtolower($p->cliente->user->name ?? 'Cliente Anónimo') }}
                                </span>
                                <span class="text-xs text-gray-400 font-mono">
                                    {{ $p->cliente->user->telefono ?? '3651916255' }}
                                </span>
                            </td>

                            <!-- Mesa / Delivery -->
                            <td class="py-4 px-6 text-gray-700">
                                <span class="font-medium capitalize block">
                                    {{ strtolower($p->tipoPedido->nombre ?? 'mesa') }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ Str::contains(strtolower($p->observaciones ?? ''), 'salón') || Str::contains(strtolower($p->observaciones ?? ''), 'salon') ? 'Salón' : ($p->observaciones ? Str::limit($p->observaciones, 20) : 'Salón') }}
                                </span>
                            </td>

                            <!-- Estado -->
                            <td class="py-4 px-6">
                                @if(Str::contains($nombreEstado, 'pendiente'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span>
                                        Pendiente
                                    </span>
                                @elseif(Str::contains($nombreEstado, 'preparaci'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#dbeafe] text-[#2563eb]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span>
                                        En preparación
                                    </span>
                                @elseif(Str::contains($nombreEstado, 'listo'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#e0e7ff] text-[#4f46e5]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#4f46e5]"></span>
                                        Listo
                                    </span>
                                @elseif(Str::contains($nombreEstado, 'entregado'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#16a34a]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                                        Entregado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#ffe4e6] text-[#e11d48]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#e11d48]"></span>
                                        Cancelado
                                    </span>
                                @endif
                            </td>

                            <!-- Total -->
                            <td class="py-4 px-6 font-bold text-emerald-600 font-mono text-sm">
                                ${{ number_format($p->total, 0, ',', '.') }}
                            </td>

                            <!-- Fecha -->
                            <td class="py-4 px-6 text-gray-600 text-xs font-mono">
                                {{ $p->created_at ? $p->created_at->format('d/m/Y') : 'N/A' }}
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Ver Detalles -->
                                    <button 
                                        type="button"
                                        onclick="openOrderDetailsModal({{ $p->id }})"
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center transition shadow-xs"
                                        title="Ver detalles del pedido"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    <!-- Cambiar Estado -->
                                    <button 
                                        type="button"
                                        onclick="openChangeStatusModal({{ $p->id }}, '{{ $codigo }}', {{ $p->estado_pedido_id }})"
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center transition shadow-xs"
                                        title="Cambiar estado del pedido"
                                    >
                                        <i class="fas fa-ellipsis-vertical text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <i class="fas fa-receipt text-4xl mb-3 text-gray-300 block"></i>
                                No se encontraron pedidos con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PIE DE TABLA Y PAGINACIÓN -->
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-500 font-medium">
                Mostrando <span class="font-bold text-gray-900">{{ $pedidos->firstItem() ?? 0 }}</span> a <span class="font-bold text-gray-900">{{ $pedidos->lastItem() ?? 0 }}</span> de <span class="font-bold text-gray-900">{{ $pedidos->total() }}</span> pedidos
            </p>
            @if($pedidos->hasPages())
                <div class="pagination-custom">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    </div>

</div>


<!-- ================================================= -->
<!-- MODAL: DETALLES DEL PEDIDO -->
<!-- ================================================= -->
<div id="orderDetailsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-xl w-full p-7 shadow-2xl space-y-6 relative animate-fade-in">
        
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
                <div>
                    <h3 id="modal_pedido_codigo" class="font-heading text-xl font-bold uppercase tracking-wider text-gray-900">#ORD-00000</h3>
                    <p id="modal_pedido_fecha" class="text-xs text-gray-500"></p>
                </div>
            </div>
            <button onclick="closeOrderDetailsModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <!-- Info Cliente y Tipo -->
        <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100 text-xs">
            <div>
                <p class="text-gray-400 font-semibold uppercase">Cliente</p>
                <p id="modal_cliente_nombre" class="font-bold text-gray-900 text-sm mt-0.5"></p>
                <p id="modal_cliente_telefono" class="text-gray-500 mt-0.5"></p>
            </div>
            <div>
                <p class="text-gray-400 font-semibold uppercase">Tipo / Estado</p>
                <p id="modal_pedido_tipo" class="font-bold text-gray-900 text-sm mt-0.5"></p>
                <p id="modal_pedido_estado" class="font-semibold text-emerald-600 mt-0.5"></p>
            </div>
        </div>

        <!-- Lista de Productos -->
        <div>
            <h4 class="text-xs font-bold uppercase text-gray-500 tracking-wider mb-2">Detalle de Platillos</h4>
            <div class="max-h-52 overflow-y-auto border border-gray-100 rounded-2xl overflow-hidden">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="py-2.5 px-3">Platillo</th>
                            <th class="py-2.5 px-3 text-center">Cant.</th>
                            <th class="py-2.5 px-3 text-right">Precio</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="modal_detalles_tbody" class="divide-y divide-gray-100">
                        <!-- JS inyectará items -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Observaciones y Total -->
        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
            <div>
                <p class="text-[11px] text-gray-400 uppercase font-semibold">Observaciones:</p>
                <p id="modal_pedido_obs" class="text-xs text-gray-600 italic">Sin observaciones</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] text-gray-400 uppercase font-semibold">Total a Pagar</p>
                <p id="modal_pedido_total" class="text-2xl font-extrabold text-gray-900 font-heading text-emerald-600">$0</p>
            </div>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
            <button type="button" onclick="closeOrderDetailsModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                Cerrar
            </button>
        </div>
    </div>
</div>


<!-- ================================================= -->
<!-- MODAL: CAMBIAR ESTADO DEL PEDIDO -->
<!-- ================================================= -->
<div id="changeStatusModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-md w-full p-7 shadow-2xl space-y-6 relative animate-fade-in">
        
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-arrows-rotate text-sm"></i>
                </div>
                <div>
                    <h3 class="font-heading text-lg font-bold uppercase tracking-wider text-gray-900">Actualizar Estado</h3>
                    <p id="status_pedido_codigo" class="text-xs text-gray-500 font-medium"></p>
                </div>
            </div>
            <button onclick="closeChangeStatusModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form id="changeStatusForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600 block mb-1">Seleccionar Nuevo Estado</label>
                <select id="status_select_estado" name="estado_pedido_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    @foreach($estados as $e)
                        <option value="{{ $e->id }}">{{ $e->nombre_estado }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeChangeStatusModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#0a0a0a] text-white hover:bg-black border border-black hover:border-retro-gold transition shadow">
                    Guardar Estado
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openOrderDetailsModal(pedidoId) {
        const tbody = document.getElementById('modal_detalles_tbody');
        tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-gray-400">Cargando detalles...</td></tr>';

        document.getElementById('orderDetailsModal').classList.remove('hidden');

        fetch(`/admin/pedidos/${pedidoId}/detalles`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modal_pedido_codigo').textContent = data.codigo;
                document.getElementById('modal_pedido_fecha').textContent = data.fecha;
                document.getElementById('modal_cliente_nombre').textContent = data.cliente;
                document.getElementById('modal_cliente_telefono').textContent = data.telefono;
                document.getElementById('modal_pedido_tipo').textContent = data.tipo;
                document.getElementById('modal_pedido_estado').textContent = data.estado;
                document.getElementById('modal_pedido_obs').textContent = data.observaciones || 'Sin observaciones';
                document.getElementById('modal_pedido_total').textContent = data.total;

                if (data.detalles && data.detalles.length > 0) {
                    let html = '';
                    data.detalles.forEach(d => {
                        html += `
                            <tr>
                                <td class="py-2.5 px-3 font-semibold text-gray-900">${d.producto}</td>
                                <td class="py-2.5 px-3 text-center text-gray-700 font-bold">${d.cantidad}</td>
                                <td class="py-2.5 px-3 text-right text-gray-600 font-mono">${d.precio_unitario}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-gray-900 font-mono">${d.subtotal}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-gray-400">No hay productos registrados en este pedido.</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-rose-500">Error al cargar los detalles.</td></tr>';
            });
    }

    function closeOrderDetailsModal() {
        document.getElementById('orderDetailsModal').classList.add('hidden');
    }

    function openChangeStatusModal(pedidoId, codigo, estadoId) {
        const form = document.getElementById('changeStatusForm');
        form.action = `/admin/pedidos/${pedidoId}/estado`;

        document.getElementById('status_pedido_codigo').textContent = codigo;
        document.getElementById('status_select_estado').value = estadoId;

        document.getElementById('changeStatusModal').classList.remove('hidden');
    }

    function closeChangeStatusModal() {
        document.getElementById('changeStatusModal').classList.add('hidden');
    }
</script>
@endsection
