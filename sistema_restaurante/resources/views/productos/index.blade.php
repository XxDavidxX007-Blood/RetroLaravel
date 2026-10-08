@extends('layouts.App')

@section('title', 'Catálogo de Menú | Retro Restaurant')

@section('header', 'CATÁLOGO DE PRODUCTOS')

@push('styles')
<style>
    /* Estilos del catálogo con información real */
    .catalogo-hero {
        background: linear-gradient(135deg, #0f1524 0%, #151c2e 60%, #0a0e18 100%);
        border: 1px solid rgba(197, 160, 89, 0.25);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .retro-badge-gold {
        background: rgba(197, 160, 89, 0.15);
        border: 1px solid rgba(197, 160, 89, 0.35);
        color: #d4b773;
    }

    .category-pill {
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
    }
    .category-pill:hover {
        border-color: #c5a059;
        color: #c5a059;
        transform: translateY(-2px);
    }
    .category-pill.active {
        background: #0a0a0a;
        color: #d4b773;
        border-color: #c5a059;
        box-shadow: 0 4px 12px rgba(197, 160, 89, 0.2);
    }

    .product-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.08);
        border-color: rgba(197, 160, 89, 0.35);
    }

    .pulse-dot {
        animation: pulseAnimation 2s infinite;
    }
    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .floating-cart-btn {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .floating-cart-btn:hover {
        transform: scale(1.05) translateY(-2px);
        box-shadow: 0 14px 30px rgba(197, 160, 89, 0.4);
    }

    .drawer-transition {
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .custom-scroll::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #c5a059;
        border-radius: 999px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 999px;
    }
</style>
@endpush

@section('content')

<div class="max-w-7xl mx-auto space-y-8 pb-16">

    {{-- ======================================================== --}}
    {{-- 1. CABECERA PRINCIPAL CON DATOS REALES DE BASE DE DATOS --}}
    {{-- ======================================================== --}}
    <div class="catalogo-hero rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase retro-badge-gold">
                    <i class="fas fa-utensils text-[10px] text-retro-gold"></i>
                    <span>MENÚ DE RETRO RESTAURANT</span>
                </div>

                <h1 class="font-heading text-3xl sm:text-4xl font-bold tracking-tight text-white leading-tight">
                    Catálogo de Productos
                </h1>

                <p class="text-gray-300 text-sm leading-relaxed">
                    Consulta la carta oficial del restaurante con precios actualizados, disponibilidad de inventario en cocina y detalles de cada plato.
                </p>

                {{-- Métricas reales obtenidas de la base de datos --}}
                <div class="pt-1 flex flex-wrap items-center gap-3 text-xs font-medium text-gray-300">
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-xl border border-white/10">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 pulse-dot"></span>
                        <span><strong>{{ $totalDisponibles }}</strong> disponibles con stock</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-xl border border-white/10">
                        <i class="fas fa-list text-retro-gold"></i>
                        <span><strong>{{ $totalPlatos }}</strong> platillos en carta</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-xl border border-white/10">
                        <i class="fas fa-layer-group text-blue-400"></i>
                        <span><strong>{{ $categorias->count() }}</strong> categorías activas</span>
                    </div>
                </div>
            </div>

            {{-- Acceso a Carrito y Pedidos --}}
            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0">
                <button
                    type="button"
                    onclick="openCartDrawer()"
                    class="bg-gradient-to-r from-retro-gold to-retro-goldlight text-retro-dark hover:brightness-105 font-bold px-6 py-3 rounded-2xl flex items-center justify-center gap-3 shadow transition text-sm cursor-pointer"
                >
                    <i class="fas fa-bag-shopping text-base"></i>
                    <span>Bandeja de Pedido</span>
                    <span id="heroCartCount" class="bg-black text-white text-xs px-2 py-0.5 rounded-full font-bold ml-1">0</span>
                </button>

                <a
                    href="{{ route('pedidos.cliente') }}"
                    class="bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold px-6 py-2.5 rounded-2xl flex items-center justify-center gap-2 transition text-sm"
                >
                    <i class="fas fa-receipt text-retro-gold text-xs"></i>
                    <span>Mis Pedidos Realizados</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 2. BUSCADOR & FILTROS --}}
    {{-- ======================================================== --}}
    <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-sm space-y-5">

        {{-- Formulario de búsqueda y ordenamiento --}}
        <form id="filterForm" method="GET" action="{{ route('productos.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">

            {{-- Input de Búsqueda --}}
            <div class="md:col-span-5 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input
                    type="text"
                    name="q"
                    id="searchInput"
                    value="{{ request('q') ?? request('search') }}"
                    placeholder="Buscar por nombre o descripción..."
                    class="w-full pl-11 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:border-retro-gold focus:bg-white transition shadow-sm font-sans"
                >
                @if(request('q') || request('search'))
                    <a
                        href="{{ route('productos.index') }}"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                        title="Limpiar búsqueda"
                    >
                        <i class="fas fa-circle-xmark text-sm"></i>
                    </a>
                @endif
            </div>

            {{-- Ordenamiento --}}
            <div class="md:col-span-3">
                <div class="relative">
                    <select
                        name="orden"
                        id="ordenSelect"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm text-gray-700 focus:outline-none focus:border-retro-gold focus:bg-white transition shadow-sm appearance-none cursor-pointer"
                    >
                        <option value="recientes" {{ request('orden', 'recientes') == 'recientes' ? 'selected' : '' }}>Últimos agregados</option>
                        <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                        <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                        <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre: A - Z</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            {{-- Checkbox Solo Disponibles --}}
            <div class="md:col-span-2 flex items-center">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 p-2.5 rounded-2xl border border-gray-200 w-full justify-center transition">
                    <input
                        type="checkbox"
                        name="disponibles"
                        value="1"
                        {{ request('disponibles') ? 'checked' : '' }}
                        onchange="this.form.submit()"
                        class="w-4 h-4 text-retro-gold rounded border-gray-300 focus:ring-retro-gold"
                    >
                    <span>Solo Disponibles</span>
                </label>
            </div>

            {{-- Botón Filtrar y Limpiar --}}
            <div class="md:col-span-2 flex items-center gap-2">
                <button
                    type="submit"
                    class="w-full bg-[#0a0a0a] hover:bg-black text-white font-semibold py-3 px-4 rounded-2xl text-xs uppercase tracking-wider transition duration-200 shadow flex items-center justify-center gap-2 border border-black hover:border-retro-gold cursor-pointer"
                >
                    <i class="fas fa-sliders text-retro-gold"></i>
                    <span>Buscar</span>
                </button>

                @if(request()->hasAny(['q', 'search', 'categoria_id', 'disponibles', 'orden']))
                    <a
                        href="{{ route('productos.index') }}"
                        class="p-3 bg-gray-100 text-gray-600 hover:bg-rose-50 hover:text-rose-600 rounded-2xl text-xs transition border border-gray-200 shrink-0"
                        title="Restablecer filtros"
                    >
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>

            @if(request('categoria_id'))
                <input type="hidden" name="categoria_id" value="{{ request('categoria_id') }}">
            @endif

        </form>

        {{-- Categorías Reales desde Base de Datos --}}
        <div>
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">
                    <i class="fas fa-tags text-retro-gold mr-1"></i> Categorías del Menú
                </span>
                <span class="text-xs text-gray-400 font-medium">
                    Total: {{ $productos->total() }} platos
                </span>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scroll">
                {{-- Todas --}}
                <a
                    href="{{ route('productos.index', array_merge(request()->except(['categoria_id', 'page']))) }}"
                    class="category-pill whitespace-nowrap px-4 py-2 rounded-2xl text-xs font-semibold flex items-center gap-2 {{ !request('categoria_id') ? 'active' : 'bg-gray-50 text-gray-700' }}"
                >
                    <i class="fas fa-border-all text-[11px]"></i>
                    <span>Todas</span>
                    <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded-full font-bold">
                        {{ $totalPlatos }}
                    </span>
                </a>

                @foreach($categorias as $cat)
                    @php
                        $catNom = strtolower($cat->nombre);
                        $iconoCat = 'fa-utensils';
                        if(str_contains($catNom, 'carne')) $iconoCat = 'fa-drumstick-bite';
                        elseif(str_contains($catNom, 'bebida')) $iconoCat = 'fa-wine-glass';
                        elseif(str_contains($catNom, 'postre')) $iconoCat = 'fa-cake-candles';
                        elseif(str_contains($catNom, 'marisco')) $iconoCat = 'fa-shrimp';
                        elseif(str_contains($catNom, 'verdura') || str_contains($catNom, 'ensalada')) $iconoCat = 'fa-seedling';
                        elseif(str_contains($catNom, 'pan')) $iconoCat = 'fa-bread-slice';
                    @endphp
                    <a
                        href="{{ route('productos.index', array_merge(request()->except(['page']), ['categoria_id' => $cat->id])) }}"
                        class="category-pill whitespace-nowrap px-4 py-2 rounded-2xl text-xs font-semibold flex items-center gap-2 {{ request('categoria_id') == $cat->id ? 'active' : 'bg-gray-50 text-gray-700' }}"
                    >
                        <i class="fas {{ $iconoCat }} text-[11px] {{ request('categoria_id') == $cat->id ? 'text-retro-gold' : 'text-gray-400' }}"></i>
                        <span>{{ $cat->nombre }}</span>
                        <span class="text-[10px] bg-gray-200/60 {{ request('categoria_id') == $cat->id ? 'text-retro-gold bg-black/40' : 'text-gray-500' }} px-1.5 py-0.5 rounded-full font-bold">
                            {{ $cat->productos_count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ======================================================== --}}
    {{-- 3. GRID DE PLATOS (INFORMACIÓN REAL) --}}
    {{-- ======================================================== --}}
    @if($productos->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($productos as $p)
                @php
                    $stockReal = $p->inventario->cantidad ?? 0;
                    $disponible = $p->estado && $stockReal > 0;
                    $categoriaNombre = $p->categoria->nombre ?? 'Menú';
                    $catNomLow = strtolower($categoriaNombre);
                    
                    $iconoHeader = 'fa-utensils';
                    if(str_contains($catNomLow, 'carne')) $iconoHeader = 'fa-drumstick-bite';
                    elseif(str_contains($catNomLow, 'bebida')) $iconoHeader = 'fa-wine-glass';
                    elseif(str_contains($catNomLow, 'postre')) $iconoHeader = 'fa-cake-candles';
                    elseif(str_contains($catNomLow, 'marisco')) $iconoHeader = 'fa-shrimp';
                    elseif(str_contains($catNomLow, 'verdura')) $iconoHeader = 'fa-seedling';
                    elseif(str_contains($catNomLow, 'pan')) $iconoHeader = 'fa-bread-slice';
                @endphp

                <div class="product-card bg-white rounded-3xl border border-gray-200 overflow-hidden flex flex-col justify-between group relative">

                    {{-- Imagen real o Placeholder elegante institucional --}}
                    <div class="relative h-48 bg-gradient-to-br from-[#121824] to-[#0a0a0a] flex items-center justify-center overflow-hidden">
                        @if(!empty($p->imagen))
                            <img
                                src="{{ $p->imagen }}"
                                alt="{{ $p->nombre }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none"></div>
                        @else
                            {{-- Placeholder elegante con icono del restaurante y categoría --}}
                            <div class="flex flex-col items-center justify-center text-center p-4">
                                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-retro-gold text-2xl mb-2 group-hover:scale-110 transition duration-300">
                                    <i class="fas {{ $iconoHeader }}"></i>
                                </div>
                                <span class="text-[10px] tracking-widest uppercase font-bold text-gray-400">RETRO RESTAURANT</span>
                                <span class="text-[11px] text-retro-gold/80 font-medium mt-0.5">{{ $categoriaNombre }}</span>
                            </div>
                        @endif

                        {{-- Badge Categoría (Superior Izquierda) --}}
                        <span class="absolute top-3 left-3 bg-black/80 backdrop-blur-md text-retro-gold text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full border border-retro-gold/30 shadow">
                            {{ $categoriaNombre }}
                        </span>

                        {{-- Badge Disponibilidad e Inventario Real (Superior Derecha) --}}
                        @if($disponible)
                            <span class="absolute top-3 right-3 bg-emerald-600/90 backdrop-blur text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow flex items-center gap-1.5 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                <span>Disponible</span>
                            </span>
                        @else
                            <span class="absolute top-3 right-3 bg-rose-600/90 backdrop-blur text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow flex items-center gap-1.5 border border-rose-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                <span>Agotado</span>
                            </span>
                        @endif

                        {{-- Botón Vista Rápida / Pedir --}}
                        <button
                            type="button"
                            onclick='openOrderModal(@json($p))'
                            class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-white/95 hover:bg-white text-gray-900 text-xs font-semibold px-4 py-1.5 rounded-full opacity-0 group-hover:opacity-100 transition duration-200 shadow-lg flex items-center gap-1.5 cursor-pointer"
                        >
                            <i class="fas fa-eye text-retro-gold"></i>
                            <span>Detalles</span>
                        </button>
                    </div>

                    {{-- Datos reales del producto --}}
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-heading text-lg font-bold text-gray-900 group-hover:text-retro-gold transition line-clamp-1 cursor-pointer" onclick='openOrderModal(@json($p))' title="{{ $p->nombre }}">
                                    {{ $p->nombre }}
                                </h3>
                            </div>

                            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                {{ $p->descripcion ?: 'Platillo preparado con la receta tradicional de la casa.' }}
                            </p>

                            {{-- Indicador de Stock Real --}}
                            <div class="pt-1 flex items-center gap-2 text-[11px]">
                                @if($stockReal > 0)
                                    <span class="text-gray-500 flex items-center gap-1">
                                        <i class="fas fa-cubes-stacked text-[10px] text-retro-gold"></i>
                                        <span>Stock: <strong>{{ $stockReal }}</strong> unid.</span>
                                    </span>
                                @else
                                    <span class="text-rose-500 font-semibold flex items-center gap-1">
                                        <i class="fas fa-circle-xmark text-[10px]"></i>
                                        <span>Sin stock en cocina</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Precio y Acción --}}
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 block">
                                    Precio
                                </span>
                                <span class="text-xl font-extrabold text-gray-900 font-heading tracking-tight">
                                    ${{ number_format($p->precio, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    onclick='openOrderModal(@json($p))'
                                    class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition cursor-pointer"
                                    title="Ver ficha completa"
                                >
                                    <i class="fas fa-info text-xs"></i>
                                </button>

                                @if($disponible)
                                    <button
                                        type="button"
                                        onclick='openOrderModal(@json($p))'
                                        class="bg-[#0a0a0a] hover:bg-black text-retro-gold hover:text-white px-3.5 py-2 rounded-xl flex items-center gap-2 text-xs font-bold transition shadow border border-black hover:border-retro-gold active:scale-95 cursor-pointer"
                                        title="Agregar a mi pedido"
                                    >
                                        <i class="fas fa-plus text-[10px]"></i>
                                        <span>Pedir</span>
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="bg-gray-100 text-gray-400 cursor-not-allowed px-3 py-2 rounded-xl text-xs font-semibold"
                                        title="No disponible"
                                    >
                                        Agotado
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            @endforeach
        </div>

        {{-- Paginación real --}}
        @if($productos->hasPages())
            <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-500 font-medium">
                    Mostrando del <span class="font-bold text-gray-900">{{ $productos->firstItem() }}</span> al <span class="font-bold text-gray-900">{{ $productos->lastItem() }}</span> de <span class="font-bold text-gray-900">{{ $productos->total() }}</span> productos
                </p>

                <div>
                    {{ $productos->links() }}
                </div>
            </div>
        @endif

    @else
        {{-- Estado cuando no hay resultados --}}
        <div class="bg-white rounded-3xl border border-gray-200 p-12 text-center max-w-xl mx-auto shadow-sm space-y-4">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-gray-100 text-gray-500 flex items-center justify-center text-2xl">
                <i class="fas fa-magnifying-glass"></i>
            </div>

            <h3 class="font-heading text-xl font-bold text-gray-800">
                No se encontraron productos
            </h3>

            <p class="text-gray-500 text-xs leading-relaxed">
                No hay productos en la carta que coincidan con los criterios de búsqueda seleccionados.
            </p>

            <div class="pt-2">
                <a
                    href="{{ route('productos.index') }}"
                    class="inline-flex items-center gap-2 bg-[#0a0a0a] hover:bg-black text-white px-5 py-2.5 rounded-2xl text-xs uppercase tracking-wider font-semibold transition"
                >
                    <i class="fas fa-rotate-left text-retro-gold"></i>
                    <span>Restablecer Filtros</span>
                </a>
            </div>
        </div>
    @endif

</div>

{{-- ======================================================== --}}
{{-- 4. BOTÓN FLOTANTE DEL CARRITO --}}
{{-- ======================================================== --}}
<button
    type="button"
    id="floatingCartBtn"
    onclick="openCartDrawer()"
    class="floating-cart-btn fixed bottom-6 right-6 z-40 bg-[#0a0a0a] border-2 border-retro-gold text-retro-gold px-4 py-3.5 rounded-full flex items-center gap-3 cursor-pointer group"
    title="Ver mi bandeja de pedido"
>
    <div class="relative">
        <i class="fas fa-bag-shopping text-lg group-hover:scale-110 transition"></i>
        <span
            id="floatingCartBadge"
            class="absolute -top-2 -right-2 bg-rose-600 text-white text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center border-2 border-black"
        >
            0
        </span>
    </div>

    <div class="text-left hidden sm:block">
        <span class="text-[10px] uppercase font-bold text-gray-400 block leading-tight">Mi Pedido</span>
        <span id="floatingCartTotal" class="text-xs font-extrabold text-white font-heading leading-tight">$0</span>
    </div>
</button>

{{-- ======================================================== --}}
{{-- 5. MODAL DE "TU PEDIDO" (CARRITO DE COMPRAS INTERACTIVO) --}}
{{-- ======================================================== --}}
<div id="cartDrawerBackdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden transition-opacity duration-300 opacity-0 flex items-center justify-center p-3 sm:p-4" onclick="handleCartModalBackdropClick(event)">
    <div
        id="cartModalCard"
        class="bg-white rounded-[28px] max-w-md w-full shadow-2xl border border-gray-100 flex flex-col max-h-[92vh] transform scale-95 transition-all duration-300 overflow-hidden"
    >
        {{-- Encabezado Modal --}}
        <div class="p-5 sm:p-6 pb-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-white">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-[#d49b16] text-white flex items-center justify-center text-xl shadow-xs shrink-0">
                    <i class="fas fa-cart-shopping"></i>
                </div>
                <div>
                    <h3 class="font-heading text-xl font-bold text-gray-900 leading-tight">Tu pedido</h3>
                    <p id="cartModalItemSummary" class="text-xs text-gray-400 font-medium mt-0.5">0 ítems · $0</p>
                </div>
            </div>

            <button
                type="button"
                onclick="closeCartDrawer()"
                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center transition cursor-pointer"
                title="Cerrar"
            >
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Contenido Scrollable Interactivo --}}
        <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-5 custom-scroll">

            {{-- 1. TIPO DE PEDIDO --}}
            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-2.5">
                    TIPO DE PEDIDO
                </label>
                <div class="grid grid-cols-3 gap-2.5">
                    {{-- Mesa --}}
                    <button
                        type="button"
                        id="tipoPedidoMesaBtn"
                        onclick="setTipoPedido('mesa')"
                        class="tipo-pedido-btn p-3 rounded-2xl border-2 text-center transition cursor-pointer flex flex-col items-center justify-center relative border-[#d49b16] bg-[#fefce8]"
                    >
                        <i class="fas fa-chair text-lg text-amber-600 mb-1"></i>
                        <span class="text-xs font-bold text-gray-800">Mesa</span>
                        <div id="checkMesaBadge" class="w-4 h-4 rounded-full bg-[#d49b16] text-white flex items-center justify-center text-[8px] mt-1 shadow-xs">
                            <i class="fas fa-check"></i>
                        </div>
                    </button>

                    {{-- Domicilio --}}
                    <button
                        type="button"
                        id="tipoPedidoDomicilioBtn"
                        onclick="setTipoPedido('domicilio')"
                        class="tipo-pedido-btn p-3 rounded-2xl border text-center transition cursor-pointer flex flex-col items-center justify-center relative border-gray-200 hover:border-gray-300 bg-white"
                    >
                        <i class="fas fa-motorcycle text-lg text-gray-500 mb-1"></i>
                        <span class="text-xs font-bold text-gray-700">Domicilio</span>
                        <div id="checkDomicilioBadge" class="hidden w-4 h-4 rounded-full bg-[#d49b16] text-white items-center justify-center text-[8px] mt-1 shadow-xs">
                            <i class="fas fa-check"></i>
                        </div>
                    </button>

                    {{-- Llevar --}}
                    <button
                        type="button"
                        id="tipoPedidoLlevarBtn"
                        onclick="setTipoPedido('llevar')"
                        class="tipo-pedido-btn p-3 rounded-2xl border text-center transition cursor-pointer flex flex-col items-center justify-center relative border-gray-200 hover:border-gray-300 bg-white"
                    >
                        <i class="fas fa-bag-shopping text-lg text-gray-500 mb-1"></i>
                        <span class="text-xs font-bold text-gray-700">Llevar</span>
                        <div id="checkLlevarBadge" class="hidden w-4 h-4 rounded-full bg-[#d49b16] text-white items-center justify-center text-[8px] mt-1 shadow-xs">
                            <i class="fas fa-check"></i>
                        </div>
                    </button>
                </div>
            </div>

            {{-- 2. CONDICIONAL SEGÚN TIPO DE PEDIDO --}}
            {{-- SECCIÓN MESA (Grid de 10 mesas) --}}
            <div id="seccionMesasContainer" class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">
                        MESA <span class="text-rose-500">*</span>
                    </label>
                    <span id="mesaSeleccionadaTexto" class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60 hidden">
                        Mesa 1
                    </span>
                </div>

                <div class="grid grid-cols-5 gap-2">
                    @php
                        $mesasLista = isset($mesas) && count($mesas) > 0 ? $mesas : [
                            (object)['numero_mesa' => 1, 'capacidad' => 2],
                            (object)['numero_mesa' => 2, 'capacidad' => 2],
                            (object)['numero_mesa' => 3, 'capacidad' => 4],
                            (object)['numero_mesa' => 4, 'capacidad' => 4],
                            (object)['numero_mesa' => 5, 'capacidad' => 4],
                            (object)['numero_mesa' => 6, 'capacidad' => 6],
                            (object)['numero_mesa' => 7, 'capacidad' => 6],
                            (object)['numero_mesa' => 8, 'capacidad' => 8],
                            (object)['numero_mesa' => 9, 'capacidad' => 8],
                            (object)['numero_mesa' => 10, 'capacidad' => 10],
                        ];
                    @endphp
                    @foreach($mesasLista as $m)
                        <button
                            type="button"
                            onclick="selectMesa({{ $m->numero_mesa }}, {{ $m->capacidad }})"
                            id="mesaBtn_{{ $m->numero_mesa }}"
                            class="mesa-card-btn p-2 rounded-xl border border-gray-200 text-center transition cursor-pointer hover:border-gray-300 flex flex-col items-center justify-center bg-white {{ $loop->first ? 'border-2 border-[#d49b16] bg-amber-50/70 shadow-xs' : '' }}"
                        >
                            <i class="fas fa-chair text-xs {{ $loop->first ? 'text-amber-600' : 'text-gray-400' }} mb-0.5"></i>
                            <span class="text-xs font-bold text-gray-800 leading-tight">{{ $m->numero_mesa }}</span>
                            <span class="text-[9px] text-gray-400 font-medium">Cap.{{ $m->capacidad }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- SECCIÓN DOMICILIO --}}
            <div id="seccionDomicilioContainer" class="hidden space-y-3 bg-gray-50/80 p-4 rounded-2xl border border-gray-200/70">
                <div>
                    <label class="text-[11px] font-bold text-gray-600 block mb-1">
                        Dirección de Entrega <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="pedidoDomicilioDireccion"
                        placeholder="Ej: Calle 45 # 12-34, Apto 201"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                    >
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[11px] font-bold text-gray-600 block mb-1">
                            Teléfono <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="tel"
                            id="pedidoDomicilioTelefono"
                            placeholder="Ej: 300 123 4567"
                            class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                        >
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-600 block mb-1">Barrio / Sector</label>
                        <input
                            type="text"
                            id="pedidoDomicilioBarrio"
                            placeholder="Ej: Centro / Laureles"
                            class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                        >
                    </div>
                </div>
            </div>

            {{-- SECCIÓN LLEVAR --}}
            <div id="seccionLlevarContainer" class="hidden space-y-3 bg-gray-50/80 p-4 rounded-2xl border border-gray-200/70">
                <div>
                    <label class="text-[11px] font-bold text-gray-600 block mb-1">
                        Nombre de Quien Recoge <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="pedidoLlevarNombre"
                        value="{{ auth()->user()->name ?? '' }}"
                        placeholder="Nombre completo"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                    >
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-600 block mb-1">Hora Aproximada de Recogida</label>
                    <input
                        type="time"
                        id="pedidoLlevarHora"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-retro-gold"
                    >
                </div>
            </div>

            {{-- 3. LISTA DE PLATILLOS SELECCIONADOS --}}
            <div class="pt-2 border-t border-gray-100">
                <div id="cartItemsContainer" class="space-y-3">
                    {{-- Inyectado dinámicamente con JS --}}
                </div>
            </div>

        </div>

        {{-- PIE DEL MODAL --}}
        <div class="p-5 sm:p-6 pt-4 border-t border-gray-100 bg-white shrink-0 space-y-3.5">
            {{-- Subtotal e ítems --}}
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between items-center text-gray-500">
                    <span>Subtotal</span>
                    <span id="cartModalSubtotal" class="font-bold text-gray-800 text-sm">$0</span>
                </div>
                <div class="flex justify-between items-center text-gray-500">
                    <span>Ítems</span>
                    <span id="cartModalItemsCount" class="font-semibold text-gray-800">0 unidades</span>
                </div>
            </div>

            <div class="border-t border-gray-200/80 my-1"></div>

            {{-- Total Grande --}}
            <div class="flex justify-between items-baseline">
                <span class="font-heading text-lg font-bold text-gray-900">Total</span>
                <span id="cartModalTotal" class="font-heading text-2xl sm:text-3xl font-extrabold text-[#d49b16]">$0</span>
            </div>

            {{-- Botón Confirmar Pedido --}}
            <button
                type="button"
                id="btnConfirmarPedidoModal"
                onclick="procesarConfirmacionPedido()"
                class="w-full bg-[#d49b16] hover:bg-[#bd870f] text-white font-bold py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2 shadow-sm hover:shadow transition duration-200 text-sm cursor-pointer"
            >
                <i class="fas fa-lock text-xs"></i>
                <span>Confirmar pedido</span>
            </button>

            {{-- Enlace Vaciar Carrito --}}
            <button
                type="button"
                onclick="clearCart()"
                class="w-full text-center text-xs text-gray-400 hover:text-rose-600 transition flex items-center justify-center gap-1.5 cursor-pointer py-0.5"
            >
                <i class="fas fa-trash-can text-[11px]"></i>
                <span>Vaciar carrito</span>
            </button>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 6. MODAL DE PEDIDO EXACTO SEGÚN DISEÑO SOLICITADO --}}
{{-- ======================================================== --}}
<div id="orderModalBackdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden transition-opacity duration-300 opacity-0 flex items-center justify-center p-4" onclick="handleOrderModalBackdropClick(event)">
    <div
        id="orderModalCard"
        class="bg-white rounded-[28px] max-w-2xl w-full p-6 sm:p-8 relative shadow-2xl border border-gray-100 transform scale-95 transition-all duration-300 overflow-hidden"
    >
        {{-- Botón Cerrar (x) en esquina superior derecha --}}
        <button
            type="button"
            onclick="closeOrderModal()"
            class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center transition cursor-pointer z-10"
            title="Cerrar"
        >
            <i class="fas fa-xmark text-sm"></i>
        </button>

        {{-- Contenedor de 2 columnas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 items-start">

            {{-- COLUMNA IZQUIERDA: Imagen, Título, Categoría, Descripción, Cantidad, Observación --}}
            <div class="space-y-3">
                {{-- Imagen del producto --}}
                <div class="w-full aspect-[4/3] rounded-2xl overflow-hidden bg-gray-100 shadow-xs relative flex items-center justify-center">
                    <img
                        id="orderModalImg"
                        src=""
                        alt=""
                        class="w-full h-full object-cover"
                    >
                    {{-- Placeholder si no hay imagen --}}
                    <div id="orderModalImgPlaceholder" class="hidden flex-col items-center justify-center text-center p-4 bg-gradient-to-br from-[#121824] to-[#0a0a0a] w-full h-full text-white">
                        <i id="orderModalPlaceholderIcon" class="fas fa-utensils text-3xl text-retro-gold mb-2"></i>
                        <span class="text-[9px] uppercase tracking-widest text-gray-400 font-bold">RETRO RESTAURANT</span>
                    </div>
                </div>

                {{-- Título y Badge de Categoría --}}
                <div class="pt-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 id="orderModalName" class="font-heading text-xl sm:text-2xl font-bold text-gray-900 capitalize">
                            Hamburguesa
                        </h3>
                        <span id="orderModalCategory" class="bg-[#f3e8ff] text-[#9333ea] text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
                            HAMBURGUESAS
                        </span>
                    </div>

                    {{-- Descripción --}}
                    <p id="orderModalDesc" class="text-xs text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                        Hamburguesa muy rica
                    </p>
                </div>

                {{-- Selector de Cantidad --}}
                <div class="pt-2">
                    <label class="text-xs font-bold text-gray-700 block mb-2">Cantidad</label>
                    <div class="inline-flex items-center justify-between border border-gray-200 bg-white rounded-2xl p-1 shadow-xs w-40">
                        <button
                            type="button"
                            onclick="decrementModalQty()"
                            class="w-8 h-8 rounded-xl hover:bg-gray-50 text-amber-500 font-bold text-lg flex items-center justify-center transition cursor-pointer select-none"
                        >
                            −
                        </button>
                        <span id="orderModalQtyDisplay" class="font-bold text-gray-900 text-sm w-10 text-center select-none">
                            1
                        </span>
                        <button
                            type="button"
                            onclick="incrementModalQty()"
                            class="w-8 h-8 rounded-xl hover:bg-gray-50 text-amber-500 font-bold text-lg flex items-center justify-center transition cursor-pointer select-none"
                        >
                            +
                        </button>
                    </div>
                </div>

                {{-- Caja de Observación --}}
                <div class="bg-[#fffbeb] border border-[#fef3c7] rounded-2xl p-3.5 space-y-1.5 pt-2.5">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#b45309]">
                        <i class="fas fa-circle-info text-amber-500 text-xs"></i>
                        <span>Observación</span>
                    </div>
                    <input
                        type="text"
                        id="orderModalObservacion"
                        placeholder="Ej: sin azúcar, extra frío..."
                        class="w-full bg-white border border-[#fde68a] rounded-xl px-3.5 py-2 text-xs text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400/50 transition font-sans"
                    >
                </div>
            </div>

            {{-- COLUMNA DERECHA: Resumen de producto, Subtotal y Botones de acción --}}
            <div class="flex flex-col justify-between h-full pt-6 md:pt-4 space-y-5">
                {{-- Tarjeta de Resumen --}}
                <div class="bg-[#f8fafc] rounded-3xl p-5 sm:p-6 space-y-3.5 border border-slate-100/80">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400 font-medium">Producto</span>
                        <span id="orderModalSummaryName" class="font-bold text-gray-800 text-sm capitalize">Hamburguesa</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400 font-medium">Precio unitario</span>
                        <span id="orderModalSummaryUnit" class="font-bold text-gray-800 text-sm">$10.000</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400 font-medium">Cantidad</span>
                        <span id="orderModalSummaryQty" class="font-bold text-gray-800 text-sm">1</span>
                    </div>

                    <div class="border-t border-gray-200/80 my-1"></div>

                    <div class="flex justify-between items-baseline pt-1">
                        <span class="font-bold text-gray-900 text-base">Subtotal</span>
                        <span id="orderModalSummarySubtotal" class="font-heading text-2xl font-bold text-[#c5a059]">$10.000</span>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="space-y-2.5 pt-2">
                    <button
                        type="button"
                        onclick="confirmAddToCart(false)"
                        class="w-full bg-[#d49b16] hover:bg-[#bd870f] text-white font-bold py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2.5 shadow-sm hover:shadow transition duration-200 text-xs uppercase tracking-wider cursor-pointer"
                    >
                        <i class="fas fa-cart-shopping text-sm"></i>
                        <span>Agregar al carrito</span>
                    </button>

                    <button
                        type="button"
                        onclick="confirmAddToCart(true)"
                        class="w-full bg-[#0f172a] hover:bg-black text-white font-bold py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2.5 shadow-sm hover:shadow transition duration-200 text-xs uppercase tracking-wider cursor-pointer"
                    >
                        <i class="fas fa-bag-shopping text-sm"></i>
                        <span>Agregar y ver pedido</span>
                    </button>

                    <button
                        type="button"
                        onclick="closeOrderModal()"
                        class="w-full text-center text-gray-400 hover:text-gray-600 text-xs font-semibold py-1.5 transition cursor-pointer"
                    >
                        Cancelar
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 7. TOAST NOTIFICATION --}}
{{-- ======================================================== --}}
<div
    id="toastNotification"
    class="fixed top-6 right-6 z-50 bg-[#0a0a0a] text-white px-5 py-4 rounded-2xl shadow-2xl border border-retro-gold/40 flex items-center gap-3 transform translate-y-[-150%] transition-transform duration-300 max-w-sm pointer-events-none"
>
    <div class="w-8 h-8 rounded-full bg-retro-gold/20 text-retro-gold flex items-center justify-center shrink-0">
        <i class="fas fa-check text-xs"></i>
    </div>
    <div class="flex-1 text-xs">
        <p id="toastMessage" class="font-bold text-white">¡Plato agregado a tu bandeja!</p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const CART_STORAGE_KEY = 'retro_restaurant_cliente_cart';

    function getCart() {
        try {
            const raw = localStorage.getItem(CART_STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function saveCart(cart) {
        try {
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
            updateCartUI();
        } catch (e) {
            console.error(e);
        }
    }

    function updateItemQuantity(id, delta) {
        let cart = getCart();
        const index = cart.findIndex(item => item.id === id);
        if (index > -1) {
            cart[index].cantidad += delta;
            if (cart[index].cantidad <= 0) {
                cart.splice(index, 1);
            }
            saveCart(cart);
        }
    }

    function removeItemFromCart(id) {
        let cart = getCart();
        cart = cart.filter(item => item.id !== id);
        saveCart(cart);
    }

    function clearCart() {
        if (confirm('¿Deseas vaciar los platillos de tu bandeja?')) {
            saveCart([]);
        }
    }

    // ========================================================
    // TIPO DE PEDIDO Y SELECCIÓN DE MESA
    // ========================================================
    let currentTipoPedido = 'mesa';
    let currentSelectedMesa = 1;
    let currentSelectedMesaCap = 2;

    function setTipoPedido(tipo) {
        currentTipoPedido = tipo;
        const btnMesa = document.getElementById('tipoPedidoMesaBtn');
        const btnDom = document.getElementById('tipoPedidoDomicilioBtn');
        const btnLlev = document.getElementById('tipoPedidoLlevarBtn');
        const badgeMesa = document.getElementById('checkMesaBadge');
        const badgeDom = document.getElementById('checkDomicilioBadge');
        const badgeLlev = document.getElementById('checkLlevarBadge');

        const secMesa = document.getElementById('seccionMesasContainer');
        const secDom = document.getElementById('seccionDomicilioContainer');
        const secLlev = document.getElementById('seccionLlevarContainer');

        // Reset styles
        [btnMesa, btnDom, btnLlev].forEach(b => {
            if (b) b.className = 'tipo-pedido-btn p-3 rounded-2xl border text-center transition cursor-pointer flex flex-col items-center justify-center relative border-gray-200 hover:border-gray-300 bg-white';
        });
        [badgeMesa, badgeDom, badgeLlev].forEach(b => {
            if (b) b.classList.add('hidden');
        });

        if (secMesa) secMesa.classList.add('hidden');
        if (secDom) secDom.classList.add('hidden');
        if (secLlev) secLlev.classList.add('hidden');

        if (tipo === 'mesa') {
            if (btnMesa) btnMesa.className = 'tipo-pedido-btn p-3 rounded-2xl border-2 text-center transition cursor-pointer flex flex-col items-center justify-center relative border-[#d49b16] bg-[#fefce8]';
            if (badgeMesa) badgeMesa.classList.remove('hidden');
            if (secMesa) secMesa.classList.remove('hidden');
        } else if (tipo === 'domicilio') {
            if (btnDom) btnDom.className = 'tipo-pedido-btn p-3 rounded-2xl border-2 text-center transition cursor-pointer flex flex-col items-center justify-center relative border-[#d49b16] bg-[#fefce8]';
            if (badgeDom) badgeDom.classList.remove('hidden');
            if (secDom) secDom.classList.remove('hidden');
        } else if (tipo === 'llevar') {
            if (btnLlev) btnLlev.className = 'tipo-pedido-btn p-3 rounded-2xl border-2 text-center transition cursor-pointer flex flex-col items-center justify-center relative border-[#d49b16] bg-[#fefce8]';
            if (badgeLlev) badgeLlev.classList.remove('hidden');
            if (secLlev) secLlev.classList.remove('hidden');
        }
    }

    function selectMesa(num, cap) {
        currentSelectedMesa = num;
        currentSelectedMesaCap = cap;

        document.querySelectorAll('.mesa-card-btn').forEach(btn => {
            btn.className = 'mesa-card-btn p-2 rounded-xl border border-gray-200 text-center transition cursor-pointer hover:border-gray-300 flex flex-col items-center justify-center bg-white';
            const icon = btn.querySelector('i');
            if (icon) icon.className = 'fas fa-chair text-xs text-gray-400 mb-0.5';
        });

        const selectedBtn = document.getElementById(`mesaBtn_${num}`);
        if (selectedBtn) {
            selectedBtn.className = 'mesa-card-btn p-2 rounded-xl border-2 border-[#d49b16] text-center transition cursor-pointer flex flex-col items-center justify-center bg-amber-50/80 shadow-xs';
            const icon = selectedBtn.querySelector('i');
            if (icon) icon.className = 'fas fa-chair text-xs text-amber-600 mb-0.5';
        }

        const textoBadge = document.getElementById('mesaSeleccionadaTexto');
        if (textoBadge) {
            textoBadge.textContent = `Mesa ${num} seleccionada`;
            textoBadge.classList.remove('hidden');
        }
    }

    function updateCartUI() {
        const cart = getCart();
        const totalItems = cart.reduce((acc, item) => acc + item.cantidad, 0);
        const totalPrice = cart.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);

        const formattedTotal = '$' + new Intl.NumberFormat('es-CO').format(totalPrice);
        const heroBadge = document.getElementById('heroCartCount');
        const floatingBadge = document.getElementById('floatingCartBadge');
        const floatingTotal = document.getElementById('floatingCartTotal');

        if (heroBadge) heroBadge.textContent = totalItems;
        if (floatingBadge) floatingBadge.textContent = totalItems;
        if (floatingTotal) floatingTotal.textContent = formattedTotal;

        // Elementos del Modal "Tu Pedido"
        const modalSummary = document.getElementById('cartModalItemSummary');
        const modalSubtotal = document.getElementById('cartModalSubtotal');
        const modalItemsCount = document.getElementById('cartModalItemsCount');
        const modalTotal = document.getElementById('cartModalTotal');
        const btnConfirmar = document.getElementById('btnConfirmarPedidoModal');

        if (modalSummary) {
            modalSummary.textContent = `${totalItems} ${totalItems === 1 ? 'ítem' : 'ítems'} · ${formattedTotal}`;
        }
        if (modalSubtotal) modalSubtotal.textContent = formattedTotal;
        if (modalItemsCount) modalItemsCount.textContent = `${totalItems} ${totalItems === 1 ? 'unidad' : 'unidades'}`;
        if (modalTotal) modalTotal.textContent = formattedTotal;

        const container = document.getElementById('cartItemsContainer');
        if (!container) return;

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="py-10 text-center space-y-2.5">
                    <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-xl mx-auto">
                        <i class="fas fa-cart-shopping"></i>
                    </div>
                    <h4 class="font-heading text-base font-bold text-gray-800">Tu pedido está vacío</h4>
                    <p class="text-xs text-gray-400 max-w-xs mx-auto">
                        Agrega los platillos que deseas pedir desde el catálogo.
                    </p>
                </div>
            `;
            if (btnConfirmar) {
                btnConfirmar.classList.add('opacity-50', 'pointer-events-none');
            }
        } else {
            if (btnConfirmar) {
                btnConfirmar.classList.remove('opacity-50', 'pointer-events-none');
            }

            let html = '';
            cart.forEach(item => {
                const itemTotal = '$' + new Intl.NumberFormat('es-CO').format(item.precio * item.cantidad);
                const itemUnit = '$' + new Intl.NumberFormat('es-CO').format(item.precio);

                html += `
                    <div class="flex items-center justify-between gap-3 py-2.5 border-b border-gray-100 last:border-none">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                                ${item.imagen ? `<img src="${item.imagen}" alt="${item.nombre}" class="w-full h-full object-cover">` : `<i class="fas fa-utensils text-gray-400 text-xs"></i>`}
                            </div>

                            <div class="min-w-0">
                                <h5 class="text-xs font-bold text-gray-900 truncate capitalize leading-tight">${item.nombre}</h5>
                                <span class="text-xs font-bold text-[#d49b16] block mt-0.5 font-heading">${itemTotal}</span>
                                ${item.observacion ? `
                                    <span class="text-[10px] text-amber-800 bg-amber-50 border border-amber-200/50 rounded px-1.5 py-0.5 mt-0.5 inline-block">
                                        <i class="fas fa-comment-dots text-[9px] mr-1 text-amber-500"></i>${item.observacion}
                                    </span>
                                ` : ''}
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <div class="flex items-center border border-gray-200 rounded-lg p-0.5 bg-white shadow-2xs">
                                <button
                                    type="button"
                                    onclick="updateItemQuantity(${item.id}, -1)"
                                    class="w-6 h-6 rounded-md hover:bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold transition cursor-pointer select-none"
                                >−</button>
                                <span class="w-5 text-center text-xs font-bold text-gray-800 select-none">${item.cantidad}</span>
                                <button
                                    type="button"
                                    onclick="updateItemQuantity(${item.id}, 1)"
                                    class="w-6 h-6 rounded-md hover:bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold transition cursor-pointer select-none"
                                >+</button>
                            </div>

                            <button
                                type="button"
                                onclick="removeItemFromCart(${item.id})"
                                class="text-gray-300 hover:text-rose-500 p-1 transition cursor-pointer ml-1"
                                title="Eliminar"
                            >
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    }

    function openCartDrawer() {
        const backdrop = document.getElementById('cartDrawerBackdrop');
        const card = document.getElementById('cartModalCard');
        if (!backdrop || !card) return;

        backdrop.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 10);
        updateCartUI();
    }

    function closeCartDrawer() {
        const backdrop = document.getElementById('cartDrawerBackdrop');
        const card = document.getElementById('cartModalCard');
        if (!backdrop || !card) return;

        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        backdrop.classList.add('opacity-0');
        setTimeout(() => {
            backdrop.classList.add('hidden');
        }, 250);
    }

    function handleCartModalBackdropClick(event) {
        if (event.target.id === 'cartDrawerBackdrop') {
            closeCartDrawer();
        }
    }

    async function procesarConfirmacionPedido() {
        const cart = getCart();
        if (cart.length === 0) {
            showToast('Tu carrito está vacío. Agrega platillos antes de confirmar.');
            return;
        }

        const btnConfirm = document.getElementById('btnConfirmarPedidoModal');
        const originalBtnHtml = btnConfirm ? btnConfirm.innerHTML : '';

        // Preparar payload según el tipo de pedido
        let payload = {
            tipo_pedido: currentTipoPedido,
            items: cart.map(item => ({
                id: item.id,
                cantidad: item.cantidad,
                observacion: item.observacion || ''
            }))
        };

        if (currentTipoPedido === 'mesa') {
            payload.mesa_numero = currentSelectedMesa || 1;
            payload.mesa_capacidad = currentSelectedMesaCap || 2;
        } else if (currentTipoPedido === 'domicilio') {
            const dir = document.getElementById('pedidoDomicilioDireccion')?.value.trim();
            const tel = document.getElementById('pedidoDomicilioTelefono')?.value.trim();
            const barrio = document.getElementById('pedidoDomicilioBarrio')?.value.trim();

            if (!dir || !tel) {
                showToast('Por favor completa la dirección y teléfono para el domicilio.');
                if (!dir) document.getElementById('pedidoDomicilioDireccion')?.focus();
                else document.getElementById('pedidoDomicilioTelefono')?.focus();
                return;
            }

            payload.direccion = dir;
            payload.telefono = tel;
            payload.barrio = barrio;
        } else if (currentTipoPedido === 'llevar') {
            const nombre = document.getElementById('pedidoLlevarNombre')?.value.trim();
            const hora = document.getElementById('pedidoLlevarHora')?.value.trim();

            if (!nombre) {
                showToast('Por favor ingresa el nombre de quien recoge el pedido.');
                document.getElementById('pedidoLlevarNombre')?.focus();
                return;
            }

            payload.nombre_recoge = nombre;
            payload.hora_recogida = hora;
        }

        // Estado de carga en el botón
        if (btnConfirm) {
            btnConfirm.disabled = true;
            btnConfirm.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i> <span>Registrando en el sistema...</span>';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            const response = await fetch("{{ route('cliente.pedidos.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 401 || data.require_login) {
                    showToast('Debes iniciar sesión para confirmar tu pedido.');
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 1200);
                    return;
                }
                throw new Error(data.message || 'Error al procesar el pedido');
            }

            // Éxito: vaciar carrito local
            saveCart([]);
            closeCartDrawer();

            // Mensaje de éxito
            showToast(data.message || '¡Pedido confirmado con éxito!');

            // Redirigir al módulo correspondiente (Mis Pedidos o Domicilios)
            setTimeout(() => {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            }, 800);

        } catch (error) {
            console.error('Error al registrar pedido:', error);
            showToast(error.message || 'Ocurrió un error al registrar el pedido.');
        } finally {
            if (btnConfirm) {
                btnConfirm.disabled = false;
                btnConfirm.innerHTML = originalBtnHtml;
            }
        }
    }

    // ========================================================
    // MODAL DE PEDIDO (EXACTO COMO LA IMAGEN DEL USUARIO)
    // ========================================================
    let selectedProductForOrder = null;
    let currentModalQty = 1;

    function openOrderModal(producto) {
        selectedProductForOrder = producto;
        currentModalQty = 1;

        // Imagen
        const imgElem = document.getElementById('orderModalImg');
        const placeholderElem = document.getElementById('orderModalImgPlaceholder');
        if (producto.imagen) {
            imgElem.src = producto.imagen;
            imgElem.classList.remove('hidden');
            placeholderElem.classList.add('hidden');
        } else {
            imgElem.classList.add('hidden');
            placeholderElem.classList.remove('hidden');
            const catName = producto.categoria ? producto.categoria.nombre.toLowerCase() : '';
            let icon = 'fa-utensils';
            if (catName.includes('carne') || catName.includes('hamburguesa')) icon = 'fa-drumstick-bite';
            else if (catName.includes('bebida')) icon = 'fa-wine-glass';
            else if (catName.includes('postre')) icon = 'fa-cake-candles';
            else if (catName.includes('marisco')) icon = 'fa-shrimp';
            else if (catName.includes('verdura') || catName.includes('ensalada')) icon = 'fa-seedling';
            else if (catName.includes('pan')) icon = 'fa-bread-slice';
            document.getElementById('orderModalPlaceholderIcon').className = `fas ${icon} text-3xl text-retro-gold mb-2`;
        }

        // Título, Categoría y Descripción
        document.getElementById('orderModalName').textContent = producto.nombre;
        const categoriaNombre = producto.categoria ? producto.categoria.nombre : 'MENÚ';
        document.getElementById('orderModalCategory').textContent = categoriaNombre.toUpperCase();
        document.getElementById('orderModalDesc').textContent = producto.descripcion || 'Preparado con ingredientes frescos de la más alta calidad.';

        // Limpiar campo de observación
        document.getElementById('orderModalObservacion').value = '';

        // Resumen lateral
        document.getElementById('orderModalSummaryName').textContent = producto.nombre;
        const unitPriceFormatted = '$' + new Intl.NumberFormat('es-CO').format(producto.precio);
        document.getElementById('orderModalSummaryUnit').textContent = unitPriceFormatted;

        updateModalCalculations();

        // Mostrar modal con animación suave
        const backdrop = document.getElementById('orderModalBackdrop');
        const card = document.getElementById('orderModalCard');
        backdrop.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 10);
    }

    function closeOrderModal() {
        const backdrop = document.getElementById('orderModalBackdrop');
        const card = document.getElementById('orderModalCard');
        if (!backdrop || !card) return;

        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        backdrop.classList.add('opacity-0');
        setTimeout(() => {
            backdrop.classList.add('hidden');
        }, 250);
    }

    function handleOrderModalBackdropClick(event) {
        if (event.target.id === 'orderModalBackdrop') {
            closeOrderModal();
        }
    }

    function incrementModalQty() {
        const maxStock = (selectedProductForOrder && selectedProductForOrder.inventario) 
            ? selectedProductForOrder.inventario.cantidad 
            : 99;

        if (currentModalQty < maxStock) {
            currentModalQty++;
            updateModalCalculations();
        } else {
            showToast('Stock máximo disponible alcanzado (' + maxStock + ' unid.)');
        }
    }

    function decrementModalQty() {
        if (currentModalQty > 1) {
            currentModalQty--;
            updateModalCalculations();
        }
    }

    function updateModalCalculations() {
        if (!selectedProductForOrder) return;
        document.getElementById('orderModalQtyDisplay').textContent = currentModalQty;
        document.getElementById('orderModalSummaryQty').textContent = currentModalQty;

        const subtotal = selectedProductForOrder.precio * currentModalQty;
        document.getElementById('orderModalSummarySubtotal').textContent = '$' + new Intl.NumberFormat('es-CO').format(subtotal);
    }

    function confirmAddToCart(goToDrawer = false) {
        if (!selectedProductForOrder) return;

        const observacion = document.getElementById('orderModalObservacion').value.trim();
        let cart = getCart();

        const existingIndex = cart.findIndex(item => 
            item.id === selectedProductForOrder.id && 
            (item.observacion || '') === observacion
        );

        if (existingIndex > -1) {
            cart[existingIndex].cantidad += currentModalQty;
        } else {
            cart.push({
                id: selectedProductForOrder.id,
                nombre: selectedProductForOrder.nombre,
                precio: parseFloat(selectedProductForOrder.precio),
                categoria: selectedProductForOrder.categoria ? selectedProductForOrder.categoria.nombre : 'Menú',
                imagen: selectedProductForOrder.imagen || '',
                cantidad: currentModalQty,
                observacion: observacion
            });
        }

        saveCart(cart);
        closeOrderModal();

        showToast(`¡${selectedProductForOrder.nombre} agregado al pedido!`);

        if (goToDrawer) {
            setTimeout(() => {
                openCartDrawer();
            }, 300);
        }
    }

    let toastTimeout = null;
    function showToast(message) {
        const toast = document.getElementById('toastNotification');
        const msgElem = document.getElementById('toastMessage');
        if (!toast || !msgElem) return;

        msgElem.textContent = message;
        toast.classList.remove('translate-y-[-150%]');

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.add('translate-y-[-150%]');
        }, 3000);
    }

    function proceedToDomicilio() {
        const cart = getCart();
        if (cart.length === 0) {
            alert('Tu bandeja está vacía.');
            return;
        }
        window.location.href = "{{ route('domicilios.cliente') }}";
    }

    function proceedToReserva() {
        window.location.href = "{{ route('reservas.cliente') }}";
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCartUI();
    });
</script>
@endpush
