@extends('layouts.app')

@section('title', 'Mis Notificaciones | Retro Menú')

@section('header', 'NOTIFICACIONES')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="font-heading text-3xl font-bold">
                Notificaciones
            </h1>
            <p class="text-gray-500 mt-1">
                Consulta tus avisos sobre pedidos, reservas y novedades.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        @if($notificaciones->isEmpty())
            <div class="py-16 text-center px-4">
                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3 text-gray-300 text-2xl">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 font-heading mb-1">
                    Sin notificaciones
                </h3>
                <p class="text-xs text-gray-400">
                    Aquí aparecerán tus alertas y novedades
                </p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($notificaciones as $notif)
                    <div class="p-5 flex items-start justify-between gap-4 {{ !$notif->leida ? 'bg-amber-50/20' : '' }}">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0 text-gray-700">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">{{ $notif->titulo }}</h4>
                                <p class="text-xs text-gray-600 mt-1">{{ $notif->mensaje }}</p>
                                <span class="text-[11px] text-gray-400 mt-1 block">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $notificaciones->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

</div>

@endsection
