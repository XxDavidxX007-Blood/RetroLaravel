<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Retro Restaurant')</title>


    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>


    {{-- Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    >


    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600&display=swap"
        rel="stylesheet"
    >


    {{-- Configuración Tailwind --}}
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


    {{-- Estilos --}}
    <style>

        body {
            font-family: 'Montserrat', sans-serif;
        }


        .font-heading {
            font-family: 'Playfair Display', serif;
        }


        .elegant-text {
            letter-spacing: 0.15em;
        }


        /* =========================
           SIDEBAR
        ========================= */

        .sidebar-link {
            transition: all 0.3s ease;
        }


        .sidebar-link:hover {
            background: #1a1a1a;
            color: #c5a059;
        }


        .sidebar-link.active {
            background: #1a1a1a;
            color: #c5a059;
            border-left: 3px solid #c5a059;
        }


    </style>


    @stack('styles')

</head>


<body class="bg-[#f5f5f5] text-gray-800">


    {{-- ========================================================= --}}
    {{-- SIDEBAR --}}
    {{-- ========================================================= --}}

    <aside
        id="clientSidebar"
        class="fixed left-0 top-0 h-screen w-64 bg-retro-dark text-white flex flex-col transition-all duration-300 z-40"
    >

        <!-- BOTÓN CIRCULAR FLOTANTE DE COLAPSO -->
        <button
            type="button"
            id="clientSidebarToggleBtn"
            onclick="toggleClientSidebar()"
            class="absolute -right-3.5 top-8 w-7 h-7 rounded-full bg-retro-gold text-retro-dark flex items-center justify-center shadow-lg hover:scale-110 active:scale-95 transition-all z-50 cursor-pointer border border-black/10 focus:outline-none"
            title="Colapsar / Expandir menú"
        >
            <i id="clientSidebarToggleIcon" class="fas fa-chevron-left text-[10px]"></i>
        </button>

        {{-- ========================= --}}
        {{-- LOGO --}}
        {{-- ========================= --}}

        <div class="p-8 border-b border-gray-800 flex items-center gap-3 overflow-hidden">

            <i class="fas fa-wine-glass text-retro-gold text-2xl shrink-0"></i>

            <div class="client-sidebar-text transition-all duration-300 whitespace-nowrap">

                <h1 class="font-heading text-xl tracking-widest leading-none">
                    RETRO
                </h1>

                <p class="text-retro-gold text-xs tracking-[0.3em] mt-1">
                    RESTAURANT
                </p>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- MENÚ --}}
        {{-- ========================================================= --}}

        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">


            {{-- ========================= --}}
            {{-- DASHBOARD --}}
            {{-- ========================= --}}

            <a
                href="{{ route('dashboard') }}"
                class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-lg mb-2
                {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                title="Dashboard"
            >

                <i class="fas fa-compass w-5 text-center shrink-0"></i>

                <span class="client-sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                    DASHBOARD
                </span>

            </a>



            {{-- ========================= --}}
            {{-- CATÁLOGO --}}
            {{-- ========================= --}}

            <a
                href="{{ route('productos.index') }}"
                class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-lg mb-2
                {{ request()->routeIs('productos.*') ? 'active' : '' }}"
                title="Catálogo"
            >

                <i class="fas fa-utensils w-5 text-center shrink-0"></i>

                <span class="client-sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                    CATÁLOGO
                </span>

            </a>



            {{-- ========================= --}}
            {{-- MIS RESERVAS --}}
            {{-- ========================= --}}

            <a
                href="{{ route('reservas.cliente') }}"
                class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-lg mb-2
                {{ request()->routeIs('reservas.cliente') ? 'active' : '' }}"
                title="Mis Reservas"
            >

                <i class="fas fa-calendar-check w-5 text-center shrink-0"></i>

                <span class="client-sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                    MIS RESERVAS
                </span>

            </a>



            {{-- ========================= --}}
            {{-- MIS PEDIDOS --}}
            {{-- ========================= --}}

            <a
                href="{{ route('pedidos.cliente') }}"
                class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-lg mb-2
                {{ request()->routeIs('pedidos.cliente') ? 'active' : '' }}"
                title="Mis Pedidos"
            >

                <i class="fas fa-receipt w-5 text-center shrink-0"></i>

                <span class="client-sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                    MIS PEDIDOS
                </span>

            </a>



            {{-- ========================= --}}
            {{-- DOMICILIOS --}}
            {{-- ========================= --}}

            <a
                href="{{ route('domicilios.cliente') }}"
                class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-lg mb-2
                {{ request()->routeIs('domicilios.cliente') ? 'active' : '' }}"
                title="Domicilios"
            >

                <i class="fas fa-motorcycle w-5 text-center shrink-0"></i>

                <span class="client-sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                    DOMICILIOS
                </span>

            </a>



            {{-- ========================= --}}
            {{-- PERFIL --}}
            {{-- ========================= --}}

            <a
                href="{{ route('perfil') }}"
                class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-lg mb-2
                {{ request()->routeIs('perfil*') ? 'active' : '' }}"
                title="Perfil"
            >

                <i class="fas fa-user w-5 text-center shrink-0"></i>

                <span class="client-sidebar-text whitespace-nowrap text-xs font-semibold tracking-wider">
                    PERFIL
                </span>

            </a>


        </nav>



        {{-- ========================================================= --}}
        {{-- USUARIO --}}
        {{-- ========================================================= --}}

        <div class="p-4 border-t border-gray-800 overflow-hidden">


            {{-- Información del usuario --}}

            <div class="flex items-center gap-3 px-3 py-3">


                {{-- Avatar --}}

                <div
                    class="w-10 h-10 rounded-full bg-retro-gold flex items-center justify-center flex-shrink-0"
                >

                    <i class="fas fa-user text-white"></i>

                </div>


                {{-- Nombre y rol --}}

                <div class="flex-1 min-w-0 client-sidebar-text whitespace-nowrap">

                    <p class="text-sm text-white truncate">

                        {{ auth()->user()->name ?? 'Usuario' }}

                    </p>


                    <p class="text-xs text-gray-500 truncate">

                        {{ auth()->user()->role->nombre ?? 'Usuario' }}

                    </p>

                </div>


            </div>



            {{-- ========================= --}}
            {{-- CERRAR SESIÓN --}}
            {{-- ========================= --}}

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

                    <span class="client-sidebar-text whitespace-nowrap ml-3 text-xs font-semibold tracking-wider">
                        Cerrar sesión
                    </span>

                </button>

            </form>


        </div>


    </aside>



    {{-- ========================================================= --}}
    {{-- CONTENIDO PRINCIPAL --}}
    {{-- ========================================================= --}}

    <main id="clientMainContent" class="ml-64 min-h-screen transition-all duration-300">


        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <header class="bg-white border-b border-gray-200 px-8 py-5">

            <div class="flex justify-between items-center">


                {{-- Título --}}

                <div>

                    <p class="text-xs text-retro-gold uppercase tracking-widest">

                        Retro Restaurant

                    </p>


                    <h2 class="font-heading text-2xl text-gray-900">

                        @yield('header', 'Dashboard')

                    </h2>

                </div>



                {{-- ========================= --}}
                {{-- Usuario / Notificaciones --}}
                {{-- ========================= --}}

                <div class="flex items-center gap-5">


                    {{-- Notificaciones --}}

                    <a
                        href="{{ route('notificaciones.index') }}"
                        class="text-gray-500 hover:text-retro-gold transition"
                        title="Notificaciones"
                    >

                        <i class="fas fa-bell"></i>

                    </a>


                    {{-- Separador --}}

                    <div class="h-8 w-px bg-gray-200"></div>


                    {{-- Nombre --}}

                    <a href="{{ route('perfil') }}" class="text-sm text-gray-600 hover:text-retro-gold transition font-medium">

                        {{ auth()->user()->name ?? 'Usuario' }}

                    </a>


                </div>


            </div>

        </header>



        {{-- ========================================================= --}}
        {{-- CONTENIDO DE CADA PÁGINA --}}
        {{-- ========================================================= --}}

        <section class="p-8">

            @yield('content')

        </section>


    </main>



    {{-- Scripts adicionales --}}

    @stack('scripts')

    <script>
        function toggleClientSidebar() {
            const sidebar = document.getElementById('clientSidebar');
            const main = document.getElementById('clientMainContent');
            const icon = document.getElementById('clientSidebarToggleIcon');
            const texts = document.querySelectorAll('.client-sidebar-text');

            if (!sidebar || !main) return;

            const isCollapsed = sidebar.classList.contains('w-20');

            if (isCollapsed) {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                main.classList.remove('ml-20');
                main.classList.add('ml-64');
                if (icon) {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-left');
                }
                texts.forEach(t => t.classList.remove('hidden'));
                localStorage.setItem('client_sidebar_collapsed', 'false');
            } else {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                main.classList.remove('ml-64');
                main.classList.add('ml-20');
                if (icon) {
                    icon.classList.remove('fa-chevron-left');
                    icon.classList.add('fa-chevron-right');
                }
                texts.forEach(t => t.classList.add('hidden'));
                localStorage.setItem('client_sidebar_collapsed', 'true');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('client_sidebar_collapsed') === 'true') {
                const sidebar = document.getElementById('clientSidebar');
                const main = document.getElementById('clientMainContent');
                const icon = document.getElementById('clientSidebarToggleIcon');
                const texts = document.querySelectorAll('.client-sidebar-text');

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