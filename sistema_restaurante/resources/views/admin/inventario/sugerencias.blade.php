@extends('layouts.admin')

@section('title', 'Sugerencias de Stock | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.inventario.index') }}" class="w-7 h-7 rounded-full bg-retro-gold/20 text-retro-gold flex items-center justify-center hover:bg-retro-gold hover:text-black transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <span class="text-gray-400">|</span>
        <span>SUGERENCIAS DE STOCK</span>
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-8">

    <div class="bg-white border-2 border-black rounded-3xl p-7 shadow-xl space-y-6">

        <div class="flex items-center gap-3">
            <i class="fas fa-lightbulb text-2xl text-[#d97706]"></i>
            <h2 class="font-heading text-2xl font-bold tracking-tight text-gray-900 uppercase">
                PRODUCTOS QUE NECESITAN REPOSICIÓN
            </h2>
        </div>

        @forelse($sugerencias as $s)
            <div class="flex items-center justify-between p-5 rounded-2xl border border-gray-200 hover:border-[#d97706] hover:bg-amber-50/30 transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#fef3c7] flex items-center justify-center">
                        <i class="fas fa-utensils text-[#d97706]"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $s->nombre }}</h4>
                        <p class="text-xs text-gray-500">{{ $s->categoria->nombre ?? '' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Actual: <span class="font-bold text-red-500">{{ $s->inventario->cantidad ?? 0 }}</span></p>
                    <p class="text-xs text-gray-400">Mínimo: {{ $s->inventario->stock_minimo ?? 0 }} · Máximo: {{ $s->inventario->stock_maximo ?? 'N/A' }}</p>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-check-circle text-4xl mb-3 text-green-300 block"></i>
                <p class="text-sm">Todos los productos tienen stock suficiente.</p>
            </div>
        @endforelse

    </div>

</div>

@endsection