@extends('layouts.admin')

@section('title', 'Notificaciones | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <span class="w-1.5 h-6 bg-retro-gold rounded-full inline-block"></span>
        <span class="font-heading font-bold text-gray-900 tracking-wider">NOTIFICACIONES</span>
    </div>
@endsection

@section('content')

<div class="max-w-4xl mx-auto space-y-7 pb-12">

    <!-- CABECERA -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#0a0a0a] text-retro-gold flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h1 class="font-heading text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                        Notificaciones
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500">
                        Centro de alertas, pedidos y novedades del restaurante.
                    </p>
                </div>
            </div>
        </div>

        @if($totalNoLeidas > 0)
            <div>
                <button
                    type="button"
                    onclick="marcarTodasComoLeidas()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 hover:bg-black hover:text-white text-gray-700 text-xs font-bold uppercase tracking-wider transition duration-300"
                >
                    <i class="fas fa-check-double"></i>
                    <span>Marcar todas como leídas</span>
                </button>
            </div>
        @endif
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

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <!-- TABS DE FILTRO -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-6 text-xs sm:text-sm">
            @php $filtroActual = request('filtro', 'todas'); @endphp

            <a
                href="{{ route('notificaciones.index') }}"
                class="pb-1 transition {{ $filtroActual === 'todas' ? 'font-bold text-gray-900 border-b-2 border-black' : 'text-gray-400 hover:text-gray-700 font-medium' }}"
            >
                Todas ({{ $total }})
            </a>

            <a
                href="{{ route('notificaciones.index', ['filtro' => 'no_leidas']) }}"
                class="pb-1 transition {{ $filtroActual === 'no_leidas' ? 'font-bold text-gray-900 border-b-2 border-black' : 'text-gray-400 hover:text-gray-700 font-medium' }}"
            >
                No leídas ({{ $totalNoLeidas }})
            </a>

            <a
                href="{{ route('notificaciones.index', ['filtro' => 'leidas']) }}"
                class="pb-1 transition {{ $filtroActual === 'leidas' ? 'font-bold text-gray-900 border-b-2 border-black' : 'text-gray-400 hover:text-gray-700 font-medium' }}"
            >
                Leídas
            </a>
        </div>

        <!-- LISTADO O ESTADO VACÍO -->
        @if($notificaciones->isEmpty())
            <div class="py-20 text-center px-4">
                <div class="w-20 h-20 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4 text-gray-300 text-3xl">
                    <i class="fas fa-bell-slash"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-gray-800 font-heading mb-1">
                    Sin notificaciones
                </h3>

                <p class="text-xs sm:text-sm text-gray-400 max-w-sm mx-auto">
                    Aquí aparecerán tus alertas y novedades
                </p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($notificaciones as $notif)
                    @php
                        $tipo = strtolower($notif->tipo);
                        $icono = 'fas fa-bell';
                        $iconoBg = 'bg-gray-100 text-gray-700';

                        if (str_contains($tipo, 'domicilio')) {
                            $icono = 'fas fa-motorcycle';
                            $iconoBg = 'bg-blue-50 text-blue-600';
                        } elseif (str_contains($tipo, 'pedido')) {
                            $icono = 'fas fa-receipt';
                            $iconoBg = 'bg-amber-50 text-amber-600';
                        } elseif (str_contains($tipo, 'reserva')) {
                            $icono = 'fas fa-calendar-check';
                            $iconoBg = 'bg-emerald-50 text-emerald-600';
                        } elseif (str_contains($tipo, 'stock') || str_contains($tipo, 'inventario')) {
                            $icono = 'fas fa-box-open';
                            $iconoBg = 'bg-rose-50 text-rose-600';
                        }
                    @endphp

                    <div class="p-5 flex items-start justify-between gap-4 transition hover:bg-gray-50/70 {{ !$notif->leida ? 'bg-amber-50/20' : '' }}">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl {{ $iconoBg }} flex items-center justify-center shrink-0 text-base">
                                <i class="{{ $icono }}"></i>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-gray-900">
                                        {{ $notif->titulo }}
                                    </h4>
                                    @if(!$notif->leida)
                                        <span class="w-2 h-2 rounded-full bg-retro-gold"></span>
                                    @endif
                                </div>

                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                    {{ $notif->mensaje }}
                                </p>

                                <span class="text-[11px] text-gray-400 mt-2 inline-block">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$notif->leida)
                                <button
                                    type="button"
                                    onclick="marcarLeidaIndividual({{ $notif->id }})"
                                    class="text-xs text-gray-400 hover:text-gray-700 p-1.5"
                                    title="Marcar como leída"
                                >
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif

                            <form action="{{ route('notificaciones.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('¿Eliminar notificación?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="text-xs text-gray-300 hover:text-rose-600 p-1.5 transition"
                                    title="Eliminar"
                                >
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- PAGINACIÓN -->
            <div class="p-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                <span>Mostrando {{ $notificaciones->firstItem() ?? 0 }} a {{ $notificaciones->lastItem() ?? 0 }} de {{ $notificaciones->total() }}</span>
                {{ $notificaciones->links('pagination::tailwind') }}
            </div>
        @endif

    </div>

</div>

@endsection

@push('scripts')
<script>
    function marcarLeidaIndividual(id) {
        fetch(`/notificaciones/${id}/leer`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function marcarTodasComoLeidas() {
        fetch(`/notificaciones/leer-todas`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
</script>
@endpush
