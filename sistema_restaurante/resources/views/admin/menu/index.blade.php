@extends('layouts.admin')

@section('title', 'Gestión de Menú | Administrador')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="w-7 h-7 rounded-full bg-retro-gold/20 text-retro-gold flex items-center justify-center hover:bg-retro-gold hover:text-black transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <span class="text-gray-400">|</span>
        <span>GESTIÓN DE MENÚ</span>
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-7">

    <!-- CABECERA PRINCIPAL Y BOTÓN -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="font-heading text-3xl font-bold text-gray-900 tracking-tight">
                Gestión de Menú
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Administra los platos, bebidas y combos que se muestran en el catálogo del cliente
            </p>
        </div>

        <button 
            onclick="openCreateMenuModal()"
            class="bg-[#2563eb] text-white hover:bg-blue-700 font-semibold px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition text-sm cursor-pointer"
        >
            <i class="fas fa-plus text-xs"></i>
            <span>Agregar Plato al Menú</span>
        </button>
    </div>

    <!-- ALERTAS / NOTIFICACIONES -->
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

    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-circle-exclamation text-rose-600 text-lg"></i>
                <p class="text-sm font-medium text-rose-800">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl space-y-1 shadow-sm">
            <div class="flex items-center gap-2 text-amber-800 font-semibold text-sm">
                <i class="fas fa-triangle-exclamation"></i>
                <span>Por favor verifica los siguientes errores:</span>
            </div>
            <ul class="list-disc list-inside text-xs text-amber-700 pl-4 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ================================================= -->
    <!-- METRICAS SUPERIORES -->
    <!-- ================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Card Total Platos -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-2xl bg-[#dbeafe] flex items-center justify-center text-[#2563eb] text-2xl shrink-0">
                <i class="fas fa-utensils"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Platos</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $totalPlatos }}</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">En el catálogo</p>
            </div>
        </div>

        <!-- Card Platos Ocultos -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-700 text-2xl shrink-0">
                <i class="fas fa-eye-slash"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Platos Ocultos</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">{{ $platosOcultos }}</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">No visibles para el cliente</p>
            </div>
        </div>

        <!-- Card Precio Promedio -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-2xl bg-[#dcfce7] flex items-center justify-center text-[#16a34a] text-2xl shrink-0">
                <i class="fas fa-tag"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Precio Promedio</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5 font-heading">${{ number_format($precioPromedio, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Por plato</p>
            </div>
        </div>

    </div>

    <!-- ================================================= -->
    <!-- BUSCADOR Y FILTROS -->
    <!-- ================================================= -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.menu.index') }}" class="w-full flex flex-col md:flex-row items-center gap-3">
            <!-- Buscar -->
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Buscar plato o bebida..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black shadow-sm"
                >
            </div>

            <!-- Categoría -->
            <select name="categoria_id" onchange="this.form.submit()" class="w-full md:w-52 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black shadow-sm text-gray-700">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>

            <!-- Visibilidad -->
            <select name="visibilidad" onchange="this.form.submit()" class="w-full md:w-56 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black shadow-sm text-gray-700">
                <option value="">Todos (Visibles/Ocultos)</option>
                <option value="visible" {{ request('visibilidad') == 'visible' ? 'selected' : '' }}>Visibles</option>
                <option value="oculto" {{ request('visibilidad') == 'oculto' ? 'selected' : '' }}>Ocultos</option>
            </select>

            <!-- Botón Limpiar -->
            @if(request()->hasAny(['search', 'categoria_id', 'visibilidad']))
                <a href="{{ route('admin.menu.index') }}" class="w-full md:w-auto px-4 py-2.5 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center gap-1.5 shadow-sm transition">
                    <i class="fas fa-filter text-xs text-gray-400"></i>
                    <span>Limpiar</span>
                </a>
            @endif
        </form>
    </div>

    <!-- ================================================= -->
    <!-- TABLA DE PLATOS DEL MENÚ -->
    <!-- ================================================= -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-700 font-heading text-sm border-b border-gray-200">
                        <th class="py-4 px-6 font-semibold">Plato / Bebida</th>
                        <th class="py-4 px-6 font-semibold">Categoría</th>
                        <th class="py-4 px-6 font-semibold">Descripción</th>
                        <th class="py-4 px-6 font-semibold">Precio</th>
                        <th class="py-4 px-6 font-semibold">Disponibilidad</th>
                        <th class="py-4 px-6 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-sm">
                    @forelse($productos as $p)
                        <tr class="hover:bg-gray-50/70 transition">
                            <!-- Plato / Bebida -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center shadow-xs">
                                        @if($p->imagen)
                                            <img src="{{ $p->imagen }}" alt="{{ $p->nombre }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                                                <i class="fas fa-utensils text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-bold text-gray-900 capitalize">
                                        {{ strtolower($p->nombre) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Categoría -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#dbeafe] text-[#1d4ed8]">
                                    {{ $p->categoria->nombre ?? 'General' }}
                                </span>
                            </td>

                            <!-- Descripción -->
                            <td class="py-4 px-6 text-gray-500 text-xs max-w-xs truncate">
                                {{ $p->descripcion ?? 'Sin descripción' }}
                            </td>

                            <!-- Precio -->
                            <td class="py-4 px-6 font-bold text-emerald-600 font-mono text-sm">
                                ${{ number_format($p->precio, 0, ',', '.') }}
                            </td>

                            <!-- Disponibilidad -->
                            <td class="py-4 px-6">
                                @if($p->estado)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">
                                        <i class="fas fa-circle-check text-[11px]"></i>
                                        Visible
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        <i class="fas fa-circle-xmark text-[11px]"></i>
                                        Oculto
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Botón Editar -->
                                    <button 
                                        type="button"
                                        onclick='openEditMenuModal(@json($p))'
                                        class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition"
                                        title="Editar plato"
                                    >
                                        <i class="fas fa-pen text-xs"></i>
                                    </button>

                                    <!-- Botón Cambiar Estado (Ocultar / Mostrar) -->
                                    <form method="POST" action="{{ route('admin.menu.toggle-status', $p->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit"
                                            class="w-8 h-8 rounded-full {{ $p->estado ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} flex items-center justify-center transition"
                                            title="{{ $p->estado ? 'Ocultar plato del catálogo' : 'Hacer visible el plato en el catálogo' }}"
                                        >
                                            <i class="fas {{ $p->estado ? 'fa-eye-slash' : 'fa-eye' }} text-xs"></i>
                                        </button>
                                    </form>

                                    <!-- Botón Eliminar -->
                                    <form method="POST" action="{{ route('admin.menu.destroy', $p->id) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este plato del menú?')">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit"
                                            class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition"
                                            title="Eliminar plato"
                                        >
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <i class="fas fa-utensils text-4xl mb-3 text-gray-300 block"></i>
                                No se encontraron platos en el menú con los criterios seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PIE DE TABLA Y PAGINACIÓN -->
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-500 font-medium">
                Mostrando <span class="font-bold text-gray-900">{{ $productos->count() }}</span> de <span class="font-bold text-gray-900">{{ $productos->total() }}</span> platos
            </p>
            @if($productos->hasPages())
                <div class="pagination-custom">
                    {{ $productos->links() }}
                </div>
            @endif
        </div>
    </div>

</div>


<!-- ================================================= -->
<!-- MODAL: AGREGAR PLATO AL MENÚ -->
<!-- ================================================= -->
<div id="createMenuModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border-2 border-black max-w-lg w-full p-7 shadow-2xl space-y-6 relative animate-fade-in">
        
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#2563eb] text-white flex items-center justify-center">
                    <i class="fas fa-plus text-sm"></i>
                </div>
                <h3 class="font-heading text-xl font-bold uppercase tracking-wider text-gray-900">Agregar Plato al Menú</h3>
            </div>
            <button onclick="closeCreateMenuModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600">Nombre del Plato / Bebida</label>
                <input type="text" name="nombre" required placeholder="Ej. Carne Bistec" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Categoría</label>
                    <select name="categoria_producto_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="">Seleccionar categoría</option>
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Precio ($)</label>
                    <input type="number" step="100" min="0" name="precio" required placeholder="30000" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black font-mono">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold uppercase text-gray-600">Descripción</label>
                <textarea name="descripcion" rows="2" placeholder="Ej. Deliciosa porción de carne bistec con especias..." class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">URL Imagen (Opcional)</label>
                    <input type="url" name="imagen_url" placeholder="https://..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase text-gray-600">Disponibilidad</label>
                    <select name="estado" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="1" selected>Visible en catálogo</option>
                        <option value="0">Oculto</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeCreateMenuModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#2563eb] text-white hover:bg-blue-700 transition shadow">
                    Guardar Plato
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ================================================= -->
<!-- MODAL: EDITAR PLATO DEL MENÚ -->
<!-- ================================================= -->
<div id="editMenuModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#f0f2f5] rounded-2xl w-full max-w-md shadow-2xl relative overflow-hidden" style="max-height:95vh;overflow-y:auto;">

        <!-- CABECERA -->
        <div class="flex items-center justify-between px-6 pt-6 pb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-pen text-gray-800 text-lg"></i>
                <h3 class="text-xl font-bold text-gray-900">Editar Plato</h3>
            </div>
            <button
                onclick="closeEditMenuModal()"
                class="text-gray-400 hover:text-gray-700 transition text-lg leading-none"
                title="Cerrar"
            >&times;</button>
        </div>

        <!-- FORMULARIO -->
        <form id="editMenuForm" method="POST" enctype="multipart/form-data" class="px-6 pb-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre del Plato o Bebida</label>
                <input
                    type="text"
                    id="edit_nombre"
                    name="nombre"
                    required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                >
            </div>

            <!-- Categoría + Precio -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Categoría</label>
                    <select
                        id="edit_categoria_id"
                        name="categoria_producto_id"
                        required
                        class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                    >
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Precio de Venta ($)</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        id="edit_precio"
                        name="precio"
                        required
                        class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm font-mono"
                    >
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción <span class="font-normal text-gray-400">(Visible para el cliente)</span></label>
                <textarea
                    id="edit_descripcion"
                    name="descripcion"
                    rows="3"
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm resize-y"
                ></textarea>
            </div>

            <!-- Imagen del Plato -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Imagen del Plato</label>
                <div class="flex items-center gap-3">

                    <!-- Preview de imagen actual -->
                    <div id="edit_img_preview_wrap" class="w-16 h-16 rounded-xl bg-gray-200 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                        <img id="edit_img_preview" src="" alt="" class="w-full h-full object-cover hidden">
                        <i id="edit_img_icon" class="fas fa-utensils text-gray-400 text-xl"></i>
                    </div>

                    <!-- Zona de carga -->
                    <label
                        for="edit_imagen_file"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border-2 border-dashed border-blue-300 rounded-xl cursor-pointer bg-white hover:bg-blue-50 transition text-blue-500 text-sm font-medium"
                    >
                        <i class="fas fa-upload text-base"></i>
                        <span>Cambiar imagen <span class="text-gray-400 font-normal">(dejar vacío para mantener la actual)</span></span>
                        <input type="file" id="edit_imagen_file" name="imagen" accept="image/*" class="hidden" onchange="previewEditImage(event)">
                    </label>

                </div>
            </div>

            <!-- Disponibilidad (checkbox) -->
            <div class="flex items-center gap-3 pt-1">
                <input
                    type="checkbox"
                    id="edit_estado_check"
                    name="estado"
                    value="1"
                    class="w-5 h-5 rounded accent-blue-500 cursor-pointer"
                >
                <label for="edit_estado_check" class="text-sm font-semibold text-gray-700 cursor-pointer">
                    Mostrar en Catálogo
                    <span id="edit_disponibilidad_label" class="font-normal text-gray-500">(Disponible)</span>
                </label>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200 mt-2">
                <button
                    type="button"
                    onclick="closeEditMenuModal()"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#2563eb] text-white hover:bg-blue-700 transition shadow"
                >
                    <i class="fas fa-floppy-disk text-sm"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openCreateMenuModal() {
        document.getElementById('createMenuModal').classList.remove('hidden');
    }

    function closeCreateMenuModal() {
        document.getElementById('createMenuModal').classList.add('hidden');
    }

    function openEditMenuModal(producto) {
        const form = document.getElementById('editMenuForm');
        form.action = `/admin/menu/${producto.id}`;

        document.getElementById('edit_nombre').value         = producto.nombre || '';
        document.getElementById('edit_categoria_id').value   = producto.categoria_producto_id || '';
        document.getElementById('edit_precio').value         = producto.precio || '';
        document.getElementById('edit_descripcion').value    = producto.descripcion || '';

        // Estado => checkbox
        const check = document.getElementById('edit_estado_check');
        const label = document.getElementById('edit_disponibilidad_label');
        const activo = (producto.estado == true || producto.estado == 1);
        check.checked = activo;
        label.textContent = activo ? '(Disponible)' : '(Oculto)';
        check.addEventListener('change', () => {
            label.textContent = check.checked ? '(Disponible)' : '(Oculto)';
        });

        // Preview de imagen actual
        const img    = document.getElementById('edit_img_preview');
        const icon   = document.getElementById('edit_img_icon');
        if (producto.imagen) {
            img.src = producto.imagen;
            img.classList.remove('hidden');
            icon.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            img.src = '';
            icon.classList.remove('hidden');
        }

        document.getElementById('edit_imagen_file').value = '';
        document.getElementById('editMenuModal').classList.remove('hidden');
    }

    function closeEditMenuModal() {
        document.getElementById('editMenuModal').classList.add('hidden');
    }

    function previewEditImage(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const img  = document.getElementById('edit_img_preview');
            const icon = document.getElementById('edit_img_icon');
            img.src = e.target.result;
            img.classList.remove('hidden');
            icon.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }

    // Cerrar modal al hacer clic en el fondo
    document.getElementById('editMenuModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditMenuModal();
    });
    document.getElementById('createMenuModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateMenuModal();
    });
</script>
@endsection
