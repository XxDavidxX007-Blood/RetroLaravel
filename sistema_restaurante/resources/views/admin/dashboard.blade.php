@extends('layouts.admin')

@section('title', 'Dashboard | Administrador')

@section('header', 'DASHBOARD')


@section('content')

<div class="max-w-7xl mx-auto">


    <!-- DESCRIPCIÓN -->

    <div class="flex justify-between items-center mb-6">

        <div>

            <p class="text-gray-500">
                Resumen general de tu negocio
            </p>

        </div>


        <div class="bg-gray-100 px-4 py-2 rounded-lg text-sm text-gray-600">

            <i class="fas fa-calendar mr-2"></i>

            {{ now()->format('d \d\e F, Y') }}

        </div>

    </div>


    <!-- ================================================= -->
    <!-- TARJETAS -->
    <!-- ================================================= -->

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">


        <!-- VENTAS -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">

            <div class="flex items-center gap-4">

                <div class="w-11 h-11 rounded-full bg-black flex items-center justify-center">

                    <i class="fas fa-coins text-retro-gold"></i>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Ventas del día
                    </p>

                    <h3 class="text-xl font-semibold">
                        $0
                    </h3>

                </div>

            </div>

            <p class="text-xs text-green-500 mt-4">
                ▲ 0% vs ayer
            </p>

        </div>


        <!-- PEDIDOS -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">

            <div class="flex items-center gap-4">

                <div class="w-11 h-11 rounded-full bg-black flex items-center justify-center">

                    <i class="fas fa-receipt text-retro-gold"></i>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Pedidos del día
                    </p>

                    <h3 class="text-xl font-semibold">
                        0
                    </h3>

                </div>

            </div>

            <p class="text-xs text-green-500 mt-4">
                ▲ 0% vs ayer
            </p>

        </div>


        <!-- CLIENTES -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">

            <div class="flex items-center gap-4">

                <div class="w-11 h-11 rounded-full bg-black flex items-center justify-center">

                    <i class="fas fa-user text-retro-gold"></i>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Clientes atendidos
                    </p>

                    <h3 class="text-xl font-semibold">
                        0
                    </h3>

                </div>

            </div>

            <p class="text-xs text-green-500 mt-4">
                ▲ 0% vs ayer
            </p>

        </div>


        <!-- PLATOS -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">

            <div class="flex items-center gap-4">

                <div class="w-11 h-11 rounded-full bg-black flex items-center justify-center">

                    <i class="fas fa-utensils text-retro-gold"></i>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Platos más vendidos
                    </p>

                    <h3 class="text-xl font-semibold">
                        1
                    </h3>

                </div>

            </div>

            <p class="text-xs text-gray-400 mt-4">
                Hoy
            </p>

        </div>


        <!-- INVENTARIO -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">

            <div class="flex items-center gap-4">

                <div class="w-11 h-11 rounded-full bg-black flex items-center justify-center">

                    <i class="fas fa-boxes-stacked text-retro-gold"></i>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Productos en inventario
                    </p>

                    <h3 class="text-xl font-semibold">
                        6
                    </h3>

                </div>

            </div>

            <p class="text-xs text-gray-400 mt-4">
                En stock
            </p>

        </div>

    </div>


    <!-- ================================================= -->
    <!-- GRÁFICO + PEDIDOS -->
    <!-- ================================================= -->

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mt-5">


        <!-- GRÁFICO -->

        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">

            <div class="flex justify-between items-center mb-5">

                <h3 class="font-heading text-lg">
                    Ventas de los últimos 7 días
                </h3>

                <span class="bg-gray-100 px-3 py-2 rounded-lg text-xs text-gray-500">
                    Últimos 7 días
                </span>

            </div>

            <div class="h-64">

                <canvas id="ventasChart"></canvas>

            </div>

        </div>


        <!-- PEDIDOS POR ESTADO -->

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

            <h3 class="font-heading text-lg mb-5">
                Pedidos por estado
            </h3>

            <div class="h-52 flex justify-center">

                <canvas id="pedidosChart"></canvas>

            </div>

            <div class="text-sm text-gray-500 mt-4">

                <span class="text-orange-400">
                    ●
                </span>

                Pendiente

                <span class="float-right">
                    1
                </span>

            </div>


            <hr class="my-5">


            <div class="flex justify-between">

                <h3 class="font-heading">
                    Reservas de hoy
                </h3>

                <a href="#" class="text-xs font-semibold">
                    VER TODAS
                </a>

            </div>

            <p class="text-center text-sm text-gray-400 mt-8">
                Sin reservas para hoy
            </p>

        </div>

    </div>


    <!-- ================================================= -->
    <!-- PEDIDOS RECIENTES + PLATOS -->
    <!-- ================================================= -->

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mt-5">


        <!-- PEDIDOS -->

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

            <div class="flex justify-between items-center mb-5">

                <h3 class="font-heading text-lg">
                    Pedidos recientes
                </h3>

                <a href="#" class="text-xs font-semibold">
                    VER TODOS
                </a>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>

                        <tr class="text-xs text-gray-400 uppercase border-b">

                            <th class="text-left py-3">
                                ID
                            </th>

                            <th class="text-left py-3">
                                Cliente
                            </th>

                            <th class="text-left py-3">
                                Mesa/Domicilio
                            </th>

                            <th class="text-left py-3">
                                Total
                            </th>

                            <th class="text-left py-3">
                                Estado
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr>

                            <td class="py-4 font-semibold">
                                #19
                            </td>

                            <td>
                                hades
                            </td>

                            <td>
                                mesa
                            </td>

                            <td class="font-semibold">
                                $10,000
                            </td>

                            <td>

                                <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-xs">
                                    Pendiente
                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- PLATOS MÁS VENDIDOS -->

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

            <div class="flex justify-between items-center mb-5">

                <h3 class="font-heading text-lg">
                    Platos más vendidos
                </h3>

                <a href="#" class="text-xs font-semibold">
                    VER REPORTE
                </a>

            </div>


            <table class="w-full text-sm">

                <thead>

                    <tr class="text-xs text-gray-400 uppercase border-b">

                        <th class="text-left py-3">
                            Plato
                        </th>

                        <th class="text-left">
                            Categoría
                        </th>

                        <th class="text-left">
                            Vendidos
                        </th>

                        <th class="text-left">
                            Ingresos
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr>

                        <td class="py-4 font-semibold">
                            🍽️ hamburguesa
                        </td>

                        <td>
                            Hamburguesas
                        </td>

                        <td>
                            1
                        </td>

                        <td class="font-semibold">
                            $10,000
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- ================================================= -->
    <!-- DOMICILIOS -->
    <!-- ================================================= -->

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mt-5">

        <div class="flex justify-between items-center mb-5">

            <h3 class="font-heading text-lg">

                <i class="fas fa-motorcycle text-retro-gold mr-2"></i>

                Domicilios recientes

                <span class="bg-black text-white text-[10px] px-2 py-1 rounded-full ml-2">
                    0 HOY
                </span>

            </h3>

            <a href="#" class="text-xs font-semibold">
                VER TODOS
            </a>

        </div>


        <table class="w-full text-sm">

            <thead>

                <tr class="text-xs text-gray-400 uppercase border-b">

                    <th class="text-left py-3">
                        ID
                    </th>

                    <th class="text-left">
                        Cliente
                    </th>

                    <th class="text-left">
                        Teléfono
                    </th>

                    <th class="text-left">
                        Fecha
                    </th>

                    <th class="text-left">
                        Total
                    </th>

                    <th class="text-left">
                        Estado
                    </th>

                </tr>

            </thead>

        </table>


        <div class="text-center py-8 text-sm text-gray-400">

            Sin domicilios registrados —

            <a href="#" class="text-red-500">
                Registrar uno
            </a>

        </div>

    </div>


</div>


@endsection


@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // ============================================
    // GRÁFICO DE VENTAS
    // ============================================

    const ventas = document.getElementById('ventasChart');

    new Chart(ventas, {

        type: 'line',

        data: {

            labels: [
                '10 Ago',
                '11 Ago',
                '12 Ago',
                '13 Ago',
                '14 Ago',
                '15 Ago',
                '16 Ago'
            ],

            datasets: [{

                label: 'Ventas',

                data: [
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0
                ],

                borderColor: '#c5a059',

                backgroundColor: 'rgba(197,160,89,0.10)',

                tension: 0.3,

                fill: true,

                pointRadius: 4

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {
                    beginAtZero: true
                }

            }

        }

    });


    // ============================================
    // PEDIDOS POR ESTADO
    // ============================================

    const pedidos = document.getElementById('pedidosChart');

    new Chart(pedidos, {

        type: 'doughnut',

        data: {

            labels: [
                'Pendiente'
            ],

            datasets: [{

                data: [
                    1
                ],

                backgroundColor: [
                    '#f59e0b'
                ],

                borderWidth: 0

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            cutout: '65%',

            plugins: {

                legend: {
                    display: false
                }

            }

        }

    });

</script>

@endsection