@extends('layouts.App')

@section('title', 'Panel Empleado')

@section('header', 'PANEL EMPLEADO')

@push('styles')
<style>
    /* ============================= BIENVENIDA ============================= */
    .welcome-card {
        background: white;
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        border: 1px solid #f3f4f6;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .welcome-avatar {
        width: 3.5rem; height: 3.5rem;
        border-radius: 1rem;
        background: #ede9fe;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 1.4rem;
        color: #7c3aed;
    }
    .welcome-card h1 {
        font-size: 1.45rem;
        font-weight: 700;
        color: #111827;
        font-family: 'Playfair Display', serif;
    }
    .welcome-card p {
        font-size: 0.82rem;
        color: #9ca3af;
        margin-top: 0.15rem;
    }

    /* ============================= STAT CARDS ============================= */
    .mini-stat {
        background: white;
        border-radius: 1.25rem;
        padding: 1.4rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        border: 1px solid #f3f4f6;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .mini-stat-icon {
        width: 2.8rem; height: 2.8rem;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .mini-stat-label { font-size: 0.75rem; color: #9ca3af; font-weight: 500; }
    .mini-stat-number { font-size: 1.9rem; font-weight: 700; color: #111827; line-height: 1.1; margin-top: 0.15rem; }

    /* ============================= ACCESS CARDS ============================= */
    .access-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        border: 1px solid #f3f4f6;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .access-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,0.08); }
    .access-card-icon {
        width: 3rem; height: 3rem;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }
    .access-card h3 { font-size: 1.1rem; font-weight: 700; color: #111827; font-family: 'Playfair Display', serif; }
    .access-card p  { font-size: 0.8rem; color: #9ca3af; }
    .access-card-link {
        font-size: 0.78rem; font-weight: 700; text-decoration: none;
        margin-top: 0.5rem; display: inline-flex; align-items: center; gap: 0.3rem;
        transition: opacity 0.2s;
    }
    .access-card-link:hover { opacity: 0.7; }

    /* ============================= MODAL ============================= */
    #nuevaReservaModal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    #nuevaReservaModal.modal-open {
        display: flex;
    }
    .mesa-card {
        transition: border-color 0.15s, background 0.15s, color 0.15s;
        cursor: pointer;
    }
    .mesa-card.selected {
        border-color: #111827 !important;
        background-color: #f3f4f6 !important;
        color: #111827 !important;
    }
</style>
@endpush

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- ===== BIENVENIDA ===== --}}
    <div class="welcome-card">
        <div class="welcome-avatar">
            <i class="fas fa-user-tie"></i>
        </div>
        <div style="flex:1;">
            <h1>Bienvenido, {{ auth()->user()->name }} 👋</h1>
            <p>Panel de empleado &mdash; {{ now()->translatedFormat('d \d\e F, Y') }}</p>
        </div>
        <button
            type="button"
            onclick="openNuevaReservaModal()"
            style="display:inline-flex;align-items:center;gap:0.5rem;background:#111827;color:white;font-size:0.875rem;font-weight:600;padding:0.625rem 1.25rem;border-radius:0.75rem;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.15);white-space:nowrap;transition:background 0.2s;"
            onmouseover="this.style.background='#000'" onmouseout="this.style.background='#111827'"
        >
            <i class="fas fa-plus" style="font-size:0.7rem;color:#c5a059;"></i>
            Nueva Reserva
        </button>
    </div>

    {{-- ===== ALERTAS FLASH ===== --}}
    @if(session('success'))
        <div style="background:#f0fdf4;border-left:4px solid #22c55e;padding:1rem 1.25rem;border-radius:0 0.75rem 0.75rem 0;display:flex;align-items:center;justify-content:space-between;gap:0.75rem;">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <i class="fas fa-circle-check" style="color:#16a34a;font-size:1.1rem;"></i>
                <p style="font-size:0.875rem;font-weight:500;color:#166534;margin:0;">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#16a34a;font-size:1rem;">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div style="background:#fff1f2;border-left:4px solid #f43f5e;padding:1rem 1.25rem;border-radius:0 0.75rem 0.75rem 0;display:flex;align-items:center;justify-content:space-between;gap:0.75rem;">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <i class="fas fa-circle-exclamation" style="color:#e11d48;font-size:1.1rem;"></i>
                <p style="font-size:0.875rem;font-weight:500;color:#9f1239;margin:0;">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#e11d48;font-size:1rem;">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- ===== STATS HOY ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:#ede9fe;">
                <i class="fas fa-calendar-check" style="color:#7c3aed;"></i>
            </div>
            <div>
                <p class="mini-stat-label">Reservas hoy</p>
                <p class="mini-stat-number">{{ $reservasHoy }}</p>
            </div>
        </div>

        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:#fff7ed;">
                <i class="fas fa-receipt" style="color:#f97316;"></i>
            </div>
            <div>
                <p class="mini-stat-label">Pedidos hoy</p>
                <p class="mini-stat-number">{{ $pedidosHoy }}</p>
            </div>
        </div>

        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:#d1fae5;">
                <i class="fas fa-motorcycle" style="color:#059669;"></i>
            </div>
            <div>
                <p class="mini-stat-label">Domicilios hoy</p>
                <p class="mini-stat-number">{{ $domiciliosHoy }}</p>
            </div>
        </div>

    </div>

    {{-- ===== ACCESOS RÁPIDOS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        <div class="access-card">
            <div class="access-card-icon" style="background:#ede9fe;">
                <i class="fas fa-calendar-check" style="color:#7c3aed;"></i>
            </div>
            <h3>Reservas</h3>
            <p>Ver y gestionar reservas del día</p>
            <a href="{{ route('admin.reservas.index') }}" class="access-card-link" style="color:#7c3aed;">
                Ir a reservas <i class="fas fa-arrow-right" style="font-size:0.65rem;"></i>
            </a>
        </div>

        <div class="access-card">
            <div class="access-card-icon" style="background:#fff7ed;">
                <i class="fas fa-receipt" style="color:#f97316;"></i>
            </div>
            <h3>Pedidos</h3>
            <p>Gestionar pedidos activos</p>
            <a href="{{ route('admin.pedidos.index') }}" class="access-card-link" style="color:#f97316;">
                Ir a pedidos <i class="fas fa-arrow-right" style="font-size:0.65rem;"></i>
            </a>
        </div>

        <div class="access-card">
            <div class="access-card-icon" style="background:#d1fae5;">
                <i class="fas fa-motorcycle" style="color:#059669;"></i>
            </div>
            <h3>Domicilios</h3>
            <p>Gestionar pedidos a domicilio</p>
            <a href="{{ route('admin.domicilios.index') }}" class="access-card-link" style="color:#059669;">
                Ir a domicilios <i class="fas fa-arrow-right" style="font-size:0.65rem;"></i>
            </a>
        </div>

    </div>

</div>

@endsection


{{-- ============================================================ --}}
{{-- MODAL FUERA DEL @section para evitar problemas de anidación  --}}
{{-- ============================================================ --}}
@section('modal')

<div id="nuevaReservaModal">
    <div style="background:white;border-radius:1rem;width:100%;max-width:480px;max-height:92vh;overflow:hidden;box-shadow:0 25px 50px rgba(0,0,0,0.25);">

        {{-- CABECERA --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f3f4f6;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-plus" style="color:#111827;"></i>
                <h3 style="font-size:1.1rem;font-weight:700;color:#111827;font-family:'Playfair Display',serif;margin:0;">Nueva Reserva</h3>
            </div>
            <button
                type="button"
                onclick="closeNuevaReservaModal()"
                style="background:none;border:none;font-size:1.5rem;color:#9ca3af;cursor:pointer;line-height:1;padding:0 4px;"
            >&times;</button>
        </div>

        {{-- FORMULARIO --}}
        <form
            method="POST"
            action="{{ route('admin.reservas.store') }}"
            style="padding:1.25rem 1.5rem;overflow-y:auto;max-height:calc(92vh - 65px);display:flex;flex-direction:column;gap:1rem;"
        >
            @csrf

            {{-- Cliente --}}
            <div>
                <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Cliente</label>
                <select name="cliente_id" style="width:100%;padding:0.625rem 0.75rem;background:white;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;font-family:'Montserrat',sans-serif;">
                    <option value="">Sin cliente asignado</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}">{{ $c->user->name ?? 'Sin nombre' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fecha + Hora --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Fecha</label>
                    <input
                        type="date"
                        name="fecha_reserva"
                        required
                        value="{{ now()->toDateString() }}"
                        style="width:100%;padding:0.625rem 0.75rem;background:white;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;"
                    >
                </div>
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Hora</label>
                    <input
                        type="time"
                        name="hora_reserva"
                        required
                        value="12:00"
                        style="width:100%;padding:0.625rem 0.75rem;background:white;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;"
                    >
                </div>
            </div>

            {{-- Personas + Estado --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Personas</label>
                    <input
                        type="number"
                        name="cantidad_personas"
                        min="1" max="20" required value="2"
                        style="width:100%;padding:0.625rem 0.75rem;background:white;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;"
                    >
                </div>
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Estado</label>
                    <select name="estado_reserva_id" required style="width:100%;padding:0.625rem 0.75rem;background:white;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;font-family:'Montserrat',sans-serif;">
                        @foreach($estados as $e)
                            <option value="{{ $e->id }}" {{ $e->nombre_estado === 'Pendiente' ? 'selected' : '' }}>
                                {{ $e->nombre_estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Mesa --}}
            <div>
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.375rem;">
                    <label style="font-size:0.875rem;font-weight:600;color:#374151;">
                        Mesa <span style="color:#ef4444;">*</span>
                    </label>
                    <span style="font-size:0.75rem;color:#9ca3af;">— las rojas ya tienen reserva en ese horario</span>
                </div>

                {{-- Leyenda --}}
                <div style="display:flex;gap:1rem;margin-bottom:0.5rem;">
                    <span style="display:flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:#4b5563;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                        Disponible
                    </span>
                    <span style="display:flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:#4b5563;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#f87171;display:inline-block;"></span>
                        Ocupada
                    </span>
                </div>

                {{-- Grid de mesas --}}
                @if($mesas->isEmpty())
                    <p style="font-size:0.75rem;color:#9ca3af;">No hay mesas registradas.</p>
                @else
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.5rem;max-height:190px;overflow-y:auto;padding-right:2px;">
                        @foreach($mesas as $m)
                            @php
                                $estadoNombre = strtolower($m->estadoMesa->nombre_estado ?? '');
                                $ocupada = $m->estadoMesa && (
                                    \Illuminate\Support\Str::contains($estadoNombre, 'ocup') ||
                                    \Illuminate\Support\Str::contains($estadoNombre, 'reserv') ||
                                    \Illuminate\Support\Str::contains($estadoNombre, 'mant')
                                );
                                $primerDisponible = $loop->first && !$ocupada;
                            @endphp
                            <label style="cursor:{{ $ocupada ? 'not-allowed' : 'pointer' }};display:block;opacity:{{ $ocupada ? '0.65' : '1' }};">
                                <input
                                    type="radio"
                                    name="mesa_id"
                                    value="{{ $m->id }}"
                                    {{ $primerDisponible ? 'checked' : '' }}
                                    {{ $ocupada ? 'disabled' : 'required' }}
                                    style="position:absolute;opacity:0;width:0;height:0;"
                                    onchange="resaltarMesa(this)"
                                >
                                <div
                                    class="mesa-card {{ $primerDisponible ? 'selected' : '' }}"
                                    data-ocupada="{{ $ocupada ? '1' : '0' }}"
                                    style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;padding:8px 4px;border-radius:0.75rem;border:2px solid {{ $ocupada ? '#fca5a5' : ($primerDisponible ? '#111827' : '#e5e7eb') }};background:{{ $ocupada ? '#fff1f2' : ($primerDisponible ? '#f3f4f6' : 'white') }};color:{{ $ocupada ? '#f87171' : ($primerDisponible ? '#111827' : '#6b7280') }};text-align:center;"
                                >
                                    <i class="fas fa-chair" style="font-size:1.1rem;"></i>
                                    <span style="font-size:0.7rem;font-weight:600;line-height:1.2;">Mesa {{ $m->numero_mesa }}</span>
                                    <span style="font-size:0.65rem;color:#9ca3af;line-height:1.2;">Cap. {{ $m->capacidad }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Botones --}}
            <div style="display:flex;justify-content:flex-end;gap:0.75rem;padding-top:0.75rem;border-top:1px solid #f3f4f6;margin-top:0.25rem;">
                <button
                    type="button"
                    onclick="closeNuevaReservaModal()"
                    style="padding:0.625rem 1.25rem;font-size:0.875rem;font-weight:600;color:#4b5563;background:none;border:none;border-radius:0.75rem;cursor:pointer;transition:background 0.2s;"
                    onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    style="padding:0.625rem 1.5rem;font-size:0.875rem;font-weight:600;color:white;background:#111827;border:none;border-radius:0.75rem;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.15);transition:background 0.2s;"
                    onmouseover="this.style.background='#000'" onmouseout="this.style.background='#111827'"
                >
                    Guardar
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openNuevaReservaModal() {
        document.getElementById('nuevaReservaModal').classList.add('modal-open');
        document.body.style.overflow = 'hidden';
    }

    function closeNuevaReservaModal() {
        document.getElementById('nuevaReservaModal').classList.remove('modal-open');
        document.body.style.overflow = '';
    }

    // Cerrar al hacer clic en el fondo oscuro
    document.getElementById('nuevaReservaModal').addEventListener('click', function(e) {
        if (e.target === this) closeNuevaReservaModal();
    });

    // Resaltar visualmente la mesa seleccionada
    function resaltarMesa(radio) {
        document.querySelectorAll('.mesa-card').forEach(function(card) {
            card.classList.remove('selected');
            if (!card.style.borderColor.includes('fca5a5')) {
                card.style.borderColor = '#e5e7eb';
                card.style.background  = 'white';
                card.style.color       = '#6b7280';
            }
        });
        var selected = radio.nextElementSibling;
        selected.classList.add('selected');
        selected.style.borderColor = '#111827';
        selected.style.background  = '#f3f4f6';
        selected.style.color       = '#111827';
    }

    // Cerrar con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeNuevaReservaModal();
    });
</script>
@endpush
