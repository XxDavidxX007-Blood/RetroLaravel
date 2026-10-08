{{--
    Partial: grid de mesas para modales de reserva.
    Variables esperadas:
      $prefijo      → 'nueva' | 'editar'  (para clases CSS únicas)
      $mesaActualId → id de la mesa ya reservada (null en nueva)
--}}
<div>
    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.375rem;flex-wrap:wrap;">
        <label style="font-size:0.875rem;font-weight:600;color:#374151;">
            Mesa <span style="color:#ef4444;">*</span>
        </label>
        <span style="font-size:0.72rem;color:#9ca3af;">— las rojas ya están reservadas en ese horario</span>
    </div>

    {{-- Leyenda --}}
    <div style="display:flex;gap:1rem;margin-bottom:0.6rem;">
        <span style="display:flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:#4b5563;">
            <span style="width:9px;height:9px;border-radius:50%;background:#22c55e;display:inline-block;"></span>Disponible
        </span>
        <span style="display:flex;align-items:center;gap:0.375rem;font-size:0.75rem;color:#4b5563;">
            <span style="width:9px;height:9px;border-radius:50%;background:#f87171;display:inline-block;"></span>Ocupada
        </span>
    </div>

    @if($mesas->isEmpty())
        <p style="font-size:0.75rem;color:#9ca3af;">No hay mesas registradas.</p>
    @else
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.5rem;max-height:190px;overflow-y:auto;padding-right:2px;">
            @php $primerDisponibleMarcado = false; @endphp
            @foreach($mesas as $m)
                @php
                    $enombre  = strtolower($m->estadoMesa->nombre_estado ?? '');
                    // La mesa actual (en edición) se muestra disponible aunque esté "Reservada"
                    $esActual = ($mesaActualId !== null && $m->id == $mesaActualId);
                    $ocupada  = !$esActual && $m->estadoMesa && (
                        \Illuminate\Support\Str::contains($enombre, 'ocup') ||
                        \Illuminate\Support\Str::contains($enombre, 'reserv') ||
                        \Illuminate\Support\Str::contains($enombre, 'mant')
                    );
                    // Marcar primera disponible (o la actual en edición)
                    $seleccionar = false;
                    if (!$ocupada && !$primerDisponibleMarcado) {
                        $seleccionar = true;
                        $primerDisponibleMarcado = true;
                    }
                    $borderColor = $ocupada  ? '#fca5a5' : ($seleccionar ? '#111827' : '#e5e7eb');
                    $bgColor     = $ocupada  ? '#fff1f2' : ($seleccionar ? '#f3f4f6' : 'white');
                    $txtColor    = $ocupada  ? '#f87171' : ($seleccionar ? '#111827' : '#6b7280');
                @endphp
                <label style="cursor:{{ $ocupada ? 'not-allowed' : 'pointer' }};display:block;opacity:{{ $ocupada ? '0.65' : '1' }};">
                    <input
                        type="radio"
                        name="mesa_id"
                        value="{{ $m->id }}"
                        {{ $seleccionar ? 'checked' : '' }}
                        {{ $ocupada ? 'disabled' : 'required' }}
                        class="{{ $prefijo }}-mesa-radio"
                        style="position:absolute;opacity:0;width:0;height:0;"
                        onchange="seleccionarMesa(this)"
                    >
                    <div
                        class="mesa-item"
                        data-ocupada="{{ $ocupada ? '1' : '0' }}"
                        data-ocupada-original="{{ $ocupada ? '1' : '0' }}"
                        style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;padding:8px 4px;border-radius:0.75rem;border:2px solid {{ $borderColor }};background:{{ $bgColor }};color:{{ $txtColor }};text-align:center;transition:all 0.15s;"
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
