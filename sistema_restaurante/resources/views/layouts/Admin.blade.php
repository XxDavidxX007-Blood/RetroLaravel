<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Administrador | Retro Restaurant')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap"
        rel="stylesheet"
    >

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        retro: {
                            gold: '#c5a059',
                            goldlight: '#d4b773',
                            dark: '#0a0a0a',
                            gray: '#1a1a1a',
                            subtle: '#f5f5f5'
                        }
                    },

                    fontFamily: {
                        heading: ['Playfair Display', 'serif'],
                        body: ['Montserrat', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>

        body {
            font-family: 'Montserrat', sans-serif;
        }

        .font-heading {
            font-family: 'Playfair Display', serif;
        }

        .sidebar-link {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            background: #1a1a1a;
            color: #c5a059;
        }

        .sidebar-link.active {
            background: #c5a059;
            color: #0a0a0a;
        }

    </style>

</head>


<body class="bg-[#f8f8f8] text-gray-800">


<!-- ===================================================== -->
<!-- SIDEBAR ADMINISTRADOR -->
<!-- ===================================================== -->

<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-[#0a0a0a] text-white flex flex-col transition-all duration-300 z-40">

    <!-- BOTÓN CIRCULAR FLOTANTE DE COLAPSO (IDÉNTICO A LA IMAGEN) -->
    <button
        type="button"
        id="sidebarToggleBtn"
        onclick="toggleSidebar()"
        class="absolute -right-3.5 top-8 w-7 h-7 rounded-full bg-retro-gold text-[#0a0a0a] flex items-center justify-center shadow-lg hover:scale-110 active:scale-95 transition-all z-50 cursor-pointer border border-black/10 focus:outline-none"
        title="Colapsar / Expandir menú"
    >
        <i id="sidebarToggleIcon" class="fas fa-chevron-left text-[10px]"></i>
    </button>

    <!-- LOGO -->

    <div class="p-7 border-b border-gray-800 flex items-center gap-3 overflow-hidden">

        <i class="fas fa-wine-glass text-retro-gold text-2xl shrink-0"></i>

        <div class="sidebar-text transition-all duration-300 whitespace-nowrap">

            <h1 class="font-heading text-xl tracking-widest leading-none">
                RETRO
            </h1>

            <p class="text-retro-gold text-xs tracking-[0.3em] mt-1">
                MENÚ
            </p>

        </div>

    </div>


    <!-- MENU -->

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">

        <a
            href="{{ route('dashboard') }}"
            class="sidebar-link flex items-center gap-4 px-4 py-3 rounded {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            title="Dashboard"
        >

            <i class="fas fa-compass w-5 text-center shrink-0"></i>

            <span class="sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                DASHBOARD
            </span>

        </a>


        <a
            href="{{ route('admin.usuarios.index') }}"
            class="sidebar-link flex items-center gap-4 px-4 py-3 rounded {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"
            title="Gestión de Usuarios"
        >

            <i class="fas fa-users w-5 text-center shrink-0"></i>

            <span class="sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                GESTIÓN DE USUARIOS
            </span>

        </a>


        <a
            href="{{ route('admin.inventario.index') }}"
            class="sidebar-link flex items-center gap-4 px-4 py-3 rounded {{ request()->routeIs('admin.inventario.*') ? 'active' : '' }}"
            title="Inventario"
        >

            <i class="fas fa-boxes-stacked w-5 text-center shrink-0"></i>

            <span class="sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                INVENTARIO
            </span>

        </a>


        <a
            href="{{ route('admin.menu.index') }}"
            class="sidebar-link flex items-center gap-4 px-4 py-3 rounded {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}"
            title="Menú"
        >

            <i class="fas fa-utensils w-5 text-center shrink-0"></i>

            <span class="sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                MENÚ
            </span>

        </a>


        <a
            href="{{ route('admin.pedidos.index') }}"
            class="sidebar-link flex items-center gap-4 px-4 py-3 rounded {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}"
            title="Pedidos"
        >

            <i class="fas fa-receipt w-5 text-center shrink-0"></i>

            <span class="sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                PEDIDOS
            </span>

        </a>


        <a
            href="{{ route('admin.reportes.index') }}"
            class="sidebar-link flex items-center gap-4 px-4 py-3 rounded {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}"
            title="Reportes"
        >

            <i class="fas fa-chart-pie w-5 text-center shrink-0"></i>

            <span class="sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                REPORTES
            </span>

        </a>


        <a
            href="{{ route('admin.reservas.index') }}"
            class="sidebar-link flex items-center gap-4 px-4 py-3 rounded {{ request()->routeIs('admin.reservas.*') ? 'active' : '' }}"
            title="Reservas"
        >

            <i class="fas fa-calendar-check w-5 text-center shrink-0"></i>

            <span class="sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                RESERVAS
            </span>

        </a>


        <a
            href="{{ route('admin.domicilios.index') }}"
            class="sidebar-link flex items-center gap-4 px-4 py-3 rounded {{ request()->routeIs('admin.domicilios.*') ? 'active' : '' }}"
            title="Domicilios"
        >

            <i class="fas fa-motorcycle w-5 text-center shrink-0"></i>

            <span class="sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                DOMICILIOS
            </span>

        </a>

    </nav>


    <!-- USUARIO -->

    <div class="p-4 border-t border-gray-800 overflow-hidden">

        <div class="flex items-center gap-3 px-3 py-3">

            <div class="w-10 h-10 rounded-full bg-retro-gold flex items-center justify-center shrink-0">

                <i class="fas fa-user text-white"></i>

            </div>


            <div class="flex-1 sidebar-text whitespace-nowrap">

                <p class="text-sm text-white">

                    {{ auth()->user()->name ?? 'Administrador' }}

                </p>

                <p class="text-xs text-gray-500">

                    Administrador

                </p>

            </div>

        </div>


        <form
            action="{{ route('logout') }}"
            method="POST"
            class="mt-2"
        >

            @csrf

            <button
                type="submit"
                class="w-full flex items-center px-4 py-2 text-gray-400 hover:text-retro-gold transition"
                title="Cerrar sesión"
            >

                <i class="fas fa-sign-out-alt w-5 text-center shrink-0"></i>

                <span class="sidebar-text whitespace-nowrap ml-3 text-xs font-semibold tracking-wider">
                    CERRAR SESIÓN
                </span>

            </button>

        </form>

    </div>

</aside>


<!-- ===================================================== -->
<!-- CONTENIDO -->
<!-- ===================================================== -->

<main id="mainContent" class="ml-64 min-h-screen transition-all duration-300">


    <!-- HEADER -->

    <header class="bg-white border-b border-gray-200 px-8 py-5">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-xs text-retro-gold uppercase tracking-widest">
                    Retro Restaurant
                </p>

                <h2 class="font-heading text-2xl text-gray-900">

                    @yield('header', 'DASHBOARD')

                </h2>

            </div>


            <div class="flex items-center gap-5">

                <!-- NOTIFICACIONES DROPDOWN -->
                <div class="relative">
                    <button
                        type="button"
                        id="btnNotificaciones"
                        onclick="toggleDropdownNotificaciones()"
                        class="w-10 h-10 rounded-2xl bg-gray-100 text-gray-700 flex items-center justify-center hover:bg-gray-200 transition relative"
                        title="Notificaciones"
                    >
                        <i class="fas fa-bell"></i>
                        <span id="badgeNotificaciones" class="absolute 1 top-1.5 right-1.5 w-2.5 h-2.5 bg-retro-gold rounded-full ring-2 ring-white hidden"></span>
                    </button>

                    <!-- POPOVER NOTIFICACIONES -->
                    <div
                        id="popoverNotificaciones"
                        class="hidden absolute right-0 top-13 z-50 w-88 sm:w-96 max-w-[90vw] bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden"
                    >
                        <!-- CABECERA -->
                        <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-[#0a0a0a] flex items-center justify-center text-retro-gold text-base shadow-sm">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-gray-900 text-base leading-tight">
                                        Notificaciones
                                    </h3>
                                    <p id="subtituloNotificaciones" class="text-xs text-gray-400 font-normal">
                                        Todo al día
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                onclick="toggleDropdownNotificaciones()"
                                class="w-7 h-7 rounded-xl bg-gray-100 text-gray-400 hover:text-gray-700 hover:bg-gray-200 flex items-center justify-center transition"
                            >
                                <i class="fas fa-xmark text-xs"></i>
                            </button>
                        </div>

                        <!-- CUERPO -->
                        <div id="cuerpoNotificaciones" class="max-h-80 overflow-y-auto">
                            <!-- ESTADO VACÍO (DEFAULT) -->
                            <div id="notifEmptyState" class="py-12 px-6 text-center">
                                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3 text-gray-300 text-xl">
                                    <i class="fas fa-bell-slash"></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-800 mb-1">
                                    Sin notificaciones
                                </h4>
                                <p class="text-xs text-gray-400">
                                    Aquí aparecerán tus alertas y novedades
                                </p>
                            </div>

                            <!-- LISTA DINÁMICA DE NOTIFICACIONES -->
                            <div id="notifListaItems" class="divide-y divide-gray-100 hidden">
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="border-t border-gray-100 p-4 text-center bg-gray-50/50">
                            <a
                                href="{{ route('notificaciones.index') }}"
                                class="text-xs font-bold uppercase tracking-wider text-gray-900 hover:text-retro-gold transition inline-flex items-center gap-1.5"
                            >
                                <span>VER TODAS LAS NOTIFICACIONES</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>


                <a href="{{ route('perfil') }}" class="flex items-center gap-3 hover:opacity-80 transition group" title="Ver mi perfil">
                    <div class="text-right">
                        <p class="text-sm font-semibold uppercase group-hover:text-retro-gold transition">
                            {{ auth()->user()->role->nombre ?? 'Administrador' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ auth()->user()->name ?? 'Administrador' }}
                        </p>
                    </div>

                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                        @if(auth()->user() && auth()->user()->foto)
                            <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user text-gray-600"></i>
                        @endif
                    </div>
                </a>

            </div>

        </div>

    </header>


    <!-- CONTENIDO -->

    <section class="p-8">

        @yield('content')

    </section>


</main>


@stack('scripts')
@yield('scripts')

<script>
    function toggleDropdownNotificaciones() {
        const popover = document.getElementById('popoverNotificaciones');
        if (popover.classList.contains('hidden')) {
            popover.classList.remove('hidden');
            cargarNotificaciones();
        } else {
            popover.classList.add('hidden');
        }
    }

    // Cerrar popover al hacer clic afuera
    document.addEventListener('click', function(e) {
        const popover = document.getElementById('popoverNotificaciones');
        const btn = document.getElementById('btnNotificaciones');
        if (popover && btn && !popover.contains(e.target) && !btn.contains(e.target)) {
            popover.classList.add('hidden');
        }
    });

    function cargarNotificaciones() {
        fetch('{{ route("notificaciones.latest") }}')
            .then(res => res.json())
            .then(data => {
                const empty = document.getElementById('notifEmptyState');
                const list = document.getElementById('notifListaItems');
                const sub = document.getElementById('subtituloNotificaciones');
                const badge = document.getElementById('badgeNotificaciones');

                if (data.totalNoLeidas > 0) {
                    badge.classList.remove('hidden');
                    sub.innerText = `${data.totalNoLeidas} no leída(s)`;
                } else {
                    badge.classList.add('hidden');
                    sub.innerText = 'Todo al día';
                }

                if (data.notificaciones && data.notificaciones.length > 0) {
                    empty.classList.add('hidden');
                    list.classList.remove('hidden');

                    let html = '';
                    data.notificaciones.forEach(n => {
                        let icon = 'fa-bell';
                        let iconColor = 'bg-gray-100 text-gray-700';
                        const tipo = (n.tipo || '').toLowerCase();

                        if (tipo.includes('domicilio')) {
                            icon = 'fa-motorcycle';
                            iconColor = 'bg-blue-50 text-blue-600';
                        } else if (tipo.includes('pedido')) {
                            icon = 'fa-receipt';
                            iconColor = 'bg-amber-50 text-amber-600';
                        } else if (tipo.includes('reserva')) {
                            icon = 'fa-calendar-check';
                            iconColor = 'bg-emerald-50 text-emerald-600';
                        } else if (tipo.includes('stock') || tipo.includes('inventario')) {
                            icon = 'fa-box-open';
                            iconColor = 'bg-rose-50 text-rose-600';
                        }

                        html += `
                            <a href="${n.url}" onclick="marcarLeidaDropdown(${n.id})" class="p-4 flex items-start gap-3.5 hover:bg-gray-50 transition block ${!n.leida ? 'bg-amber-50/20' : ''}">
                                <div class="w-9 h-9 rounded-xl ${iconColor} flex items-center justify-center shrink-0 text-sm">
                                    <i class="fas ${icon}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold text-gray-900 truncate">${n.titulo}</p>
                                        ${!n.leida ? '<span class="w-2 h-2 rounded-full bg-retro-gold shrink-0"></span>' : ''}
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-2">${n.mensaje}</p>
                                    <span class="text-[10px] text-gray-400 mt-1 block">${n.fecha}</span>
                                </div>
                            </a>
                        `;
                    });
                    list.innerHTML = html;
                } else {
                    empty.classList.remove('hidden');
                    list.classList.add('hidden');
                }
            })
            .catch(err => console.error(err));
    }

    function marcarLeidaDropdown(id) {
        fetch(`/notificaciones/${id}/leer`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
    }

    // ==========================================
    // COLAPSO / EXPANSIÓN DEL SIDEBAR
    // ==========================================
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('mainContent');
        const icon = document.getElementById('sidebarToggleIcon');
        const texts = document.querySelectorAll('.sidebar-text');

        if (!sidebar || !main) return;

        const isCollapsed = sidebar.classList.contains('w-20');

        if (isCollapsed) {
            // EXPANDIR
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');
            main.classList.remove('ml-20');
            main.classList.add('ml-64');
            if (icon) {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');
            }
            texts.forEach(t => t.classList.remove('hidden'));
            localStorage.setItem('admin_sidebar_collapsed', 'false');
        } else {
            // COLAPSAR
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
            main.classList.remove('ml-64');
            main.classList.add('ml-20');
            if (icon) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
            }
            texts.forEach(t => t.classList.add('hidden'));
            localStorage.setItem('admin_sidebar_collapsed', 'true');
        }
    }

    // Inicializar estado del sidebar desde localStorage
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            const icon = document.getElementById('sidebarToggleIcon');
            const texts = document.querySelectorAll('.sidebar-text');

            if (sidebar && main) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                main.classList.remove('ml-64');
                main.classList.add('ml-20');
                if (icon) {
                    icon.classList.remove('fa-chevron-left');
                    icon.classList.add('fa-chevron-right');
                }
                texts.forEach(t => t.classList.add('hidden'));
            }
        }
    });
</script>

</body>

</html>