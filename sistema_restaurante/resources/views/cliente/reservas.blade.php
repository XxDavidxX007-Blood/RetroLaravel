@extends('layouts.App')

@section('title', 'Mis Reservas | Retro Menú')

@section('header', 'MIS RESERVAS')

@section('content')

<div class="max-w-6xl mx-auto">

    {{-- ===== CABECERA ===== --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-heading text-3xl font-bold text-gray-900">Mis reservas</h1>
            <p class="text-gray-500 mt-1 text-sm">Consulta y administra tus reservas.</p>
        </div>
        <button type="button" onclick="abrirModalNueva()"
            style="display:inline-flex;align-items:center;gap:0.5rem;background:#c5a059;color:white;font-size:0.875rem;font-weight:600;padding:0.7rem 1.4rem;border-radius:0.75rem;border:none;cursor:pointer;transition:background 0.2s;box-shadow:0 2px 8px rgba(197,160,89,0.3);"
            onmouseover="this.style.background='#b18e48'" onmouseout="this.style.background='#c5a059'">
            <i class="fas fa-plus" style="font-size:0.75rem;"></i> Nueva reserva
        </button>
    </div>

    {{-- ===== ALERTAS FLASH ===== --}}
    @if(session('success'))
        <div style="background:#f0fdf4;border-left:4px solid #22c55e;padding:0.875rem 1.25rem;border-radius:0 0.75rem 0.75rem 0;display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <i class="fas fa-circle-check" style="color:#16a34a;"></i>
                <p style="font-size:0.875rem;font-weight:500;color:#166534;margin:0;">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#16a34a;font-size:1.2rem;">×</button>
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fff1f2;border-left:4px solid #f43f5e;padding:0.875rem 1.25rem;border-radius:0 0.75rem 0.75rem 0;display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <i class="fas fa-circle-exclamation" style="color:#e11d48;"></i>
                <p style="font-size:0.875rem;font-weight:500;color:#9f1239;margin:0;">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#e11d48;font-size:1.2rem;">×</button>
        </div>
    @endif

    {{-- ===== TABLA DE RESERVAS ===== --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden" style="box-shadow:0 2px 12px rgba(0,0,0,0.04);">

        @if($reservas->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-calendar-check text-5xl mb-4" style="color:#c5a059;display:block;"></i>
                <h2 class="font-heading text-2xl font-semibold text-gray-800">No tienes reservas</h2>
                <p class="text-gray-500 mt-2 text-sm">Cuando realices una reserva aparecerá aquí.</p>
                <button type="button" onclick="abrirModalNueva()"
                    style="margin-top:1.25rem;display:inline-flex;align-items:center;gap:0.5rem;background:#c5a059;color:white;font-size:0.85rem;font-weight:600;padding:0.65rem 1.4rem;border-radius:0.75rem;border:none;cursor:pointer;"
                    onmouseover="this.style.background='#b18e48'" onmouseout="this.style.background='#c5a059'">
                    <i class="fas fa-plus" style="font-size:0.7rem;"></i> Hacer una reserva
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                    <thead>
                        <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                            <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Fecha</th>
                            <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Hora</th>
                            <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Mesa</th>
                            <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Personas</th>
                            <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Estado</th>
                            <th style="padding:0.875rem 1.25rem;text-align:center;font-size:0.7rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $r)
                            @php
                                $estado    = strtolower($r->estadoReserva->nombre_estado ?? 'pendiente');
                                $badge     = match(true) {
                                    str_contains($estado, 'confirm') => 'background:#dcfce7;color:#16a34a;',
                                    str_contains($estado, 'cancel')  => 'background:#ffe4e6;color:#e11d48;',
                                    str_contains($estado, 'complet') => 'background:#dbeafe;color:#2563eb;',
                                    default                          => 'background:#fef3c7;color:#d97706;',
                                };
                                $hora = $r->hora_reserva
                                    ? \Carbon\Carbon::parse($r->hora_reserva)->format('H:i')
                                    : '—';
                            @endphp
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:1rem 1.25rem;font-weight:600;color:#111827;">
                                    {{ $r->fecha_reserva ? \Carbon\Carbon::parse($r->fecha_reserva)->format('d/m/Y') : '—' }}
                                    <span style="display:block;font-size:0.72rem;font-weight:400;color:#9ca3af;">
                                        {{ $r->fecha_reserva ? \Carbon\Carbon::parse($r->fecha_reserva)->translatedFormat('l') : '' }}
                                    </span>
                                </td>
                                <td style="padding:1rem 1.25rem;color:#374151;font-family:monospace;">{{ $hora }}</td>
                                <td style="padding:1rem 1.25rem;font-weight:600;color:#374151;">
                                    Mesa {{ $r->mesa->numero_mesa ?? '—' }}
                                    <span style="display:block;font-size:0.72rem;font-weight:400;color:#9ca3af;">Cap. {{ $r->mesa->capacidad ?? '' }}</span>
                                </td>
                                <td style="padding:1rem 1.25rem;color:#374151;">{{ $r->cantidad_personas }}</td>
                                <td style="padding:1rem 1.25rem;">
                                    <span style="padding:0.25rem 0.75rem;border-radius:999px;font-size:0.72rem;font-weight:600;{{ $badge }}">
                                        {{ $r->estadoReserva->nombre_estado ?? 'Pendiente' }}
                                    </span>
                                </td>
                                <td style="padding:1rem 1.25rem;text-align:center;">
                                    <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                                        {{-- Botón editar --}}
                                        <button
                                            type="button"
                                            onclick="abrirModalEditar({{ json_encode([
                                                'id'               => $r->id,
                                                'mesa_id'          => $r->mesa_id,
                                                'fecha_reserva'    => $r->fecha_reserva?->format('Y-m-d'),
                                                'hora_reserva'     => $hora,
                                                'cantidad_personas'=> $r->cantidad_personas,
                                                'estado_reserva_id'=> $r->estado_reserva_id,
                                            ]) }})"
                                            style="width:2rem;height:2rem;border-radius:50%;background:#eff6ff;color:#2563eb;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                            onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'"
                                            title="Editar reserva"
                                        >
                                            <i class="fas fa-pen" style="font-size:0.7rem;"></i>
                                        </button>
                                        {{-- Formulario eliminar --}}
                                        <form method="POST" action="{{ route('reservas.cliente.destroy', $r->id) }}"
                                            onsubmit="return confirm('¿Eliminar esta reserva?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                style="width:2rem;height:2rem;border-radius:50%;background:#fff1f2;color:#e11d48;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                                onmouseover="this.style.background='#ffe4e6'" onmouseout="this.style.background='#fff1f2'"
                                                title="Eliminar reserva">
                                                <i class="fas fa-trash" style="font-size:0.7rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection


{{-- ================================================================ --}}
{{-- MODALES                                                           --}}
{{-- ================================================================ --}}
@section('modal')

{{-- ---- HELPER: bloque de mesas reutilizable (macro Blade) ----
     Se incluye dentro de cada modal via JS, renderizado en servidor --}}

{{-- ===== MODAL NUEVA RESERVA ===== --}}
<div id="modalNueva" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:1.25rem;width:100%;max-width:490px;max-height:92vh;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.25);">

        {{-- Cabecera --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f3f4f6;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-plus" style="color:#111827;"></i>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#111827;margin:0;">Nueva Reserva</h3>
            </div>
            <button type="button" onclick="cerrarModalNueva()"
                style="background:none;border:none;font-size:1.5rem;color:#9ca3af;cursor:pointer;line-height:1;padding:0 4px;"
                onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#9ca3af'">&times;</button>
        </div>

        <form method="POST" action="{{ route('reservas.cliente.store') }}"
            style="padding:1.25rem 1.5rem;overflow-y:auto;max-height:calc(92vh - 65px);display:flex;flex-direction:column;gap:1rem;">
            @csrf

            {{-- Cliente --}}
            <div>
                <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Cliente</label>
                <select name="cliente_id" style="width:100%;padding:0.625rem 0.75rem;background:white;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                    <option value="">Sin cliente asignado</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}" {{ (Auth::user()->cliente && Auth::user()->cliente->id === $c->id) ? 'selected' : '' }}>
                            {{ $c->user->name ?? 'Sin nombre' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Personas + Estado --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Personas</label>
                    <input type="number" name="cantidad_personas" min="1" max="20" required value="2"
                        style="width:100%;padding:0.625rem 0.75rem;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Estado</label>
                    <select name="estado_reserva_id" required style="width:100%;padding:0.625rem 0.75rem;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                        @foreach($estados as $e)
                            <option value="{{ $e->id }}" {{ $e->nombre_estado === 'Pendiente' ? 'selected' : '' }}>{{ $e->nombre_estado }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fecha + Hora --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Fecha</label>
                    <input type="date" name="fecha_reserva" required value="{{ now()->toDateString() }}"
                        style="width:100%;padding:0.625rem 0.75rem;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Hora</label>
                    <input type="time" name="hora_reserva" required value="12:00"
                        style="width:100%;padding:0.625rem 0.75rem;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                </div>
            </div>

            @include('cliente._mesas_grid', ['prefijo' => 'nueva', 'mesaActualId' => null])

            <div style="display:flex;justify-content:flex-end;gap:0.75rem;padding-top:0.875rem;border-top:1px solid #f3f4f6;">
                <button type="button" onclick="cerrarModalNueva()"
                    style="padding:0.625rem 1.25rem;font-size:0.875rem;font-weight:600;color:#4b5563;background:none;border:none;border-radius:0.75rem;cursor:pointer;"
                    onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit"
                    style="padding:0.625rem 1.5rem;font-size:0.875rem;font-weight:600;color:white;background:#111827;border:none;border-radius:0.75rem;cursor:pointer;"
                    onmouseover="this.style.background='#000'" onmouseout="this.style.background='#111827'">Guardar</button>
            </div>
        </form>
    </div>
</div>


{{-- ===== MODAL EDITAR RESERVA ===== --}}
<div id="modalEditar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:1.25rem;width:100%;max-width:490px;max-height:92vh;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.25);">

        {{-- Cabecera --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f3f4f6;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-pen" style="color:#2563eb;"></i>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#111827;margin:0;">Editar Reserva</h3>
            </div>
            <button type="button" onclick="cerrarModalEditar()"
                style="background:none;border:none;font-size:1.5rem;color:#9ca3af;cursor:pointer;line-height:1;padding:0 4px;"
                onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#9ca3af'">&times;</button>
        </div>

        <form id="formEditar" method="POST" action=""
            style="padding:1.25rem 1.5rem;overflow-y:auto;max-height:calc(92vh - 65px);display:flex;flex-direction:column;gap:1rem;">
            @csrf
            @method('PUT')

            {{-- Personas + Estado --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Personas</label>
                    <input type="number" id="edit_personas" name="cantidad_personas" min="1" max="20" required
                        style="width:100%;padding:0.625rem 0.75rem;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Estado</label>
                    <select id="edit_estado" name="estado_reserva_id" required
                        style="width:100%;padding:0.625rem 0.75rem;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                        @foreach($estados as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre_estado }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fecha + Hora --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Fecha</label>
                    <input type="date" id="edit_fecha" name="fecha_reserva" required
                        style="width:100%;padding:0.625rem 0.75rem;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">Hora</label>
                    <input type="time" id="edit_hora" name="hora_reserva" required
                        style="width:100%;padding:0.625rem 0.75rem;border:1px solid #d1d5db;border-radius:0.75rem;font-size:0.875rem;color:#374151;outline:none;box-sizing:border-box;">
                </div>
            </div>

            @include('cliente._mesas_grid', ['prefijo' => 'editar', 'mesaActualId' => null])

            <div style="display:flex;justify-content:flex-end;gap:0.75rem;padding-top:0.875rem;border-top:1px solid #f3f4f6;">
                <button type="button" onclick="cerrarModalEditar()"
                    style="padding:0.625rem 1.25rem;font-size:0.875rem;font-weight:600;color:#4b5563;background:none;border:none;border-radius:0.75rem;cursor:pointer;"
                    onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">Cancelar</button>
                <button type="submit"
                    style="padding:0.625rem 1.5rem;font-size:0.875rem;font-weight:600;color:white;background:#2563eb;border:none;border-radius:0.75rem;cursor:pointer;"
                    onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">Actualizar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ---- MODAL NUEVA ----
    function abrirModalNueva() {
        document.getElementById('modalNueva').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function cerrarModalNueva() {
        document.getElementById('modalNueva').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.getElementById('modalNueva').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalNueva();
    });

    // ---- MODAL EDITAR ----
    function abrirModalEditar(r) {
        var form = document.getElementById('formEditar');
        form.action = '/mis-reservas/' + r.id;

        document.getElementById('edit_personas').value = r.cantidad_personas;
        document.getElementById('edit_fecha').value    = r.fecha_reserva;
        document.getElementById('edit_hora').value     = r.hora_reserva ? r.hora_reserva.substring(0,5) : '12:00';
        document.getElementById('edit_estado').value   = r.estado_reserva_id;

        // Desbloquear y resetear todas las mesas del modal editar
        document.querySelectorAll('.editar-mesa-radio').forEach(function(radio) {
            var card = radio.nextElementSibling;
            var isActual = parseInt(radio.value) === parseInt(r.mesa_id);

            if (isActual) {
                // La mesa actual de esta reserva siempre se puede re-seleccionar
                radio.disabled = false;
                radio.checked  = true;
                radio.parentElement.style.opacity = '1';
                radio.parentElement.style.cursor  = 'pointer';
                card.dataset.ocupada   = '0';
                card.style.borderColor = '#111827';
                card.style.background  = '#f3f4f6';
                card.style.color       = '#111827';
            } else {
                radio.checked = false;
                if (card.dataset.ocupadaOriginal === '1') {
                    card.style.borderColor = '#fca5a5';
                    card.style.background  = '#fff1f2';
                    card.style.color       = '#f87171';
                } else {
                    card.style.borderColor = '#e5e7eb';
                    card.style.background  = 'white';
                    card.style.color       = '#6b7280';
                }
            }
        });

        document.getElementById('modalEditar').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function cerrarModalEditar() {
        document.getElementById('modalEditar').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.getElementById('modalEditar').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalEditar();
    });

    // ---- Escape cierra cualquier modal ----
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { cerrarModalNueva(); cerrarModalEditar(); }
    });

    // ---- Selección visual de mesa ----
    function seleccionarMesa(radio) {
        var contenedor = radio.closest('div[style*="grid-template-columns"]');
        if (!contenedor) return;
        contenedor.querySelectorAll('.mesa-item').forEach(function(card) {
            if (card.dataset.ocupada === '1') return;
            card.style.borderColor = '#e5e7eb';
            card.style.background  = 'white';
            card.style.color       = '#6b7280';
        });
        var card = radio.nextElementSibling;
        card.style.borderColor = '#111827';
        card.style.background  = '#f3f4f6';
        card.style.color       = '#111827';
    }

    // Abrir modal nueva si hay errores de validación en store
    @if($errors->any())
        abrirModalNueva();
    @endif
</script>
@endpush
