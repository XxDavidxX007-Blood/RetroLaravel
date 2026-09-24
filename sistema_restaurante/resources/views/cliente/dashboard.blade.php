@extends('layouts.App')

@section('title', 'Dashboard Cliente')

@section('header', 'DASHBOARD')

@push('styles')
<style>
    /* ============================= STAT CARDS ============================= */
    .stat-card {
        background: #0f1524;
        border-radius: 1.25rem;
        padding: 1.75rem;
        color: white;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.25); }
    .stat-card-icon {
        width: 2.4rem; height: 2.4rem;
        border-radius: 0.6rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem; margin-bottom: 0.4rem;
    }
    .stat-card-label  { font-size: 0.78rem; color: #9ca3af; font-weight: 500; }
    .stat-card-number { font-size: 2.6rem; font-weight: 700; line-height: 1; margin: 0.2rem 0; }
    .stat-card-sub    { font-size: 0.73rem; color: #9ca3af; }
    .stat-card-link   {
        font-size: 0.73rem; color: #c5a059; font-weight: 600;
        margin-top: 0.7rem; text-decoration: none; letter-spacing: 0.02em; transition: opacity 0.2s;
    }
    .stat-card-link:hover { opacity: 0.7; }

    /* ============================= TABLA RESERVAS ============================= */
    .reservas-table th {
        font-size: 0.67rem; font-weight: 700; letter-spacing: 0.1em; color: #9ca3af;
        padding: 0.7rem 1rem; text-align: left; border-bottom: 1px solid #f3f4f6;
    }
    .reservas-table td {
        padding: 0.9rem 1rem; font-size: 0.84rem;
        border-bottom: 1px solid #f9fafb; vertical-align: middle;
    }
    .reservas-table tr:last-child td { border-bottom: none; }
    .badge-estado {
        padding: 0.25rem 0.7rem; border-radius: 999px;
        font-size: 0.68rem; font-weight: 600;
    }

    /* ============================= RETRO PUNTOS ============================= */
    .puntos-card {
        background: linear-gradient(135deg, #0f1524 0%, #1a2035 100%);
        border-radius: 1.25rem; padding: 2rem; color: white;
        border: 1px solid rgba(197,160,89,0.2);
        position: relative; overflow: hidden;
    }
    .puntos-card::before {
        content: ''; position: absolute; top: -40px; right: -40px;
        width: 160px; height: 160px; border-radius: 50%;
        background: rgba(197,160,89,0.07); pointer-events: none;
    }
    .progress-bar-track {
        height: 8px; background: rgba(255,255,255,0.1);
        border-radius: 999px; margin: 1rem 0 0.4rem; overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%; background: linear-gradient(90deg, #c5a059, #d4b773);
        border-radius: 999px; transition: width 1.2s ease;
    }
    .puntos-ver-btn {
        display: block; width: 100%; text-align: center; margin-top: 1.25rem;
        padding: 0.7rem; border: 1px solid rgba(197,160,89,0.4);
        border-radius: 0.75rem; color: #c5a059; font-size: 0.85rem;
        font-weight: 600; letter-spacing: 0.05em; text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }
    .puntos-ver-btn:hover { background: rgba(197,160,89,0.12); color: #d4b773; }

    /* ============================= BUSCADOR ============================= */
    .dashboard-search { position: relative; width: 260px; }
    .dashboard-search input {
        width: 100%; border: 1px solid #e5e7eb; border-radius: 999px;
        padding: 0.5rem 1rem 0.5rem 2.4rem; font-size: 0.84rem; outline: none;
        background: white; transition: border-color 0.2s, box-shadow 0.2s;
        font-family: 'Montserrat', sans-serif;
    }
    .dashboard-search input:focus { border-color: #c5a059; box-shadow: 0 0 0 3px rgba(197,160,89,0.15); }
    .dashboard-search i { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.78rem; }

    /* ============================= TRUST STRIP ============================= */
    .trust-strip {
        display: grid; grid-template-columns: repeat(4,1fr); gap: 1.5rem;
        padding: 1.5rem 2rem; background: white;
        border-radius: 1.25rem; border: 1px solid #f3f4f6;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .trust-item { display: flex; align-items: center; gap: 0.75rem; }
    .trust-icon {
        width: 2.4rem; height: 2.4rem; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 0.95rem;
    }
    .trust-item p    { font-size: 0.77rem; font-weight: 600; color: #374151; margin: 0; line-height: 1.3; }
    .trust-item span { font-size: 0.68rem; color: #9ca3af; }
    @media (max-width: 768px) { .trust-strip { grid-template-columns: repeat(2,1fr); } .dashboard-search { width: 100%; } }
</style>
@endpush

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ===== BIENVENIDA + BUSCADOR ===== --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-heading text-3xl text-gray-900">
                ¡Bienvenido, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-gray-400 mt-1 text-sm">¿Qué quieres disfrutar hoy?</p>
        </div>
        <div class="dashboard-search">
            <i class="fas fa-search"></i>
            <input
                type="text"
                id="dashboardSearch"
                placeholder="Buscar platos, bebidas..."
                onkeydown="if(event.key==='Enter') window.location='{{ route('productos.index') }}?q='+this.value"
            >
        </div>
    </div>

    {{-- ===== TARJETAS DE ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(99,102,241,0.15);">
                <i class="fas fa-calendar-check" style="color:#818cf8;"></i>
            </div>
            <p class="stat-card-label">Mis Reservas</p>
            <p class="stat-card-number">{{ $totalReservas }}</p>
            <p class="stat-card-sub">próximas</p>
            <a href="{{ route('reservas.cliente') }}" class="stat-card-link">Ver todas →</a>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(251,146,60,0.15);">
                <i class="fas fa-receipt" style="color:#fb923c;"></i>
            </div>
            <p class="stat-card-label">Mis Pedidos</p>
            <p class="stat-card-number">{{ $totalPedidos }}</p>
            <p class="stat-card-sub">pedidos</p>
            <a href="{{ route('pedidos.cliente') }}" class="stat-card-link">Ver historial →</a>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(239,68,68,0.15);">
                <i class="fas fa-motorcycle" style="color:#f87171;"></i>
            </div>
            <p class="stat-card-label">Domicilios</p>
            <p class="stat-card-number">{{ $totalDomicilios }}</p>
            <p class="stat-card-sub">Pide a domicilio</p>
            <a href="{{ route('domicilios.cliente') }}" class="stat-card-link">Ordenar ahora →</a>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(20,184,166,0.15);">
                <i class="fas fa-utensils" style="color:#2dd4bf;"></i>
            </div>
            <p class="stat-card-label">Menú</p>
            <p class="stat-card-number">{{ $totalProductos > 0 ? $totalProductos.'+' : $totalProductos }}</p>
            <p class="stat-card-sub">Explorar carta</p>
            <a href="{{ route('productos.index') }}" class="stat-card-link">Ver menú →</a>
        </div>

    </div>

    {{-- ===== MIS RESERVAS PRÓXIMAS ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" style="box-shadow:0 2px 12px rgba(0,0,0,0.04);">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-base">📅 Mis reservas próximas</h2>
            <a href="{{ route('reservas.cliente') }}" class="text-sm font-semibold" style="color:#c5a059;">Ver todas</a>
        </div>

        @if($reservasProximas->count() > 0)
        <div class="overflow-x-auto">
            <table class="reservas-table w-full">
                <thead>
                    <tr>
                        <th>FECHA</th>
                        <th>HORA</th>
                        <th>MESA</th>
                        <th>PERSONAS</th>
                        <th>ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservasProximas as $reserva)
                    <tr>
                        <td>
                            <p class="font-semibold text-gray-800">{{ $reserva->fecha_reserva->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $reserva->fecha_reserva->translatedFormat('l') }}</p>
                        </td>
                        <td class="text-gray-600">
                            {{ \Carbon\Carbon::parse($reserva->hora_reserva)->format('g:i A') }}
                        </td>
                        <td class="font-semibold text-gray-800">
                            Mesa {{ $reserva->mesa->numero ?? $reserva->mesa_id }}
                        </td>
                        <td class="text-gray-600">{{ $reserva->cantidad_personas }} personas</td>
                        <td>
                            @php
                                $estado = $reserva->estadoReserva->nombre ?? 'Pendiente';
                                $colores = [
                                    'Pendiente'  => 'background:#fef3c7;color:#d97706;',
                                    'Confirmada' => 'background:#d1fae5;color:#065f46;',
                                    'Cancelada'  => 'background:#fee2e2;color:#dc2626;',
                                    'Completada' => 'background:#e0f2fe;color:#0369a1;',
                                ];
                                $estilo = $colores[$estado] ?? 'background:#f3f4f6;color:#374151;';
                            @endphp
                            <span class="badge-estado" style="{{ $estilo }}">{{ $estado }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                <i class="fas fa-calendar-times text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Sin reservas próximas</p>
            <p class="text-gray-400 text-xs mt-1">Haz una reserva y aparecerá aquí</p>
            <a href="{{ route('reservas.cliente') }}"
               class="mt-4 px-5 py-2 rounded-full text-sm font-semibold text-white"
               style="background:linear-gradient(135deg,#c5a059,#d4b773);">
                Hacer reserva
            </a>
        </div>
        @endif

    </div>

    {{-- ===== RETRO PUNTOS ===== --}}
    @php
        $puntos   = 1250;
        $meta     = 2000;
        $progreso = min(100, round(($puntos / $meta) * 100));
        $faltan   = $meta - $puntos;
        $nivel    = $puntos >= 2000 ? 'Cliente Platinum' : ($puntos >= 1000 ? 'Cliente Oro' : 'Cliente Silver');
    @endphp

    <div class="puntos-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold tracking-widest uppercase" style="color:#c5a059;">⭐ RETRO PUNTOS</p>
                <p class="text-5xl font-bold mt-3 text-white">
                    {{ number_format($puntos) }}
                    <span class="text-2xl font-normal text-gray-400 ml-1">pts</span>
                </p>
                <p class="text-sm font-semibold mt-1" style="color:#c5a059;">{{ $nivel }}</p>
            </div>
            <div class="text-3xl opacity-30">👑</div>
        </div>
        <div class="progress-bar-track">
            <div class="progress-bar-fill" style="width:{{ $progreso }}%;"></div>
        </div>
        <p class="text-xs text-gray-400">Faltan {{ number_format($faltan) }} pts para tu próximo descuento</p>
        <a href="{{ route('perfil') }}" class="puntos-ver-btn">Ver beneficios</a>
    </div>

    {{-- ===== FRANJA DE CONFIANZA ===== --}}
    <div class="trust-strip">
        <div class="trust-item">
            <div class="trust-icon" style="background:#fef3c7;">
                <i class="fas fa-shield-alt" style="color:#d97706;"></i>
            </div>
            <div><p>Pago 100% seguro</p><span>Tus datos están protegidos</span></div>
        </div>
        <div class="trust-item">
            <div class="trust-icon" style="background:#dbeafe;">
                <i class="fas fa-headset" style="color:#2563eb;"></i>
            </div>
            <div><p>Atención 24/7</p><span>Siempre para ayudarte</span></div>
        </div>
        <div class="trust-item">
            <div class="trust-icon" style="background:#d1fae5;">
                <i class="fas fa-shipping-fast" style="color:#059669;"></i>
            </div>
            <div><p>Envíos rápidos</p><span>Directo a tu puerta</span></div>
        </div>
        <div class="trust-item">
            <div class="trust-icon" style="background:#fce7f3;">
                <i class="fas fa-star" style="color:#db2777;"></i>
            </div>
            <div><p>Calidad garantizada</p><span>Ingredientes seleccionados</span></div>
        </div>
    </div>

</div>

@endsection