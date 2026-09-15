@extends('layouts.app')

@section('title', 'Catálogo de Menú | Retro Restaurant')

@section('header', 'CATÁLOGO DE PRODUCTOS')

@section('content')

<div class="max-w-7xl mx-auto space-y-8">

    <!-- CABECERA DE LA PÁGINA -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="font-heading text-3xl font-bold text-gray-900">
                Nuestra Carta Exclusiva
            </h1>
            <p class="text-gray-500 mt-1">
                Descubre los platillos y bebidas preparados con los más finos ingredientes.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a 
                href="{{ route('pedidos.cliente') }}" 
                class="bg-[#0a0a0a] text-white hover:bg-black font-semibold px-5 py-2.5 rounded-full flex items-center gap-2 border border-black hover:border-retro-gold transition shadow text-xs uppercase tracking-wider"
            >
                <i class="fas fa-receipt text-retro-gold"></i>
                <span>Mis Pedidos</span>
            </a>
        </div>
    </div>

    <!-- NOTIFICACIONES -->
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

    <!-- GRID DE PRODUCTOS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($productos as $p)
            <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between group">
                <!-- Imagen / Icono de Cabecera -->
                <div class="h-44 bg-gradient-to-br from-[#0a0a0a] to-[#262626] relative flex items-center justify-center overflow-hidden">
                    @if($p->imagen)
                        <img src="{{ $p->imagen }}" alt="{{ $p->nombre }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-16 h-16 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-retro-gold text-2xl group-hover:scale-110 transition duration-300">
                            <i class="fas fa-utensils"></i>
                        </div>
                    @endif

                    <!-- Categoría Badge -->
                    <span class="absolute top-3 left-3 bg-black/80 backdrop-blur-md text-retro-gold text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full border border-retro-gold/30">
                        {{ $p->categoria->nombre ?? 'General' }}
                    </span>

                    <!-- Badge Estado -->
                    @if($p->estado)
                        <span class="absolute top-3 right-3 bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow">
                            Disponible
                        </span>
                    @else
                        <span class="absolute top-3 right-3 bg-rose-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow">
                            No disponible
                        </span>
                    @endif
                </div>

                <!-- Detalles -->
                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <h3 class="font-heading text-lg font-bold text-gray-900 group-hover:text-retro-gold transition">
                            {{ $p->nombre }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                            {{ $p->descripcion ?? 'Preparado con ingredientes seleccionados de la más alta calidad.' }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase text-gray-400 font-semibold block">Precio</span>
                            <span class="text-xl font-extrabold text-gray-900 font-heading">
                                ${{ number_format($p->precio, 0, ',', '.') }}
                            </span>
                        </div>

                        <a 
                            href="{{ route('domicilios.cliente') }}" 
                            class="w-10 h-10 rounded-2xl bg-[#0a0a0a] text-retro-gold hover:bg-retro-gold hover:text-black flex items-center justify-center transition shadow"
                            title="Ordenar"
                        >
                            <i class="fas fa-bag-shopping text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">
                <i class="fas fa-utensils text-4xl text-gray-300 mb-3 block"></i>
                <h3 class="text-lg font-bold text-gray-700">No hay productos disponibles por el momento</h3>
                <p class="text-sm text-gray-400 mt-1">Pronto estaremos agregando nuevos platillos a nuestro menú.</p>
            </div>
        @endforelse
    </div>

    <!-- PAGINACIÓN -->
    @if($productos->hasPages())
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-medium">
                Mostrando <span class="font-bold text-gray-900">{{ $productos->firstItem() }}</span> a <span class="font-bold text-gray-900">{{ $productos->lastItem() }}</span> de <span class="font-bold text-gray-900">{{ $productos->total() }}</span> platillos
            </p>
            <div>
                {{ $productos->links() }}
            </div>
        </div>
    @endif

</div>

@endsection
