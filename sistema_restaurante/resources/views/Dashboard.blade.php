@extends('layouts.app')

@section('title', 'Dashboard | Retro Menú')

@section('header', 'DASHBOARD')

@section('content')

<div class="max-w-[1140px] mx-auto">

    {{-- =====================================================
         BIENVENIDA
    ====================================================== --}}

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-7">

        <div>

            <h1 class="font-heading text-3xl font-bold text-gray-900">
                ¡Bienvenido, {{ auth()->user()->name ?? 'Cliente' }}! 👋
            </h1>

            <p class="text-gray-500 mt-1">
                ¿Qué quieres disfrutar hoy?
            </p>

        </div>


        {{-- BUSCADOR --}}

        <div class="mt-4 lg:mt-0">

            <div class="relative">

                <i
                    class="fas fa-search absolute left-4 top-1/2
                           -translate-y-1/2 text-gray-400 text-sm"
                ></i>

                <input
                    type="text"
                    placeholder="Buscar platos, bebidas..."
                    class="w-[235px] bg-white border border-gray-200
                           rounded-xl py-3 pl-11 pr-4
                           text-sm text-gray-700
                           focus:outline-none
                           focus:border-[#c5a059]"
                >

            </div>

        </div>

    </div>


    {{-- =====================================================
         TARJETAS
    ====================================================== --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 mb-6">


        {{-- RESERVAS --}}

        <div
            class="bg-[#1b2030] rounded-2xl p-5 text-white
                   min-h-[190px]"
        >

            <div
                class="w-10 h-10 rounded-xl bg-[#293247]
                       flex items-center justify-center mb-4"
            >

                <i class="fas fa-calendar-alt text-blue-300"></i>

            </div>


            <p class="text-gray-400 text-sm">
                Mis Reservas
            </p>


            <h2 class="text-3xl font-bold mt-1">
                {{ $totalReservas ?? 0 }}
            </h2>


            <p class="text-gray-400 text-xs mt-1">
                próximas
            </p>


            <a
                href="#"
                class="inline-block text-[#e6bd45]
                       text-sm font-semibold mt-4"
            >

                Ver todas →

            </a>

        </div>


        {{-- PEDIDOS --}}

        <div
            class="bg-[#1b2030] rounded-2xl p-5 text-white
                   min-h-[190px]"
        >

            <div
                class="w-10 h-10 rounded-xl bg-[#293247]
                       flex items-center justify-center mb-4"
            >

                <i class="fas fa-receipt text-gray-300"></i>

            </div>


            <p class="text-gray-400 text-sm">
                Mis Pedidos
            </p>


            <h2 class="text-3xl font-bold mt-1">
                {{ $totalPedidos ?? 0 }}
            </h2>


            <p class="text-gray-400 text-xs mt-1">
                pedidos
            </p>


            <a
                href="#"
                class="inline-block text-[#e6bd45]
                       text-sm font-semibold mt-4"
            >

                Ver historial →

            </a>

        </div>


        {{-- DOMICILIOS --}}

        <div
            class="bg-[#1b2030] rounded-2xl p-5 text-white
                   min-h-[190px]"
        >

            <div
                class="w-10 h-10 rounded-xl bg-[#293247]
                       flex items-center justify-center mb-4"
            >

                <i class="fas fa-motorcycle text-pink-400"></i>

            </div>


            <p class="text-gray-400 text-sm">
                Domicilios
            </p>


            <h2 class="text-3xl font-bold mt-1">
                {{ $totalDomicilios ?? 0 }}
            </h2>


            <p class="text-gray-400 text-xs mt-1">
                Pide a domicilio
            </p>


            <a
                href="#"
                class="inline-block text-[#e6bd45]
                       text-sm font-semibold mt-4"
            >

                Ordenar ahora →

            </a>

        </div>


        {{-- MENÚ --}}

        <div
            class="bg-[#1b2030] rounded-2xl p-5 text-white
                   min-h-[190px]"
        >

            <div
                class="w-10 h-10 rounded-xl bg-[#293247]
                       flex items-center justify-center mb-4"
            >

                <i class="fas fa-utensils text-pink-200"></i>

            </div>


            <p class="text-gray-400 text-sm">
                Menú
            </p>


            <h2 class="text-3xl font-bold mt-1">
                {{ $totalProductos ?? 0 }}+
            </h2>


            <p class="text-gray-400 text-xs mt-1">
                Explorar carta
            </p>


            <a
                href="#"
                class="inline-block text-[#e6bd45]
                       text-sm font-semibold mt-4"
            >

                Ver menú →

            </a>

        </div>

    </div>


    {{-- =====================================================
         MIS RESERVAS
    ====================================================== --}}

    <div
        class="bg-white rounded-2xl border border-gray-200
               mb-6 overflow-hidden"
    >

        {{-- CABECERA --}}

        <div
            class="flex items-center justify-between
                   px-6 py-4 border-b border-gray-100"
        >

            <h2 class="font-heading text-lg font-semibold">

                <span class="mr-2">
                    🗓️
                </span>

                Mis reservas próximas

            </h2>


            <a
                href="#"
                class="text-red-500 text-xs font-semibold"
            >

                Ver todas

            </a>

        </div>


        {{-- CONTENIDO --}}

        <div class="min-h-[115px] flex items-center justify-center">

            @if(($totalReservas ?? 0) == 0)

                <div class="text-center">

                    <p class="text-gray-400 text-sm">
                        No tienes reservas próximas.
                    </p>


                    <a
                        href="#"
                        class="text-[#e6bd45]
                               text-xs font-semibold
                               mt-2 inline-block"
                    >

                        + Nueva reserva

                    </a>

                </div>

            @else

                <p class="text-gray-500 text-sm">

                    Tienes
                    <strong>{{ $totalReservas }}</strong>
                    reserva(s) próxima(s).

                </p>

            @endif

        </div>

    </div>


    {{-- =====================================================
         RETRO PUNTOS
    ====================================================== --}}

    <div
        class="bg-[#293247] rounded-2xl
               p-6 text-white mb-6"
    >

        <div class="flex justify-between items-start">


            <div>

                <p
                    class="text-[#f2c744] text-xs
                           font-bold uppercase tracking-wide"
                >

                    ⭐ RETRO PUNTOS

                </p>


                <div class="flex items-end gap-2 mt-4">

                    <span class="text-4xl font-bold">
                        1,250
                    </span>

                    <span class="text-gray-300 mb-1">
                        pts
                    </span>

                </div>


                <p
                    class="text-[#f2c744]
                           font-semibold text-sm mt-1"
                >

                    Cliente Oro

                </p>

            </div>


            <span class="text-2xl">
                👑
            </span>

        </div>


        {{-- PROGRESO --}}

        <div class="mt-4">

            <div
                class="h-1.5 bg-gray-600
                       rounded-full overflow-hidden"
            >

                <div
                    class="h-full bg-[#e6bd45]"
                    style="width: 65%;"
                ></div>

            </div>


            <p class="text-gray-300 text-xs mt-3">

                Faltan 750 pts para tu próximo descuento

            </p>

        </div>


        {{-- BOTÓN --}}

        <button
            type="button"
            class="w-full border border-[#e6bd45]
                   text-[#e6bd45] rounded-xl
                   py-2 mt-4 text-sm font-semibold
                   hover:bg-[#e6bd45]
                   hover:text-[#1b2030]
                   transition"
        >

            Ver beneficios

        </button>

    </div>


    {{-- =====================================================
         BENEFICIOS
    ====================================================== --}}

    <div
        class="bg-white rounded-2xl
               border border-gray-200 p-5"
    >

        <div
            class="grid grid-cols-1
                   md:grid-cols-4 gap-6"
        >


            {{-- PAGO --}}

            <div class="flex items-center gap-4">

                <div
                    class="text-[#e6bd45]
                           text-2xl w-8 text-center"
                >

                    <i class="fas fa-shield-alt"></i>

                </div>


                <div>

                    <h3 class="font-semibold text-xs">
                        Pago 100% seguro
                    </h3>

                    <p class="text-[10px] text-gray-400">
                        Tus datos están protegidos
                    </p>

                </div>

            </div>


            {{-- ATENCIÓN --}}

            <div class="flex items-center gap-4">

                <div
                    class="text-[#e6bd45]
                           text-2xl w-8 text-center"
                >

                    <i class="fas fa-headset"></i>

                </div>


                <div>

                    <h3 class="font-semibold text-xs">
                        Atención 24/7
                    </h3>

                    <p class="text-[10px] text-gray-400">
                        Siempre para ayudarte
                    </p>

                </div>

            </div>


            {{-- ENVÍOS --}}

            <div class="flex items-center gap-4">

                <div
                    class="text-[#e6bd45]
                           text-2xl w-8 text-center"
                >

                    <i class="fas fa-truck"></i>

                </div>


                <div>

                    <h3 class="font-semibold text-xs">
                        Envíos rápidos
                    </h3>

                    <p class="text-[10px] text-gray-400">
                        Directo a tu puerta
                    </p>

                </div>

            </div>


            {{-- CALIDAD --}}

            <div class="flex items-center gap-4">

                <div
                    class="text-[#e6bd45]
                           text-2xl w-8 text-center"
                >

                    <i class="fas fa-star"></i>

                </div>


                <div>

                    <h3 class="font-semibold text-xs">
                        Calidad garantizada
                    </h3>

                    <p class="text-[10px] text-gray-400">
                        Ingredientes seleccionados
                    </p>

                </div>

            </div>


        </div>

    </div>

</div>

@endsection