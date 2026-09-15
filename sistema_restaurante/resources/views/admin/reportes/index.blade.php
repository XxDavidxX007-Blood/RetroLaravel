@extends('layouts.admin')

@section('title', 'Panel de Reportes | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="w-7 h-7 rounded-full bg-retro-gold/20 text-retro-gold flex items-center justify-center hover:bg-retro-gold hover:text-black transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <span class="text-gray-400">|</span>
        <span>REPORTES</span>
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-8">

    <!-- CABECERA PRINCIPAL -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="font-heading text-3xl font-bold text-gray-900 tracking-tight">
                Panel de Reportes
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Visualiza el rendimiento y las estadísticas generales del restaurante en tiempo real
            </p>
        </div>

        <!-- Indicador de En Vivo -->
        <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-gray-200 shadow-xs">
            <span class="flex h-2.5 w-2.5 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">En Tiempo Real</span>
            <button onclick="refreshLiveData()" class="text-gray-400 hover:text-black transition ml-1" title="Actualizar datos ahora">
                <i id="refreshIcon" class="fas fa-arrows-rotate text-xs"></i>
            </button>
        </div>
    </div>

    <!-- ALERTAS -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-circle-check text-emerald-600 text-lg"></i>
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
            </div>
            @if(session('ultimo_reporte_id'))
                <button onclick="verReporte({{ session('ultimo_reporte_id') }})" class="text-xs font-bold bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition">
                    Ver Reporte Generado
                </button>
            @endif
        </div>
    @endif

    <!-- ================================================= -->
    <!-- METRICAS SUPERIORES (3 CARDS) -->
    <!-- ================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Card Total Productos -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-2xl bg-[#dbeafe] flex items-center justify-center text-[#2563eb] text-2xl shrink-0">
                <i class="fas fa-box-open"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Productos</p>
                <h3 id="stat_total_productos" class="text-3xl font-extrabold text-gray-900 mt-1 font-heading">{{ $totalProductos }}</h3>
            </div>
        </div>

        <!-- Card Valor Total Inventario -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-2xl bg-[#dcfce7] flex items-center justify-center text-[#16a34a] text-2xl font-bold shrink-0">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total Inventario</p>
                <h3 id="stat_valor_inventario" class="text-3xl font-extrabold text-gray-900 mt-1 font-heading">${{ number_format($valorTotalInventario, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Card Usuarios Registrados -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-2xl bg-[#f3e8ff] flex items-center justify-center text-[#9333ea] text-2xl shrink-0">
                <i class="fas fa-user-group"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios Registrados</p>
                <h3 id="stat_usuarios_registrados" class="text-3xl font-extrabold text-gray-900 mt-1 font-heading">{{ $usuariosRegistrados }}</h3>
            </div>
        </div>

    </div>

    <!-- ================================================= -->
    <!-- GRAFICOS EN TIEMPO REAL -->
    <!-- ================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Gráfico 1: Valor de Inventario por Categoría -->
        <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm flex flex-col justify-between">
            <h3 class="font-heading text-lg font-bold text-gray-900 text-center uppercase tracking-wide mb-4">
                Valor de Inventario por Categoría
            </h3>
            <div class="h-72 relative flex items-center justify-center">
                <canvas id="chartCategoria"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Estado General del Stock -->
        <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm flex flex-col justify-between">
            <h3 class="font-heading text-lg font-bold text-gray-900 text-center uppercase tracking-wide mb-4">
                Estado General del Stock
            </h3>
            <div class="h-72 relative flex items-center justify-center">
                <canvas id="chartStock"></canvas>
            </div>
        </div>

    </div>

    <!-- ================================================= -->
    <!-- GENERADOR DE REPORTES ESPECIALES -->
    <!-- ================================================= -->
    <div class="bg-white rounded-3xl p-7 border border-gray-200 shadow-sm space-y-5">
        
        <div class="flex items-center gap-3">
            <i class="fas fa-file-invoice text-2xl text-gray-900"></i>
            <h3 class="font-heading text-xl font-bold text-gray-900">
                Generador de Reportes Especiales
            </h3>
        </div>

        <hr class="border-gray-100">

        <form action="{{ route('admin.reportes.generar') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf

            <!-- Filtrar por -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase text-gray-600">Filtrar por:</label>
                <select name="filtro" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="todo" selected>Todo el Inventario</option>
                    <option value="stock_bajo">Stock Bajo</option>
                    <option value="sin_stock">Sin Stock (Agotados)</option>
                    @foreach($categorias as $c)
                        <option value="cat_{{ $c->id }}">Categoría: {{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Formato -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase text-gray-600">Formato:</label>
                <select name="formato" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="pantalla" selected>Ver en Pantalla (Imprimible)</option>
                    <option value="csv">Descargar CSV / Excel</option>
                </select>
            </div>

            <!-- Botón Generar -->
            <div>
                <button type="submit" class="w-full bg-[#0a0a0a] text-white hover:bg-black font-semibold py-2.5 px-6 rounded-xl flex items-center justify-center gap-2 shadow-sm transition border border-black hover:border-retro-gold group cursor-pointer text-sm">
                    <i class="fas fa-play text-xs text-retro-gold group-hover:scale-110 transition"></i>
                    <span class="tracking-wide">Generar Reporte</span>
                </button>
            </div>
        </form>

    </div>

    <!-- ================================================= -->
    <!-- HISTORIAL DE REPORTES GUARDADOS -->
    <!-- ================================================= -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden p-7 space-y-5">
        
        <div class="flex items-center gap-3">
            <i class="fas fa-clock-rotate-left text-2xl text-gray-900"></i>
            <h3 class="font-heading text-xl font-bold text-gray-900">
                Historial de Reportes Guardados
            </h3>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-700 font-heading text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="py-4 px-6 font-semibold">ID</th>
                        <th class="py-4 px-6 font-semibold">Fecha y Hora</th>
                        <th class="py-4 px-6 font-semibold">Generado Por</th>
                        <th class="py-4 px-6 font-semibold">Filtro Usado</th>
                        <th class="py-4 px-6 font-semibold">Total Productos</th>
                        <th class="py-4 px-6 font-semibold">Valor ($)</th>
                        <th class="py-4 px-6 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-xs">
                    @forelse($reportes as $r)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-4 px-6 font-bold text-gray-900 font-mono">
                                #REP-{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-4 px-6 text-gray-600 font-mono">
                                {{ $r->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-gray-800 capitalize">
                                {{ $r->generado_por }}
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 font-medium">
                                    {{ $r->filtro_usado }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-bold text-gray-900">
                                {{ $r->total_productos }}
                            </td>
                            <td class="py-4 px-6 font-bold text-emerald-600 font-mono">
                                ${{ number_format($r->valor_total, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Ver / Imprimir -->
                                    <button 
                                        type="button"
                                        onclick="verReporte({{ $r->id }})"
                                        class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition shadow-xs"
                                        title="Ver / Imprimir reporte"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    <!-- Eliminar -->
                                    <form method="POST" action="{{ route('admin.reportes.destroy', $r->id) }}" class="inline" onsubmit="return confirm('¿Eliminar este reporte del historial?')">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit"
                                            class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition shadow-xs"
                                            title="Eliminar reporte"
                                        >
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <i class="fas fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                                No hay reportes guardados en el historial.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reportes->hasPages())
            <div class="pt-2 flex justify-end">
                {{ $reportes->links() }}
            </div>
        @endif

    </div>

</div>


<!-- ================================================= -->
<!-- MODAL: VISOR DE REPORTE IMPRIMIBLE -->
<!-- ================================================= -->
<div id="reporteModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-3xl w-full p-8 shadow-2xl space-y-6 relative animate-fade-in max-h-[90vh] flex flex-col justify-between">
        
        <!-- Cabecera del Modal -->
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-black text-retro-gold flex items-center justify-center">
                    <i class="fas fa-file-invoice text-sm"></i>
                </div>
                <div>
                    <h3 id="rep_modal_titulo" class="font-heading text-xl font-bold uppercase tracking-wider text-gray-900">Reporte de Inventario</h3>
                    <p id="rep_modal_subtitulo" class="text-xs text-gray-500"></p>
                </div>
            </div>
            <button onclick="closeReporteModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <!-- Contenido Imprimible -->
        <div id="printableReportArea" class="overflow-y-auto space-y-4 pr-1">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-2xl border border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 block font-semibold uppercase text-[10px]">Generado Por</span>
                    <span id="rep_modal_usuario" class="font-bold text-gray-800 capitalize text-xs"></span>
                </div>
                <div>
                    <span class="text-gray-400 block font-semibold uppercase text-[10px]">Filtro</span>
                    <span id="rep_modal_filtro" class="font-bold text-gray-800 text-xs"></span>
                </div>
                <div>
                    <span class="text-gray-400 block font-semibold uppercase text-[10px]">Total Ítems</span>
                    <span id="rep_modal_items_count" class="font-bold text-gray-800 text-xs"></span>
                </div>
                <div>
                    <span class="text-gray-400 block font-semibold uppercase text-[10px]">Valor Total</span>
                    <span id="rep_modal_valor_total" class="font-bold text-emerald-600 font-mono text-sm"></span>
                </div>
            </div>

            <!-- Tabla de Ítems del Reporte -->
            <div class="border border-gray-200 rounded-2xl overflow-hidden">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200 font-semibold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-4">Producto</th>
                            <th class="py-2.5 px-4">Categoría</th>
                            <th class="py-2.5 px-4 text-center">Stock</th>
                            <th class="py-2.5 px-4 text-right">Precio</th>
                            <th class="py-2.5 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="rep_modal_tbody" class="divide-y divide-gray-100">
                        <!-- JS inyecta filas -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Acciones del Modal -->
        <div class="pt-4 flex justify-between items-center border-t border-gray-100">
            <button type="button" onclick="closeReporteModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                Cerrar
            </button>
            <button type="button" onclick="window.print()" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#0a0a0a] text-white hover:bg-black border border-black hover:border-retro-gold transition shadow flex items-center gap-2">
                <i class="fas fa-print text-retro-gold"></i>
                <span>Imprimir Reporte</span>
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ===============================================
    // INICIALIZACIÓN DE GRÁFICOS (CHART.JS)
    // ===============================================
    let chartCat = null;
    let chartStk = null;

    const catLabels = @json($catLabels);
    const catValues = @json($catValues);
    const catColors = @json($catColors);

    const stockLabels = @json($stockLabels);
    const stockValues = @json($stockValues);
    const stockColors = @json($stockColors);

    function initCharts() {
        // Gráfico 1: Categorías (Doughnut)
        const ctxCat = document.getElementById('chartCategoria').getContext('2d');
        chartCat = new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: catColors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, family: 'Montserrat' },
                            padding: 10
                        }
                    }
                }
            }
        });

        // Gráfico 2: Stock (Pie)
        const ctxStk = document.getElementById('chartStock').getContext('2d');
        chartStk = new Chart(ctxStk, {
            type: 'pie',
            data: {
                labels: stockLabels,
                datasets: [{
                    data: stockValues,
                    backgroundColor: stockColors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            font: { size: 11, family: 'Montserrat' },
                            padding: 14
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initCharts);

    // ===============================================
    // ACTUALIZACIÓN EN TIEMPO REAL (POLLING / AJAX)
    // ===============================================
    function refreshLiveData() {
        const icon = document.getElementById('refreshIcon');
        icon.classList.add('fa-spin');

        fetch("{{ route('admin.reportes.live-data') }}")
            .then(res => res.json())
            .then(data => {
                // Actualizar tarjetas
                document.getElementById('stat_total_productos').textContent = data.totalProductos;
                document.getElementById('stat_valor_inventario').textContent = '$' + new Intl.NumberFormat('es-CO').format(data.valorTotalInventario);
                document.getElementById('stat_usuarios_registrados').textContent = data.usuariosRegistrados;

                // Actualizar Gráfico 1
                if (chartCat) {
                    chartCat.data.labels = data.catLabels;
                    chartCat.data.datasets[0].data = data.catValues;
                    chartCat.data.datasets[0].backgroundColor = data.catColors;
                    chartCat.update();
                }

                // Actualizar Gráfico 2
                if (chartStk) {
                    chartStk.data.labels = data.stockLabels;
                    chartStk.data.datasets[0].data = data.stockValues;
                    chartStk.update();
                }
            })
            .finally(() => {
                setTimeout(() => icon.classList.remove('fa-spin'), 600);
            });
    }

    // Auto-polling cada 15 segundos para tiempo real
    setInterval(refreshLiveData, 15000);

    // ===============================================
    // VISOR DE REPORTES GUARDADOS
    // ===============================================
    function verReporte(reporteId) {
        fetch(`/admin/reportes/${reporteId}/ver`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('rep_modal_titulo').textContent = `Reporte #REP-${String(data.id).padStart(4, '0')}`;
                document.getElementById('rep_modal_subtitulo').textContent = data.fecha;
                document.getElementById('rep_modal_usuario').textContent = data.generado_por;
                document.getElementById('rep_modal_filtro').textContent = data.filtro_usado;
                document.getElementById('rep_modal_items_count').textContent = data.total_productos + ' productos';
                document.getElementById('rep_modal_valor_total').textContent = data.valor_total;

                const tbody = document.getElementById('rep_modal_tbody');
                if (data.items && data.items.length > 0) {
                    let html = '';
                    data.items.forEach(i => {
                        html += `
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-2.5 px-4 font-semibold text-gray-900">${i.nombre}</td>
                                <td class="py-2.5 px-4 text-gray-500">${i.categoria}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-gray-800">${i.stock}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-gray-600">$${new Intl.NumberFormat('es-CO').format(i.precio)}</td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-gray-900">$${new Intl.NumberFormat('es-CO').format(i.valor_subtotal)}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-gray-400">No hay productos en este reporte.</td></tr>';
                }

                document.getElementById('reporteModal').classList.remove('hidden');
            });
    }

    function closeReporteModal() {
        document.getElementById('reporteModal').classList.add('hidden');
    }
</script>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #reporteModal, #reporteModal * {
            visibility: visible;
        }
        #reporteModal {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            background: white !important;
        }
    }
</style>
@endsection
